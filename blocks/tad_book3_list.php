<?php
//  ------------------------------------------------------------------------ //
// 本模組由 tad 製作
// ------------------------------------------------------------------------- //

//區塊主函式 (把所有的書以文字列出)
function tad_book3_list($options){
	global $xoopsDB;
	$block="<table>";
	if(empty($options[0]))$options[0]="5";
	if(empty($options[1]))$options[1]="create_date";
	if(empty($options[2]))$options[2]="desc";
	$sql = "select `tbsn`,`title`,`counter` from ".$xoopsDB->prefix("tad_book3")." where enable='1' order by {$options[1]} {$options[2]} limit 0,{$options[0]}";
	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	while(list($tbsn,$title,$counter)=$xoopsDB->fetchRow($result)){
	  $block.="<tr><td><img src='".XOOPS_URL."/modules/tad_book3/images/dot_started.gif' hspace=2 align='absmiddle'></td>
		<td><a href='".XOOPS_URL."/modules/tad_book3/index.php?op=list_docs&tbsn=$tbsn'>$title</a> ($counter)</td></tr>";
	}
	$block.="</table>";
	return $block;
}

//區塊編輯函式
function tad_book3_list_edit($options){
	$seled1_0=($options[1]=="counter")?"selected":"";
	$seled1_1=($options[1]=="create_date")?"selected":"";
	$seled1_2=($options[1]=="title")?"selected":"";
	$chked2_0=($options[2]=="")?"checked":"";
	$chked2_1=($options[2]=="desc")?"checked":"";

	$form="
	"._MB_TADBOOK3_TAD_BOOK3_LIST_EDIT_BITEM0."
	<INPUT type='text' name='options[0]' value='{$options[0]}' size=3><br>
	"._MB_TADBOOK3_TAD_BOOK3_LIST_EDIT_BITEM1."
	<select name='options[1]'>
		<option $seled1_0 value='counter'>"._MB_TADBOOK3_COUNTER."</option>
		<option $seled1_1 value='create_date'>"._MB_TADBOOK3_POST_DATE."</option>
		<option $seled1_2 value='title'>"._MB_TADBOOK3_TITLE."</option>
	</select><br>
	"._MB_TADBOOK3_TAD_BOOK3_LIST_EDIT_BITEM2."
	<INPUT type='radio' $chked2_0 name='options[2]' value=''>"._MB_TADBOOK3_ASC."
	<INPUT type='radio' $chked2_1 name='options[2]' value='desc'>"._MB_TADBOOK3_DESC."
	";
	return $form;
}

?>
