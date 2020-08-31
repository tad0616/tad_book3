<?php
/*-----------引入檔案區--------------*/
require dirname(dirname(__DIR__)) . '/mainfile.php';
if (!$_SESSION['tad_book3_adm']) {
    exit;
}

$sort = 1;
foreach ($_POST['book'] as $tbsn) {
    $tbsn = (int) $tbsn;
    $sql = 'update ' . $xoopsDB->prefix('tad_book3') . " set `sort`='{$sort}' where `tbsn`='{$tbsn}'";
    $xoopsDB->queryF($sql) or die('Save Sort Fail! (' . date('Y-m-d H:i:s') . ')');
    $sort++;
}

echo 'Save Sort OK! (' . date('Y-m-d H:i:s') . ')';
