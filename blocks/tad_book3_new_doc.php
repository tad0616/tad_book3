<?php
use XoopsModules\Tadtools\Utility;
if (!class_exists('XoopsModules\Tadtools\Utility')) {
    require XOOPS_ROOT_PATH . '/modules/tadtools/preloads/autoloader.php';
}
use XoopsModules\Tad_book3\Tools;
if (!class_exists('XoopsModules\Tad_book3\Tools')) {
    require XOOPS_ROOT_PATH . '/modules/tad_book3/preloads/autoloader.php';
}
//區塊主函式 (會列出最新發表的文章)
function tad_book3_new_doc($options)
{
    global $xoopsDB;

    $now = date('Y-m-d H:i:s', xoops_getUserTimestamp(time()));

    $block = [];
    $sql = 'SELECT a.`tbdsn`, a.`tbsn`, a.`category`, a.`page`, a.`paragraph`, a.`sort`, a.`title`, a.`last_modify_date`, b.`title` FROM `' . $xoopsDB->prefix('tad_book3_docs') . '` AS a LEFT JOIN `' . $xoopsDB->prefix('tad_book3') . '` AS b ON a.`tbsn` = b.`tbsn` WHERE a.`enable` = 1 AND TO_DAYS(?) - TO_DAYS(FROM_UNIXTIME(a.`last_modify_date`)) <= ? ORDER BY a.`last_modify_date` DESC';
    $result = Utility::query($sql, 'si', [$now, $options[0]]) or Utility::web_error($sql, __FILE__, __LINE__);

    //$today=date("Y-m-d H:i:s",xoops_getUserTimestamp(time()));
    $i = 0;
    while (list($tbdsn, $tbsn, $category, $page, $paragraph, $sort, $title, $last_modify_date, $book_title) = $xoopsDB->fetchRow($result)) {
        $last_modify_date = date('Y-m-d', xoops_getUserTimestamp($last_modify_date));
        //if($today > $show_time+$last_modify_date)continue;
        $doc_sort = Tools::mk_category($category, $page, $paragraph, $sort);
        $block[$i]['doc_sort'] = $doc_sort['main'];
        $block[$i]['tbsn'] = $tbsn;
        $block[$i]['tbdsn'] = $tbdsn;
        $block[$i]['title'] = $title;
        $block[$i]['last_modify_date'] = $last_modify_date;
        $block[$i]['book_title'] = $book_title;
        $i++;
    }

    return $block;
}

//區塊編輯函式
function tad_book3_new_doc_edit($options)
{
    $form = "
    <ol class='my-form'>
        <li class='my-row'>
            <lable class='my-label'>" . _MB_TADBOOK3_TAD_BOOK3_NEW_DOC_EDIT_BITEM0 . "</lable>
            <div class='my-content'>
                <input type='text' class='my-input' name='options[0]' value='{$options[0]}' size=6>
            </div>
        </li>
    </ol>";

    return $form;
}
