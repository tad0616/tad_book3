<?php
use XoopsModules\Tadtools\Utility;

//區塊主函式 (會自動偵測目前閱讀的書籍，並秀出該書目錄)
function tad_book3_content($options)
{
    global $xoopsDB, $xoopsUser, $xoopsTpl;
    require_once XOOPS_ROOT_PATH . '/modules/tad_book3/function_block.php';

    $def_tbsn = !empty($options[0]) ? (int) $options[0] : '1';

    $uid = ($xoopsUser) ? $xoopsUser->uid() : 0;

    add_book_counter($def_tbsn);

    $all_cate = all_cate();

    $sql = 'select * from ' . $xoopsDB->prefix('tad_book3') . " where tbsn='$def_tbsn' and enable='1'";
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    $data = $xoopsDB->fetchArray($result);
    foreach ($data as $k => $v) {
        $$k = $v;
        $block[$k] = $v;
    }

    if (isset($read_group) && !chk_power($read_group)) {
        $block['msg'] = _MB_TADBOOK3_NO_READ_ACCESS;

        return $block;
    }

    $block['needpasswd'] = 0;
    if (!empty($passwd) and $_SESSION['passwd'] != $passwd) {
        $block['needpasswd'] = 1;
    }

    $block['enable_txt'] = (isset($enable) && '1' == $enable) ? _MB_TADBOOK3_ENABLE : _MB_TADBOOK3_UNABLE;

    //共同編輯者
    $author_arr = isset($author) ? explode(',', $author): [];
    foreach ($author_arr as $uid) {
        $uidname = \XoopsUser::getUnameFromId($uid, 1);
        $uidname = (empty($uidname)) ? XoopsUser::getUnameFromId($uid, 0) : $uidname;
        $uid_name[] = $uidname;
    }
    $block['author'] = isset($uid_name) ? implode(' , ', $uid_name):'';
    $block['create_date'] = date('Y-m-d H:i:s', xoops_getUserTimestamp(strtotime(isset($create_date) ? $create_date : time())));
    $block['cate'] = (!isset($tbcsn) || empty($all_cate[$tbcsn])) ? _MB_TADBOOK3_NOT_CLASSIFIED : $all_cate[$tbcsn];
    $book = book_shadow($data);
    $block['book'] = $book;
    $block['book_content'] = sprintf(_MB_TADBOOK3_BOOK_CONTENT, $title);

    if ($xoopsTpl) {
        $xoopsTpl->assign('xoops_pagetitle', isset($title) ? $title : '');
        $xoopsTpl->assign('fb_description', strip_tags(isset($description)?$description:''));
        $xoopsTpl->assign('logo_img', $book['pic']);
    }

    $i = 0;
    $docs = [];
    $sql = 'select * from ' . $xoopsDB->prefix('tad_book3_docs') . " where tbsn='{$def_tbsn}' order by category,page,paragraph,sort";
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
    $i1 = $i2 = $i3 = $i4 = 0;
    $new_category = $new_page = $new_paragraph = $new_sort = '';
    while (false !== ($data = $xoopsDB->fetchArray($result))) {
        foreach ($data as $k => $v) {
            $$k = $v;
        }

        $doc_sort = mk_category($category, $page, $paragraph, $sort);
        $have_sub = have_sub($def_tbsn, $category, $page, $paragraph, $sort);
        $last_modify_date = date('Y-m-d H:i:s', xoops_getUserTimestamp($last_modify_date));

        if ('1' != $enable) {
            continue;
        }

        $enable_txt = ('1' == $enable) ? '' : '[' . _MB_TADBOOK3_UNABLE . '] ';

        $docs[$i]['tbdsn'] = $tbdsn;
        $docs[$i]['last_modify_date'] = $last_modify_date;
        $docs[$i]['doc_sort_level'] = $doc_sort['level'];
        $docs[$i]['doc_sort_main'] = $doc_sort['main'];
        $docs[$i]['title'] = $title;
        $docs[$i]['count'] = $count;
        $docs[$i]['enable'] = $enable;
        $docs[$i]['enable_txt'] = $enable_txt;
        $docs[$i]['have_sub'] = $have_sub;

        if (empty($new_category)) {
            $new_category = $category;
            $i1++;
        } elseif ($new_category != $category) {
            $new_category = $category;
            $i1++;
            $new_page = 0;
            $new_paragraph = 0;
            $new_sort = 0;
        }

        if (!empty($page)) {
            if (empty($new_page)) {
                $new_page = $page;
                $i2++;
            } elseif ($new_page != $page) {
                $new_page = $page;
                $i2++;
                $new_paragraph = 0;
                $new_sort = 0;
            }
        } else {
            $i2 = 0;
        }

        if (!empty($paragraph)) {
            if (empty($new_paragraph)) {
                $new_paragraph = $paragraph;
                $i3++;
            } elseif ($new_paragraph != $paragraph) {
                $new_paragraph = $paragraph;
                $i3++;
                $new_sort = 0;
            }
        } else {
            $i3 = 0;
        }

        if (!empty($sort)) {
            if (empty($new_sort)) {
                $new_sort = $sort;
                $i4++;
            } elseif ($new_sort != $sort) {
                $new_sort = $sort;
                $i4++;
            }
        } else {
            $i4 = 0;
        }

        $docs[$i]['new_sort'] = mk_category($i1, $i2, $i3, $i4);
        $i++;
    }

    $block['docs'] = $docs;

    return $block;
}

//區塊編輯函式
function tad_book3_content_edit($options)
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
