<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController
{

    public function indexAction()
    {
        /** @var \SmgSvn\Svn $svn */
        $svn = $this->getServiceLocator()->get('SvnManager');
        $users = $svn->getUsers();
        return new ViewModel(array(
            'users' => $users,
        ));
    }

    public function projectsAction() {
        $userName = $this->params()->fromRoute('userName', false);
        if (!$userName) {
            return $this->getResponse()->setStatusCode(404);
        }
        /** @var \SmgSvn\Svn $svn */
        $svn = $this->getServiceLocator()->get('SvnManager');
        $projects = $svn->getUserProjects($userName);
        $projectsToAdd = array_diff($svn->getProjects(), $projects);

        return new ViewModel(array(
            'user' => $userName,
            'projects' => $projects,
            'projectsToAdd' => $projectsToAdd,
        ));
    }

    public function removeUserFromAllProjectsAction() {
        $userName = $this->params()->fromRoute('userName', false);
        $backUrl = $this->params()->fromRoute('backUrl', false);
        /** @var \SmgSvn\Svn $svn */
        $svn = $this->getServiceLocator()->get('SvnManager');
        foreach($svn->getProjects() as $projectName) {
            if (in_array($projectName,$svn->getUserProjects($userName))) {
                try {
                    $svn->removeUserFromProject($projectName, $userName);
                    $this->flashMessenger()->addSuccessMessage(sprintf('User %s was removed from %s project.',$userName, $projectName));
                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage(sprintf('Cant remove user %s from %s project.',$userName, $projectName));
                }
            }
        }
        return $this->redirect()->toUrl(base64_decode($backUrl));
    }
}

