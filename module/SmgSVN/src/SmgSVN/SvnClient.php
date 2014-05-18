<?php
/**
 * Created by PhpStorm.
 * User: smeagol
 * Date: 17.05.14
 * Time: 14:53
 */

namespace SmgSVN;


class SvnClient extends SvnBase {

    protected function getCmd() {
        if (!$this->command) {
            $this->command = sprintf('%s ', $this->getConfig(['smg_svn','svn_bin_path']));
        }
        return $this->command;
    }
}