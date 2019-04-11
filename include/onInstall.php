<?php

use XoopsModules\Tad_book3\Utility;

include dirname(__DIR__) . '/preloads/autoloader.php';

function xoops_module_install_tad_book3(&$module)
{
    
    Utility::mk_dir(XOOPS_ROOT_PATH . "/uploads/tad_book3");
    Utility::mk_dir(XOOPS_ROOT_PATH . "/uploads/tad_book3/file");
    Utility::mk_dir(XOOPS_ROOT_PATH . "/uploads/tad_book3/image");
    Utility::mk_dir(XOOPS_ROOT_PATH . "/uploads/tad_book3/image/.thumbs");

    return true;
}


