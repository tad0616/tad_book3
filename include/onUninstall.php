<?php

use XoopsModules\Tad_book3\Utility;

function xoops_module_uninstall_tad_book3(&$module)
{
    global $xoopsDB;

    $date = date("Ymd");

    rename(XOOPS_ROOT_PATH . "/uploads/tad_book3", XOOPS_ROOT_PATH . "/uploads/tad_book3_bak_{$date}");

    return true;
}

