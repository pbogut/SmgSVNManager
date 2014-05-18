<?php
/**
 * Created by PhpStorm.
 * User: smeagol
 * Date: 18.05.14
 * Time: 12:53
 */

namespace SmgSVN;


use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class SvnBase implements ServiceLocatorAwareInterface {

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    protected $command;

    public function create($projectName) {
        return $this->exec(sprintf('create %s',$projectName));
    }

    protected function getCmd() {
        throw new \Exception('To use run getCmd needs to be implemented');
    }

    public function run($params) {
        if (is_array($params)) {
            $params = implode(' ', $params);
        }

        return $this->exec(sprintf('%s %s', $this->getCmd(), $params));
    }

    protected function exec($command) {
        exec($command, $output, $return);
        $output = implode("\n",$output);
        if ($return !== 0) {
            throw new \Exception(sprintf('Error: %s\n%s',$return,$output));
        }

        return $output;
    }

    /**
     * @param $path array
     * @return array|object
     */
    protected function getConfig($path = array()) {
        $config = $this->getServiceLocator()->get('Config');
        foreach ($path as $key) {
            $config = $config[$key];
        }

        return $config;
    }

    /**
     * @return string
     */
    protected function getRepoPath()
    {
        $repoPath = rtrim($this->getConfig(['smg_svn', 'svn_repo_path']), '\\/');
        return $repoPath;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

} 