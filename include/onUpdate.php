<?php

use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_book3\Update;

function xoops_module_update_tad_book3(&$module, $old_version)
{
    global $xoopsDB;

    if (Update::chk_chk1()) {
        Update::go_update1();
    }

    if (Update::chk_uid()) {
        Update::go_update_uid();
    }
    Update::chk_tad_book3_block();

    $old_fckeditor = XOOPS_ROOT_PATH . '/modules/tad_book3/fckeditor';
    if (is_dir($old_fckeditor)) {
        Utility::delete_directory($old_fckeditor);
    }

    return true;
}
