<?php
//區塊主函式 (把所有的書以文字列出)
function tad_book3_list($options)
{
    global $xoopsDB;
    if (empty($options[0])) {
        $options[0] = "5";
    }

    if (empty($options[1])) {
        $options[1] = "create_date";
    }

    if (empty($options[2])) {
        $options[2] = "desc";
    }

    $i      = 0;
    $sql    = "select `tbsn`,`title`,`counter` from " . $xoopsDB->prefix("tad_book3") . " where enable='1' order by {$options[1]} {$options[2]} limit 0,{$options[0]}";
    $result = $xoopsDB->query($sql) or web_error($sql);
    while (list($tbsn, $title, $counter) = $xoopsDB->fetchRow($result)) {
        $block[$i]['tbsn']    = $tbsn;
        $block[$i]['title']   = $title;
        $block[$i]['counter'] = $counter;
        $i++;
    }

    return $block;
}

//區塊編輯函式
function tad_book3_list_edit($options)
{
    $seled1_0 = ($options[1] == "counter") ? "selected" : "";
    $seled1_1 = ($options[1] == "create_date") ? "selected" : "";
    $seled1_2 = ($options[1] == "title") ? "selected" : "";
    $chked2_0 = ($options[2] == "") ? "checked" : "";
    $chked2_1 = ($options[2] == "desc") ? "checked" : "";

    $form = "
	" . _MB_TADBOOK3_TAD_BOOK3_LIST_EDIT_BITEM0 . "
	<INPUT type='text' name='options[0]' value='{$options[0]}' size=3><br>
	" . _MB_TADBOOK3_TAD_BOOK3_LIST_EDIT_BITEM1 . "
	<select name='options[1]'>
		<option $seled1_0 value='counter'>" . _MB_TADBOOK3_COUNTER . "</option>
		<option $seled1_1 value='create_date'>" . _MB_TADBOOK3_POST_DATE . "</option>
		<option $seled1_2 value='title'>" . _MB_TADBOOK3_TITLE . "</option>
	</select><br>
	" . _MB_TADBOOK3_TAD_BOOK3_LIST_EDIT_BITEM2 . "
	<INPUT type='radio' $chked2_0 name='options[2]' value=''>" . _MB_TADBOOK3_ASC . "
	<INPUT type='radio' $chked2_1 name='options[2]' value='desc'>" . _MB_TADBOOK3_DESC . "
	";
    return $form;
}
