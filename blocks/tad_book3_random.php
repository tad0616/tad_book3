<?php
//區塊主函式 (會隨機出現書的封面)
function tad_book3_random($options)
{
    global $xoopsDB;
    $block = array();

    $sql    = "select `tbsn`,`title`,`counter`,`pic_name` from " . $xoopsDB->prefix("tad_book3") . " where enable='1' order by rand() limit 0,{$options[0]}";
    $result = $xoopsDB->query($sql) or web_error($sql);
    $i      = 0;
    while (list($tbsn, $title, $counter, $pic_name) = $xoopsDB->fetchRow($result)) {
        $pic       = (empty($pic_name)) ? XOOPS_URL . "/modules/tad_book3/images/blank.png" : XOOPS_URL . "/uploads/tad_book3/{$pic_name}";
        $booktitle = ($options[1] == "0") ? "" : "<a href='" . XOOPS_URL . "/modules/tad_book3/index.php?op=list_docs&tbsn=$tbsn'>$title</a> ($counter)";

        $block[$i]['tbsn']      = $tbsn;
        $block[$i]['booktitle'] = $booktitle;
        $block[$i]['counter']   = $counter;
        $block[$i]['pic']       = $pic;
        $block[$i]['title']     = $title;
        $i++;
    }

    return $block;
}

//區塊編輯函式
function tad_book3_random_edit($options)
{
    $chked1_0 = ($options[1] == "1") ? "checked" : "";
    $chked1_1 = ($options[1] == "0") ? "checked" : "";

    $form = "
	" . _MB_TADBOOK3_TAD_BOOK3_RANDOM_EDIT_BITEM0 . "
	<INPUT type='text' name='options[0]' value='{$options[0]}'><br>
	" . _MB_TADBOOK3_TAD_BOOK3_RANDOM_EDIT_BITEM1 . "
	<INPUT type='radio' $chked1_0 name='options[1]' value='1'>" . _YES . "
	<INPUT type='radio' $chked1_1 name='options[1]' value='0'>" . _NO . "
	";
    return $form;
}
