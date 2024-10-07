<?php
use XoopsModules\Tadtools\Utility;
/*-----------引入檔案區--------------*/
require dirname(dirname(dirname(__DIR__))) . '/include/cp_header.php';

$tbcsn = (int) $_POST['tbcsn'];
$sort = (int) $_POST['sort'];
$sql = 'UPDATE `' . $xoopsDB->prefix('tad_book3_cate') . '` SET `sort`=? WHERE `tbcsn`=?';
Utility::query($sql, 'ii', [$sort, $tbcsn]) or die('Save Sort Fail! (' . date('Y-m-d H:i:s') . ')');

echo 'Save Sort OK! (' . date('Y-m-d H:i:s') . ') ';
