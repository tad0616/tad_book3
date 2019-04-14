<?php

use XoopsModules\Tad_book3\Utility;

function xoops_module_update_tad_book3(&$module, $old_version)
{
    global $xoopsDB;

    if (Utility::chk_chk1()) {
        Utility::go_update1();
    }

    //if(!Utility::chk_chk2()) Utility::go_update2();
    if (Utility::chk_uid()) {
        Utility::go_update_uid();
    }
    Utility::chk_tad_book3_block();

    $old_fckeditor = XOOPS_ROOT_PATH . '/modules/tad_book3/fckeditor';
    if (is_dir($old_fckeditor)) {
        Utility::delete_directory($old_fckeditor);
    }

    return true;
}
