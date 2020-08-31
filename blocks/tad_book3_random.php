<?php
use XoopsModules\Tadtools\Utility;

//區塊主函式 (會隨機出現書的封面)
function tad_book3_random($options)
{
    global $xoopsDB;
    require_once XOOPS_ROOT_PATH . '/modules/tad_book3/function_block.php';
    $block = [];

    $sql = 'select `tbsn`,`title`,`counter`,`pic_name`, `read_group` from ' . $xoopsDB->prefix('tad_book3') . " where enable='1' order by rand() limit 0, {$options[0]}";
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
    $i = 0;
    while (list($tbsn, $title, $counter, $pic_name, $read_group) = $xoopsDB->fetchRow($result)) {
        if (!chk_power($read_group)) {
            continue;
        }

        $pic = (empty($pic_name)) ? XOOPS_URL . '/modules/tad_book3/images/blank.png' : XOOPS_URL . "/uploads/tad_book3/{$pic_name}";
        // $booktitle = ('0' == $options[1]) ? '' : "<a href='" . XOOPS_URL . "/modules/tad_book3/index.php?op=list_docs&tbsn=$tbsn'>$title</a> ($counter)";

        $block[$i]['tbsn'] = $tbsn;
        $block[$i]['show_title'] = $options[1];
        $block[$i]['counter'] = $counter;
        $block[$i]['pic'] = $pic;
        $block[$i]['title'] = $title;
        $i++;
    }

    return $block;
}

//區塊編輯函式
function tad_book3_random_edit($options)
{
    $chked1_0 = ('1' == $options[1]) ? 'checked' : '';
    $chked1_1 = ('0' == $options[1]) ? 'checked' : '';

    $form = "
    <ol class='my-form'>
        <li class='my-row'>
            <lable class='my-label'>" . _MB_TADBOOK3_TAD_BOOK3_RANDOM_EDIT_BITEM0 . "</lable>
            <div class='my-content'>
                <input type='text' class='my-input' name='options[0]' value='{$options[0]}' size=6>
            </div>
        </li>
        <li class='my-row'>
            <lable class='my-label'>" . _MB_TADBOOK3_TAD_BOOK3_RANDOM_EDIT_BITEM1 . "</lable>
            <div class='my-content'>
                <INPUT type='radio' $chked1_0 name='options[1]' value='1'>" . _YES . "
                <INPUT type='radio' $chked1_1 name='options[1]' value='0'>" . _NO . '
            </div>
        </li>
    </ol>';

    return $form;
}
