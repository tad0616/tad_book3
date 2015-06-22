<?php
/*-----------引入檔案區--------------*/
include "../../../include/cp_header.php";

$tbcsn = intval($_POST['tbcsn']);
$sort  = intval($_POST['sort']);
$sql   = "update " . $xoopsDB->prefix("tad_book3_cate") . " set `sort`='{$sort}' where tbcsn='{$tbcsn}'";
$xoopsDB->queryF($sql) or die("Save Sort Fail! (" . date("Y-m-d H:i:s") . ")");

echo "Save Sort OK! (" . date("Y-m-d H:i:s") . ") ";
