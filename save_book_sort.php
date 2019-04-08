<?php
/*-----------引入檔案區--------------*/
include "../../mainfile.php";
include "../function.php";
include_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
$updateRecordsArray = system_CleanVars($_POST, 'tr', '', 'array');

$sort = 1;
foreach ($updateRecordsArray as $recordIDValue) {
    $sql = "update " . $xoopsDB->prefix("tad_book3") . " set `sort`='{$sort}' where `tbsn`='{$recordIDValue}'";
    $xoopsDB->queryF($sql) or die("Save Sort Fail! (" . date("Y-m-d H:i:s") . ")");
    $sort++;
}

echo "Save Sort OK! (" . date("Y-m-d H:i:s") . ")";
