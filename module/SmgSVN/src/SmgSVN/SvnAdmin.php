<?php
/**
 * Created by PhpStorm.
 * User: smeagol
 * Date: 17.05.14
 * Time: 14:53
 */

namespace SmgSVN;


class SvnAdmin extends SvnBase {

    protected $svnManager;

    function __construct()
    {
        $this->svnManager = new Svn();
    }

    public function create($projectName) {
        $repoPath = $this->getRepoPath();
        return $this->run(sprintf('create %s%s%s',$repoPath, DS, $projectName));
    }

    protected function getCmd() {
        if (!$this->command) {
            $this->command = sprintf('%s ', $this->getConfig(['smg_svn','svnadmin_bin_path']));
        }
        return $this->command;
    }
}