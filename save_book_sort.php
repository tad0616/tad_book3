<?php
use Xmf\Request;
use XoopsModules\Tadtools\Utility;
/*-----------引入檔案區--------------*/
require dirname(dirname(__DIR__)) . '/mainfile.php';
if (!$_SESSION['tad_book3_adm']) {
    exit;
}
// 關閉除錯訊息
$xoopsLogger->activated = false;

$updateRecordsArray = Request::getVar('book', [], null, 'array', 4);
$sort = 1;
foreach ($updateRecordsArray as $tbsn) {
    $tbsn = (int) $tbsn;
    $sql = 'UPDATE `' . $xoopsDB->prefix('tad_book3') . '` SET `sort`=? WHERE `tbsn`=?';
    Utility::query($sql, 'ii', [$sort, $tbsn]) or die(_TAD_SORT_FAIL . ' (' . date('Y-m-d H:i:s') . ')');

    $sort++;
}

echo _TAD_SORTED . "(" . date("Y-m-d H:i:s") . ")";
