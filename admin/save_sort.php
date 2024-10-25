<?php
use XoopsModules\Tadtools\Utility;
/*-----------引入檔案區--------------*/
require dirname(dirname(dirname(__DIR__))) . '/include/cp_header.php';

$tbcsn = (int) $_POST['tbcsn'];
$sort = (int) $_POST['sort'];
$sql = 'UPDATE `' . $xoopsDB->prefix('tad_book3_cate') . '` SET `sort`=? WHERE `tbcsn`=?';
Utility::query($sql, 'ii', [$sort, $tbcsn]) or die(_TAD_SORT_FAIL . ' (' . date('Y-m-d H:i:s') . ')');

echo _TAD_SORTED . "(" . date("Y-m-d H:i:s") . ")";
