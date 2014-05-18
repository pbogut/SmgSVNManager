<?php

namespace SmgSVN;

define('DS', DIRECTORY_SEPARATOR);

return array(
    'smg_svn' => array(
        /**
         * Path to repository dir
         */
        'svn_repo_path' => '/home/multimedia/svn',

        /**
         * Path to svnadmin binaries
         */
        'svnadmin_bin_path' => '/usr/bin/svnadmin',

        /**
         * Path to svn binaries
         */
        'svn_bin_path' => '/usr/bin/svn',

        'svn_repo_url' => 'svn://localhost:1100',

        'temp_path' => '/tmp',
        'project_structures' => array(
            'main' => array(
                'trunk',
                'tags',
                'brunches',
                'doc',
            ),
        ),
        'app_account' => array(
            'svn' => 'change_this_password'
        )
    ),
);