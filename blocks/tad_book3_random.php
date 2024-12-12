<?php
use XoopsModules\Tadtools\Utility;
if (!class_exists('XoopsModules\Tadtools\Utility')) {
    require XOOPS_ROOT_PATH . '/modules/tadtools/preloads/autoloader.php';
}
use XoopsModules\Tad_book3\Tools;
if (!class_exists('XoopsModules\Tad_book3\Tools')) {
    require XOOPS_ROOT_PATH . '/modules/tad_book3/preloads/autoloader.php';
}

//區塊主函式 (會隨機出現書的封面)
function tad_book3_random($options)
{
    global $xoopsDB, $xoTheme;
    $block = [];

    $sql = 'SELECT `tbsn`, `title`, `counter`, `pic_name`, `read_group` FROM `' . $xoopsDB->prefix('tad_book3') . '` WHERE `enable`=1 ORDER BY RAND() LIMIT 0, ?';
    $result = Utility::query($sql, 'i', [$options[0]]) or Utility::web_error($sql, __FILE__, __LINE__);

    $i = 0;
    while (list($tbsn, $title, $counter, $pic_name, $read_group) = $xoopsDB->fetchRow($result)) {
        if (!Tools::chk_power($read_group)) {
            continue;
        }

        $pic = (empty($pic_name)) ? XOOPS_URL . '/modules/tad_book3/images/blank.png' : XOOPS_URL . "/uploads/tad_book3/{$pic_name}";

        $block[$i]['tbsn'] = $tbsn;
        $block[$i]['show_title'] = $options[1];
        $block[$i]['counter'] = $counter;
        $block[$i]['pic'] = $pic;
        $block[$i]['title'] = $title;
        $i++;
    }
    $xoTheme->addStylesheet('modules/tad_book3/css/module.css');
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
