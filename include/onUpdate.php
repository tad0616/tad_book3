<?php

use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_book3\Update;
if (!class_exists('XoopsModules\Tadtools\Utility')) {
    require XOOPS_ROOT_PATH . '/modules/tadtools/preloads/autoloader.php';
}
if (!class_exists('XoopsModules\Tad_book3\Update')) {
    include dirname(__DIR__) . '/preloads/autoloader.php';
}
function xoops_module_update_tad_book3(&$module, $old_version)
{
    global $xoopsDB;

    if (Update::chk_chk1()) {
        Update::go_update1();
    }

    if (Update::chk_chk2()) {
        Update::go_update2();
    }

    if (Update::chk_chk3()) {
        Update::go_update3();
    }

    if (Update::chk_chk4()) {
        Update::go_update4();
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
