<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ProjectController extends AbstractActionController
{

    public function indexAction()
    {
        /** @var \SmgSvn\Svn $svn */
        $svn = $this->getServiceLocator()->get('SvnManager');
        $defaultUsers = $this->getServiceLocator()->get('Config')['smg_svn']['default_users'];
        return new ViewModel(array(
            'projects' => $svn->getProjects(),
            'users' => $svn->getUsers(),
            'defaultUsers' => $defaultUsers,
        ));
    }

    public function usersAction()
    {
        $projectName = $this->params()->fromRoute('projectName', false);
        if (!$projectName) {
            return $this->getResponse()->setStatusCode(404);
        }
        /** @var \SmgSvn\Svn $svn */
        $svn = $this->getServiceLocator()->get('SvnManager');
        $users = $svn->getUsersInProject($projectName);
        $usersToAdd = array_diff($svn->getUsers(), $users);

        return new ViewModel(array(
            'users' => $users,
            'project' => $projectName,
            'usersToAdd' => $usersToAdd,
        ));
    }

    public function removeUserFromProjectAction()
    {
        $backUrl = $this->params()->fromRoute('backUrl');
        $projectName = $this->params()->fromRoute('projectName');
        $userName = $this->params()->fromRoute('userName');
        /** @var \SmgSvn\Svn $svn */
        $svn = $this->getServiceLocator()->get('SvnManager');
        try {
            $svn->removeUserFromProject($projectName, $userName);
            $this->flashMessenger()->addSuccessMessage(sprintf('User %s was removed from %s project.', $userName, $projectName));
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage(sprintf('Cant remove user %s from %s project.', $userName, $projectName));
        }
        return $this->redirect()->toUrl(base64_decode($backUrl));
    }

    public function addUserToProjectAction()
    {
        $backUrl = $this->params()->fromRoute('backUrl');
        $projectName = $this->params()->fromPost('projectName');
        $userName = $this->params()->fromPost('userName');
        $password = $this->params()->fromPost('password');
        /** @var \SmgSvn\Svn $svn */
        $svn = $this->getServiceLocator()->get('SvnManager');
        $svn->addUserToProject($projectName, $userName, $password);
        $this->flashMessenger()->addSuccessMessage(sprintf('User %s was added to %s project.', $userName, $projectName));
        return $this->redirect()->toUrl(base64_decode($backUrl));
    }

    public function createAction()
    {
        $projectName = $this->params()->fromPost('projectName');
        $usersNames = $this->params()->fromPost('usersNames');
        try {
            /** @var \SmgSvn\SvnAdmin $svnAdmin */
            $svnAdmin = $this->getServiceLocator()->get('SvnAdmin');
            $svnAdmin->create($projectName);
            $this->flashMessenger()->addSuccessMessage(sprintf('Project %s was created successfully.', $projectName));
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage(sprintf('Cant create %s project', $projectName));
            return $this->redirect()->toRoute('application/projects');
        }
        /** @var \SmgSvn\Svn $svn */
        $svn = $this->getServiceLocator()->get('SvnManager');
        foreach ($usersNames as $userName) {
            try {
                $svn->addUserToProject($projectName, $userName);
                $this->flashMessenger()->addSuccessMessage(sprintf('User %s was added to %s project.', $userName, $projectName));
            } catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage(sprintf('Cant add user %s to %s project.', $userName, $projectName));
            }
        }
        $svn->setSvnServeConf($projectName, array(
            'general' => array(
                'anon-access' => 'none',
                'auth-access' => 'write',
                'password-db' => 'passwd',
            )
        ));
        $svn->createDirStructure($projectName, ['trunk/src', 'trunk/src - code', 'branches', 'doc']);

        return $this->redirect()->toRoute('application/project_users', array(
            'projectName' => $projectName
        ));
    }

}

