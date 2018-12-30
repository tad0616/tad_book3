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

    if ($options[3] == '') {
        $options[3] = "1";
    }

    $show_ncsn = isset($options[4]) ? $options[4] : "";
    // $ncsn_arr  = explode(',', $show_ncsn);

    $i         = 0;
    $and_tbcsn = empty($show_ncsn) ? "" : "and tbcsn in($show_ncsn)";
    $sql       = "select `tbsn`,`title`,`counter`,`pic_name` from " . $xoopsDB->prefix("tad_book3") . " where enable='1' $and_tbcsn order by {$options[1]} {$options[2]} limit 0,{$options[0]}";

    $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
    while (list($tbsn, $title, $counter, $pic_name) = $xoopsDB->fetchRow($result)) {
        $block[$i]['tbsn']         = $tbsn;
        $block[$i]['title']        = $title;
        $block[$i]['counter']      = $counter;
        $block[$i]['show_counter'] = $options[3];
        $block[$i]['pic']          = (empty($pic_name)) ? XOOPS_URL . "/modules/tad_book3/images/blank.png" : XOOPS_URL . "/uploads/tad_book3/{$pic_name}";
        $i++;
    }
    $data['books']    = $block;
    $data['show_pic'] = $options[5];
    return $data;
}

//區塊編輯函式
function tad_book3_list_edit($options)
{
    $seled1_0 = ($options[1] == "counter") ? "selected" : "";
    $seled1_1 = ($options[1] == "create_date") ? "selected" : "";
    $seled1_2 = ($options[1] == "title") ? "selected" : "";
    $seled1_3 = ($options[1] == "sort") ? "selected" : "";

    $chked2_0 = ($options[2] == "") ? "checked" : "";
    $chked2_1 = ($options[2] == "desc") ? "checked" : "";

    $chked3_0 = ($options[3] == "0") ? "checked" : "";
    $chked3_1 = ($options[3] != "0") ? "checked" : "";

    $options4_1 = ($options[4] == "1") ? "checked" : "";
    $options4_0 = ($options[4] == "0") ? "checked" : "";
    $option     = block_book_cate($options[4]);

    $chked5_0 = ($options[5] == "0") ? "checked" : "";
    $chked5_1 = ($options[5] != "0") ? "checked" : "";

    $form = "
    {$option['js']}
	" . _MB_TADBOOK3_TAD_BOOK3_LIST_EDIT_BITEM0 . "
	<INPUT type='text' name='options[0]' value='{$options[0]}' size=3><br>
	" . _MB_TADBOOK3_TAD_BOOK3_LIST_EDIT_BITEM1 . "
	<select name='options[1]'>
		<option $seled1_0 value='counter'>" . _MB_TADBOOK3_COUNTER . "</option>
		<option $seled1_1 value='create_date'>" . _MB_TADBOOK3_POST_DATE . "</option>
		<option $seled1_2 value='title'>" . _MB_TADBOOK3_TITLE . "</option>
        <option $seled1_3 value='sort'>" . _MB_TADBOOK3_SORT . "</option>
	</select><br>
	" . _MB_TADBOOK3_TAD_BOOK3_LIST_EDIT_BITEM2 . "
	<INPUT type='radio' $chked2_0 name='options[2]' value=''>" . _MB_TADBOOK3_ASC . "
	<INPUT type='radio' $chked2_1 name='options[2]' value='desc'>" . _MB_TADBOOK3_DESC . "<br>
    " . _MB_TADBOOK3_TAD_BOOK3_SHOW_COUNT . "
    <INPUT type='radio' $chked3_1 name='options[3]' value='1'>" . _YES . "
    <INPUT type='radio' $chked3_0 name='options[3]' value='0'>" . _NO . "<br>

    " . _MB_TADBOOK3_SHOW_CATE . "
      {$option['form']}
      <INPUT type='hidden' name='options[4]' id='bb' value='{$options[4]}'><br>
    " . _MB_TADBOOK3_SHOW_PIC . "
    <INPUT type='radio' $chked5_1 name='options[5]' value='1'>" . _YES . "
    <INPUT type='radio' $chked5_0 name='options[5]' value='0'>" . _NO . "<br>
	";
    return $form;
}

//取得所有類別標題
if (!function_exists("block_book_cate")) {
    function block_book_cate($selected = "")
    {
        global $xoopsDB;

        if (!empty($selected)) {
            $sc = explode(",", $selected);
        }

        $js = "<script>
            function bbv(){
              i=0;
              var arr = new Array();";

        $sql    = "SELECT tbcsn,title FROM " . $xoopsDB->prefix("tad_book3_cate") . " ORDER BY sort";
        $result = $xoopsDB->query($sql);
        $option = "";
        while (list($tbcsn, $title) = $xoopsDB->fetchRow($result)) {
            $js      .= "if(document.getElementById('c{$tbcsn}').checked){
               arr[i] = document.getElementById('c{$tbcsn}').value;
               i++;
              }";
            $ckecked = (in_array($tbcsn, $sc)) ? "checked" : "";
            $option  .= "<span style='white-space:nowrap;'><input type='checkbox' id='c{$tbcsn}' value='{$tbcsn}' class='bbv' onChange=bbv() $ckecked><label for='c{$tbcsn}'>$title</label></span> ";
        }

        $js .= "document.getElementById('bb').value=arr.join(',');
    }
    </script>";

        $main['js']   = $js;
        $main['form'] = $option;
        return $main;
    }
}
