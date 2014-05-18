<?php
/**
 * Created by PhpStorm.
 * User: smeagol
 * Date: 17.05.14
 * Time: 14:56
 */

namespace SmgSVN;

use SmgSVN\Exception\UserNotFound;
use SmgSVN\Model\User;
use Zend\Config\Reader\Ini as IniReader;
use Zend\Config\Writer\Ini as IniWriter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Svn extends SvnBase
{

    /**
     * @var \Zend\Config\Reader\Ini
     */
    protected $iniReader;

    /**
     * @var \Zend\Config\Writer\Ini
     */
    protected $iniWriter;

    function __construct()
    {
        $this->iniReader = new IniReader();
        $this->iniWriter = new IniWriter();
    }

    /**
     * Returns list of projects
     * @return array
     */
    public function getProjects()
    {
        $repoPath = $this->getRepoPath();
        $projectList = scandir($repoPath);
        $projectList = array_filter($projectList, function ($value) use ($repoPath) {
            return strpos($value, '.') === 0 || !is_dir($repoPath . DS . $value) ? false : $value;
        });
        return $projectList;
    }

    public function getUsers()
    {
        $usersList = array();
        foreach ($this->getProjects() as $projectName) {
            $usersList = array_merge($usersList, $this->getUsersInProject($projectName));
        }
        return $usersList;
    }

    public function getUsersInProject($projectName)
    {
        $usersList = array();
        $pwdFileName = $this->getProjectPasswdFileName($projectName);
        $iniFile = @$this->getIniReader()->fromFile($pwdFileName);
        foreach ($iniFile['users'] as $user => $pass) {
            $usersList[$user] = new User($user, $pass);
        }
        return $usersList;
    }

    /**
     * Adding user to project, if password is not provided it gets passwd for user from other project
     * @param $projectName
     * @param $userName
     * @param null $password
     * @throws Exception\UserNotFound
     */
    public function addUserToProject($projectName, $userName, $password = null)
    {
        if (!$password) {
            $user = $this->getUser($userName);
        } else {
            $user = new User($userName, $password);
        }
        $passwdFile = $this->getProjectPasswdFileName($projectName);
        $ini = @$this->getIniReader()->fromFile($passwdFile);
        $ini['users'][$user->getName()] = $user->getPassword();
        $this->getIniWriter()->toFile($passwdFile, $ini);
    }

    public function removeUserFromProject($projectName, $userName)
    {
        $passwdFile = $this->getProjectPasswdFileName($projectName);
        $ini = @$this->getIniReader()->fromFile($passwdFile);
        unset($ini['users'][$userName]);
        $this->getIniWriter()->toFile($passwdFile, $ini);
    }

    /**
     * @param $userName
     * @return User
     * @throws Exception\UserNotFound
     */
    public function getUser($userName)
    {
        $users = $this->getUsers();
        if (!isset($users[$userName])) {
            throw new UserNotFound(sprintf('User \'%s\' was not found.', $userName));
        }

        return $users[$userName];
    }

    /**
     * @param $userName
     * @return array
     */
    public function getUserProjects($userName)
    {
        $projectList = array();
        foreach ($this->getProjects() as $project) {
            $users = $this->getUsersInProject($project);
            if (isset($users[$userName])) {
                $projectList[] = $project;
            }
        }
        return $projectList;
    }

    public function createDirStructure($projectName, $structure)
    {
        $appAccount = @$this->getServiceLocator()->get('Config')['smg_svn']['app_account'];
        if (!$appAccount) {
            throw new \Exception('You need to set up app_account config to use this function');
        }
        $userName = key($appAccount);
        $password = current($appAccount);
        $this->addUserToProject($projectName, $userName, $password);
        /** @var \SmgSvn\Svn $svnClient */
        $svnClient = $this->getServiceLocator()->get('SvnClient');
        $svnClient->run(sprintf('mkdir %s %s -m"Creating directories"', $this->getSvnMkdirParams($projectName, $structure), $this->getSvnLoginParams($userName, $password)));
    }

    public function setSvnServeConf($projectName, $config) {
        $iniFile = @$this->getIniReader()->fromFile($this->getProjectSvnServeConfFileName($projectName));
        $iniFile = array_merge($iniFile,$config);
        $this->getIniWriter()->toFile($this->getProjectSvnServeConfFileName($projectName), $iniFile);
    }

    protected function getSvnMkdirParams($projectName, $structure)
    {
        $directories = array();
        $repoUrl = $this->getConfig(['smg_svn', 'svn_repo_url']);
        foreach ($structure as $dir) {
            $directories = array_merge($directories, $this->parseDirectories($dir));
        }
        $result = '';
        foreach ($directories as $dir) {
            $result .= sprintf(' "%s/%s%s"', $repoUrl, $projectName, $dir);
        }

        return $result;
    }

    protected function parseDirectories($dir)
    {
        $result = array();
        $dirList = explode('/', $dir);
        $newDir = '';
        foreach ($dirList as $dir) {
            $newDir .= '/' . $dir;
            $result[$newDir] = $newDir;
        }

        return $result;
    }

    /**
     * @param $projectName
     * @return string
     */
    protected function getProjectPasswdFileName($projectName)
    {
        $pwdFileName = $this->getRepoPath() . DS . $projectName . DS . 'conf' . DS . 'passwd';
        return $pwdFileName;
    }

    /**
     * @param $projectName
     * @return string
     */
    protected function getProjectSvnServeConfFileName($projectName)
    {
        $pwdFileName = $this->getRepoPath() . DS . $projectName . DS . 'conf' . DS . 'svnserve.conf';
        return $pwdFileName;
    }

    /**
     * @return IniReader
     */
    protected function getIniReader()
    {
        return $this->iniReader;
    }

    /**
     * @return IniWriter
     */
    protected function getIniWriter()
    {
        return $this->iniWriter;
    }

    protected function getSvnLoginParams($userName, $password)
    {
        return sprintf('--username %s --password %s', $userName, $password);
    }
}