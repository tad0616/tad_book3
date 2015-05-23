<?php
//  ------------------------------------------------------------------------ //
// 本模組由 tad 製作
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
include "../../../include/cp_header.php";
include "../function.php";
$updateRecordsArray 	= $_POST['tr'];

$sort = 1;
foreach ($updateRecordsArray as $recordIDValue) {
  $sql="update ".$xoopsDB->prefix("tad_book3")." set `sort`='{$sort}' where `tbsn`='{$recordIDValue}'";
  $xoopsDB->queryF($sql) or die("Save Sort Fail! (".date("Y-m-d H:i:s").")");
  $sort++;
}

echo "Save Sort OK! (".date("Y-m-d H:i:s").")";
?>
