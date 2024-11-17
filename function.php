<?php
use XoopsModules\Tadtools\CkEditor;
use XoopsModules\Tadtools\SweetAlert;
use XoopsModules\Tadtools\TadDataCenter;
use XoopsModules\Tadtools\TreeTable;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tadtools\Wcag;
use XoopsModules\Tad_book3\Tools;

xoops_loadLanguage('main', 'tadtools');

define('_TADBOOK3_BOOK_DIR', XOOPS_ROOT_PATH . '/uploads/tad_book3');
define('_TADBOOK3_BOOK_URL', XOOPS_URL . '/uploads/tad_book3');

//判斷是否對該模組有管理權限
if (!isset($tad_book3_adm)) {
    $tad_book3_adm = isset($xoopsUser) && \is_object($xoopsUser) ? $xoopsUser->isAdmin() : false;
}
//以流水號取得某筆tad_book3_cate資料
function get_tad_book3_cate($tbcsn = '')
{
    global $xoopsDB;
    if (empty($tbcsn)) {
        return;
    }
    $counter = tad_book3_cate_count();
    $sql = 'SELECT * FROM `' . $xoopsDB->prefix('tad_book3_cate') . '` WHERE `tbcsn`=?';
    $result = Utility::query($sql, 'i', [$tbcsn]) or Utility::web_error($sql, __FILE__, __LINE__);

    $data = $xoopsDB->fetchArray($result);
    $data['count'] = isset($counter[$tbcsn]) ? $counter[$tbcsn] : 0;

    return $data;
}

//分類底下的書籍數
function tad_book3_cate_count()
{
    global $xoopsDB;
    $all = [];
    $sql = 'SELECT `tbcsn`, COUNT(*) FROM `' . $xoopsDB->prefix('tad_book3') . '` GROUP BY `tbcsn`';
    $result = Utility::query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    while (list($tbcsn, $count) = $xoopsDB->fetchRow($result)) {
        $all[$tbcsn] = (int) ($count);
    }

    return $all;
}

//以流水號取得某筆tad_book3_docs資料
function get_tad_book3_docs($tbdsn = '')
{
    global $xoopsDB;
    if (empty($tbdsn)) {
        return;
    }

    $sql = 'SELECT * FROM `' . $xoopsDB->prefix('tad_book3_docs') . '` WHERE `tbdsn`=?';
    $result = Utility::query($sql, 'i', [$tbdsn]) or Utility::web_error($sql, __FILE__, __LINE__);

    $data = $xoopsDB->fetchArray($result);

    return $data;
}

//秀出所有分類及書籍
function list_all_cate_book()
{
    global $xoopsDB, $xoopsTpl;

    $i = 0;
    $cates = [];
    $sql = 'SELECT * FROM `' . $xoopsDB->prefix('tad_book3_cate') . '` ORDER BY `sort`';
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    while (false !== ($data = $xoopsDB->fetchArray($result))) {
        $cates[$i] = $data;

        $sql = 'SELECT * FROM `' . $xoopsDB->prefix('tad_book3') . '` WHERE `tbcsn` = ? AND `enable` = ? ORDER BY `sort`';
        $result2 = Utility::query($sql, 'is', [$data['tbcsn'], '1']) or Utility::web_error($sql, __FILE__, __LINE__);

        $j = 0;
        $books = [];
        while (false !== ($data2 = $xoopsDB->fetchArray($result2))) {
            if (!Tools::chk_power($data2['read_group'])) {
                continue;
            }
            $books[$j] = Tools::book_shadow($data2);
            $j++;
        }
        $cates[$i]['books'] = $books;
        $i++;
    }
    // // Utility::dd($cates);
    $xoopsTpl->assign('cates', $cates);

    $SweetAlert = new SweetAlert();
    $SweetAlert->render('delete_tad_book3_func', 'admin/main.php?op=delete_tad_book3&tbsn=', 'tbsn');
}

//列出某書資料
function list_docs($def_tbsn = '')
{
    global $xoopsDB, $xoopsUser, $xoopsTpl, $xoopsModuleConfig;

    if ($xoopsUser) {
        $uid = $xoopsUser->uid();
    } else {
        $uid = 0;
    }

    if (empty($def_tbsn)) {
        header("location: index.php");
        exit;
    }

    Tools::add_book_counter($def_tbsn);

    $xoopsTpl->assign('now_op', 'list_docs');

    $all_cate = Tools::all_cate();

    $sql = 'SELECT * FROM `' . $xoopsDB->prefix('tad_book3') . '` WHERE `tbsn` =? AND `enable`=?';
    $result = Utility::query($sql, 'is', [$def_tbsn, 1]) or Utility::web_error($sql, __FILE__, __LINE__);

    $data = $xoopsDB->fetchArray($result);
    foreach ($data as $k => $v) {
        $$k = $v;
    }

    if (!Tools::chk_power($read_group)) {
        redirect_header('index.php', 3, _MD_TADBOOK3_CANT_READ);
    }

    $needpasswd = 0;
    if (!empty($passwd) and $_SESSION['passwd'] != $passwd) {
        $needpasswd = 1;
    }

    $enable_txt = ('1' == $enable) ? _MD_TADBOOK3_ENABLE : _MD_TADBOOK3_UNABLE;

    $read_group = Utility::txt_to_group_name($read_group, _MD_TADBOOK3_ALL_OPEN);
    $video_group = Utility::txt_to_group_name($video_group, _MD_TADBOOK3_ALL_OPEN);

    //共同編輯者
    $author_arr = explode(',', $author);
    $my = in_array($uid, $author_arr);
    $xoopsTpl->assign('my', $my);
    foreach ($author_arr as $uid) {
        $uidname = \XoopsUser::getUnameFromId($uid, 1);
        $uidname = (empty($uidname)) ? XoopsUser::getUnameFromId($uid, 0) : $uidname;
        $uid_name[] = $uidname;
    }
    $author = implode(' , ', $uid_name);
    $uid_name = '';

    $create_date = date('Y-m-d H:i:s', xoops_getUserTimestamp(strtotime($create_date)));

    $cates = (empty($all_cate[$tbcsn])) ? _MD_TADBOOK3_NOT_CLASSIFIED : $all_cate[$tbcsn];

    $book = Tools::book_shadow($data);

    $xoopsTpl->assign('book', $book);
    $xoopsTpl->assign('tbsn', $def_tbsn);
    $xoopsTpl->assign('cates', $cates);
    $xoopsTpl->assign('title', $title);
    $xoopsTpl->assign('description', $description);
    $xoopsTpl->assign('sort', $sort);
    $xoopsTpl->assign('read_group', $read_group);
    $xoopsTpl->assign('video_group', $video_group);
    $xoopsTpl->assign('author', $author);
    $xoopsTpl->assign('passwd', $passwd);
    $xoopsTpl->assign('needpasswd', $needpasswd);
    $xoopsTpl->assign('enable', $enable);
    $xoopsTpl->assign('enable_txt', $enable_txt);
    $xoopsTpl->assign('counter', $counter);
    $xoopsTpl->assign('create_date', $create_date);
    $xoopsTpl->assign('push_url', Utility::push_url());
    $xoopsTpl->assign('book_content', sprintf(_MD_TADBOOK3_BOOK_CONTENT, $title));

    $xoopsTpl->assign('xoops_pagetitle', $title);
    $xoopsTpl->assign('fb_description', strip_tags($description));
    // $xoopsTpl->assign('logo_img', $book['pic']);
    $xoopsTpl->assign('use_social_tools', $xoopsModuleConfig['use_social_tools']);

    Utility::setup_meta($title, $description, $book['pic_fb']);

    list($docs, $total_time, $total_view) = get_docs($def_tbsn, false, $my);

    $xoopsTpl->assign('docs', $docs);
    $percentage = $total_time ? round($total_view / $total_time, 4) * 100 : 0;
    $total_view = secondsToTime($total_view);
    $total_time = secondsToTime($total_time);

    $xoopsTpl->assign('total_view', $total_view);
    $xoopsTpl->assign('total_time', $total_time);
    $xoopsTpl->assign('percentage', $percentage);

    $view_info = sprintf(_MD_TADBOOK3_VIEW_LOG, $total_time, $total_view, $percentage);
    $xoopsTpl->assign('view_info', $view_info);

    $SweetAlert = new SweetAlert();
    $SweetAlert->render('delete_tad_book3_func', 'admin/main.php?op=delete_tad_book3&tbsn=', 'tbsn');

    $SweetAlert2 = new SweetAlert();
    $SweetAlert2->render('delete_tad_book3_docs_func', "index.php?op=delete_tad_book3_docs&tbsn={$def_tbsn}&tbdsn=", 'tbdsn');

    //treetable($show_jquery=true , $sn="cat_sn" , $of_sn="of_cat_sn" , $tbl_id="#tbl" , $post_url="save_drag.php" ,$folder_class=".folder", $msg="#save_msg" ,$expanded=true,$sort_id="", $sort_url="save_sort.php", $sort_msg="#save_msg2")
    $TreeTable = new TreeTable(false, '', '', '#content_tbl');
    $TreeTable->render();
}

// 找出所本書所有單元
function get_docs($def_tbsn, $have_content = false, $my = true)
{
    global $xoopsDB;

    $lengths = get_video_lengths($def_tbsn);
    $logs = get_user_logs($def_tbsn);
    $docs = [];
    $and = $have_content ? "AND `content` != ''" : '';
    $sql = 'SELECT * FROM `' . $xoopsDB->prefix('tad_book3_docs') . '` WHERE `tbsn` = ? ' . $and . ' ORDER BY `category`, `page`, `paragraph`, `sort`';
    $result = Utility::query($sql, 'i', [$def_tbsn]) or Utility::web_error($sql, __FILE__, __LINE__);

    $i = $i1 = $i2 = $i3 = $i4 = $total_time = $total_view = 0;
    $new_category = $new_page = $new_paragraph = $new_sort = '';
    while (false !== ($data = $xoopsDB->fetchArray($result))) {
        foreach ($data as $k => $v) {
            $$k = $v;
        }

        $doc_sort = Tools::mk_category($category, $page, $paragraph, $sort);
        $have_sub = Tools::have_sub($def_tbsn, $category, $page, $paragraph, $sort);
        $last_modify_date = date('Y-m-d H:i:s', xoops_getUserTimestamp($last_modify_date));

        if ('1' != $enable and !$my) {
            continue;
        }

        $enable_txt = ('1' == $enable) ? '' : '[' . _MD_TADBOOK3_UNABLE . '] ';

        $docs[$i]['tbdsn'] = $tbdsn;
        $docs[$i]['last_modify_date'] = $last_modify_date;
        $docs[$i]['doc_sort_level'] = $doc_sort['level'];
        $docs[$i]['doc_sort_main'] = $doc_sort['main'];
        $docs[$i]['ttid'] = $doc_sort['ttid'];
        $docs[$i]['doc_sort_parent'] = $doc_sort['parent'];
        $docs[$i]['title'] = $title;
        $docs[$i]['category'] = $category;
        $docs[$i]['page'] = $page;
        $docs[$i]['paragraph'] = $paragraph;
        $docs[$i]['sort'] = $sort;
        $docs[$i]['content'] = $content;
        $docs[$i]['count'] = $count;
        $docs[$i]['enable'] = $enable;
        $docs[$i]['enable_txt'] = $enable_txt;
        $docs[$i]['have_sub'] = $have_sub;
        $docs[$i]['from_tbdsn'] = $from_tbdsn;
        if (is_array($lengths) && isset($lengths[$tbdsn])) {
            $docs[$i]['lengths'] = $lengths[$tbdsn];
            $docs[$i]['time'] = secondsToTime($lengths[$tbdsn]);
            $total_time += $lengths[$tbdsn];
            if ($logs[$tbdsn] && $lengths[$tbdsn]) {
                $docs[$i]['percentage'] = round($logs[$tbdsn] / $lengths[$tbdsn], 2) * 100;
                $total_view += $logs[$tbdsn];
            }
        } else {
            $docs[$i]['lengths'] = $docs[$i]['time'] = $docs[$i]['percentage'] = '';
        }

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

        $docs[$i]['new_sort'] = Tools::mk_category($i1, $i2, $i3, $i4);
        $i++;
    }
    return [$docs, $total_time, $total_view];
}

// 取得所有影片長度
function get_video_lengths($tbsn)
{
    $TadDataCenter = new TadDataCenter('tad_book3');
    $TadDataCenter->set_col($tbsn);
    $data = $TadDataCenter->getData('length');
    $lengths = [];
    foreach ($data as $tbdsn => $value) {
        $lengths[$tbdsn] = $value[0];
    }
    return $lengths;
}

// 取得所有使用者閱讀影片長度
function get_user_logs($tbsn, $uid = '')
{
    global $xoopsUser;
    if (!$xoopsUser and empty($uid)) {
        return [];
    }
    $uid = $uid ? $uid : $xoopsUser->uid();
    $TadDataCenter = new TadDataCenter('tad_book3');
    $TadDataCenter->set_col($uid);
    $data = $TadDataCenter->getData('currentTime');
    $logs = [];
    foreach ($data as $tbdsn => $value) {
        $logs[$tbdsn] = $value[0];
    }
    return $logs;
}

// 查詢某人看到影片的哪裡
function get_video_start($tbdsn)
{
    global $xoopsUser;

    $TadDataCenter = new TadDataCenter('tad_book3');
    $uid = $xoopsUser ? $xoopsUser->uid() : 0;
    $TadDataCenter->set_col($uid, $tbdsn);
    $data = $TadDataCenter->getData('currentTime');
    return (float) $data['currentTime'][0];
}

//tad_book3編輯表單
function tad_book3_form($tbsn = '', $tbcsn = '')
{
    global $xoopsDB, $xoopsUser, $xoopsTpl, $tad_book3_adm;
    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

    //抓取預設值
    if (!empty($tbsn)) {
        $DBV = get_tad_book3($tbsn);
    } else {
        $DBV = [];
    }

    if (!$tad_book3_adm) {
        if (!chk_edit_power($DBV['author'])) {
            header('location:index.php');
            exit;
        }
    }
    //預設值設定

    $tbsn = (!isset($DBV['tbsn'])) ? '' : $DBV['tbsn'];
    $tbcsn = (!isset($DBV['tbcsn'])) ? $tbcsn : $DBV['tbcsn'];
    $sort = (!isset($DBV['sort'])) ? get_max_doc_sort($tbcsn) : $DBV['sort'];
    $title = (!isset($DBV['title'])) ? '' : $DBV['title'];
    $description = (!isset($DBV['description'])) ? '' : $DBV['description'];
    $author = (!isset($DBV['author'])) ? '' : $DBV['author'];
    $read_group = (!isset($DBV['read_group'])) ? '' : $DBV['read_group'];
    $video_group = (!isset($DBV['video_group'])) ? '' : $DBV['video_group'];
    $passwd = (!isset($DBV['passwd'])) ? '' : $DBV['passwd'];
    $enable = (!isset($DBV['enable'])) ? '1' : $DBV['enable'];
    $pic_name = (!isset($DBV['pic_name'])) ? '' : $DBV['pic_name'];
    $counter = (!isset($DBV['counter'])) ? '' : $DBV['counter'];
    $create_date = (!isset($DBV['create_date'])) ? '' : $DBV['create_date'];

    $ck = new CkEditor('tad_book3', 'description', $description);
    $ck->setHeight(150);
    $editor = $ck->render();

    $author_arr = (empty($author)) ? [$xoopsUser->uid()] : explode(',', $author);

    $cate_select = cate_select($tbcsn);

    $memberHandler = xoops_getHandler('member');
    $usercount = $memberHandler->getUserCount(new \Criteria('level', 0, '>'));

    if ($usercount < 1000) {
        $select = new \XoopsFormSelect('', 'author', $author_arr, 5, true);
        $select->setExtra("class='form-control'");
        $memberHandler = xoops_getHandler('member');
        $criteria = new \CriteriaCompo();
        $criteria->setSort('uname');
        $criteria->setOrder('ASC');
        $criteria->setLimit(1000);
        $criteria->setStart(0);

        $select->addOptionArray($memberHandler->getUserList($criteria));
        $user_menu = $select->render();
    } else {
        $user_menu = "<textarea name='author_str' class='form-control'>$author</textarea>
    <div>user uid, ex:\"1,27,103\"</div>";
    }

    $group_arr = (empty($read_group)) ? [''] : explode(',', $read_group);
    $SelectGroup = new \XoopsFormSelectGroup('', 'read_group', false, $group_arr, 5, true);
    $SelectGroup->setExtra("class='form-control'");
    $SelectGroup->addOption('', _MD_TADBOOK3_ALL_OPEN, false);
    $group_menu = $SelectGroup->render();

    $video_group_arr = (empty($video_group)) ? [''] : explode(',', $video_group);
    $SelectGroup = new \XoopsFormSelectGroup('', 'video_group', false, $video_group_arr, 5, true);
    $SelectGroup->setExtra("class='form-control'");
    $SelectGroup->addOption('', _MD_TADBOOK3_ALL_OPEN, false);
    $video_group_menu = $SelectGroup->render();

    $op = (empty($tbsn)) ? 'insert_tad_book3' : 'update_tad_book3';

    $xoopsTpl->assign('action', $_SERVER['PHP_SELF']);
    $xoopsTpl->assign('tbsn', $tbsn);
    $xoopsTpl->assign('cate_select', $cate_select);
    $xoopsTpl->assign('sort', $sort);
    $xoopsTpl->assign('title', $title);
    $xoopsTpl->assign('editor', $editor);
    $xoopsTpl->assign('user_menu', $user_menu);
    $xoopsTpl->assign('group_menu', $group_menu);
    $xoopsTpl->assign('video_group_menu', $video_group_menu);
    $xoopsTpl->assign('enable', $enable);
    $xoopsTpl->assign('passwd', $passwd);
    $xoopsTpl->assign('op', $op);
    $xoopsTpl->assign('now_op', 'tad_book3_form');
}

//新增資料到tad_book3中
function insert_tad_book3()
{
    global $xoopsDB;

    if (!empty($_POST['new_tbcsn'])) {
        $tbcsn = add_tad_book3_cate();
    } else {
        $tbcsn = (int) $_POST['tbcsn'];
    }

    if (!empty($_POST['author_str'])) {
        $author = $_POST['author_str'];
    } else {
        $author = implode(',', $_POST['author']);
    }

    $title = $_POST['title'];
    $description = $_POST['description'];
    $description = Wcag::amend($description);
    $passwd = $_POST['passwd'];
    $enable = $_POST['enable'];
    $pic_name = $_POST['pic_name'];
    $sort = (int) $_POST['sort'];

    $read_group = (in_array('', $_POST['read_group'])) ? '' : implode(',', $_POST['read_group']);
    $video_group = (in_array('', $_POST['video_group'])) ? '' : implode(',', $_POST['video_group']);

    $now = date('Y-m-d H:i:s', xoops_getUserTimestamp(time()));
    $sql = 'INSERT INTO `' . $xoopsDB->prefix('tad_book3') . '` (`tbcsn`, `sort`, `title`, `description`, `author`, `read_group`, `video_group`, `passwd`, `enable`, `pic_name`, `counter`, `create_date`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?)';
    Utility::query($sql, 'iisssssssss', [$tbcsn, $sort, $title, $description, $author, $read_group, $video_group, $passwd, $enable, $pic_name, $now]) or Utility::web_error($sql, __FILE__, __LINE__);

    //取得最後新增資料的流水編號
    $tbsn = $xoopsDB->getInsertId();

    if (!empty($_FILES['pic_name']['name'])) {
        mk_thumb($tbsn, 'pic_name', 120);
    }

    return $tbsn;
}

//新增資料到tad_book3_cate中
function add_tad_book3_cate()
{
    global $xoopsDB;
    if (empty($_POST['new_tbcsn'])) {
        return;
    }

    $title = $_POST['new_tbcsn'];
    $sort = tad_book3_cate_max_sort();
    $sql = 'INSERT INTO `' . $xoopsDB->prefix('tad_book3_cate') . '` (`of_tbsn`,`sort`,`title`) VALUES (?, ?, ?)';
    Utility::query($sql, 'iis', [0, $sort, $title]) or Utility::web_error($sql, __FILE__, __LINE__);
    //取得最後新增資料的流水編號
    $tbcsn = $xoopsDB->getInsertId();

    return $tbcsn;
}

//自動取得新排序
function tad_book3_cate_max_sort()
{
    global $xoopsDB;
    $sql = 'SELECT MAX(`sort`) FROM `' . $xoopsDB->prefix('tad_book3_cate') . '` WHERE `of_tbsn`=?';
    $result = Utility::query($sql, 's', ['']) or Utility::web_error($sql, __FILE__, __LINE__);

    list($sort) = $xoopsDB->fetchRow($result);

    return ++$sort;
}

//更新tad_book3某一筆資料
function update_tad_book3($tbsn = '')
{
    global $xoopsDB, $tad_book3_adm;
    if (!$tad_book3_adm) {
        $book = get_tad_book3($tbsn);
        if (!chk_edit_power($book['author'])) {
            header('location:index.php');
            exit;
        }
    }
    if (!empty($_POST['new_tbcsn'])) {
        $tbcsn = add_tad_book3_cate();
    } else {
        $tbcsn = $_POST['tbcsn'];
    }

    if (!empty($_POST['author_str'])) {
        $author = $_POST['author_str'];
    } else {
        $author = implode(',', $_POST['author']);
    }
    $title = $_POST['title'];
    $description = $_POST['description'];
    $description = Wcag::amend($description);
    $passwd = $_POST['passwd'];
    $enable = $_POST['enable'];
    $sort = (int) $_POST['sort'];

    $read_group = (in_array('', $_POST['read_group'])) ? '' : implode(',', $_POST['read_group']);
    $video_group = (in_array('', $_POST['video_group'])) ? '' : implode(',', $_POST['video_group']);

    $sql = 'UPDATE `' . $xoopsDB->prefix('tad_book3') . '` SET `tbcsn` = ?, `sort` = ?, `title` = ?, `description` = ?, `author` = ?, `read_group` = ?, `video_group` = ?, `passwd` = ?, `enable` = ? WHERE `tbsn` = ?';
    Utility::query($sql, 'iisssssssi', [$tbcsn, $sort, $title, $description, $author, $read_group, $video_group, $passwd, $enable, $tbsn]) or Utility::web_error($sql, __FILE__, __LINE__);

    if (!empty($_FILES['pic_name']['name'])) {
        mk_thumb($tbsn, 'pic_name', 120);
    }

    return $tbsn;
}

//自動取得新排序
function get_max_doc_sort($tbcsn = '')
{
    global $xoopsDB;
    $sql = 'SELECT MAX(`sort`) FROM `' . $xoopsDB->prefix('tad_book3') . '` WHERE `tbcsn`=?';
    $result = Utility::query($sql, 'i', [$tbcsn]) or Utility::web_error($sql, __FILE__, __LINE__);

    list($sort) = $xoopsDB->fetchRow($result);

    return ++$sort;
}

//縮圖上傳
function mk_thumb($tbsn = '', $col_name = '', $width = 100)
{
    global $xoopsDB, $tad_book3_adm;
    if (!$tad_book3_adm) {
        $book = get_tad_book3($tbsn);
        if (!chk_edit_power($book['author'])) {
            header('location:index.php');
            exit;
        }
    }

    require_once XOOPS_ROOT_PATH . '/modules/tadtools/upload/class.upload.php';

    if (file_exists(_TADBOOK3_BOOK_DIR . "/book_{$tbsn}.png")) {
        unlink(_TADBOOK3_BOOK_DIR . "/book_{$tbsn}.png");
        unlink(_TADBOOK3_BOOK_DIR . "/fb_book_{$tbsn}.png");
    }
    $handle = new \Verot\Upload\Upload($_FILES[$col_name]);
    if ($handle->uploaded) {
        $handle->file_new_name_body = "book_{$tbsn}";
        $handle->image_convert = 'png';
        $handle->image_resize = true;
        $handle->image_x = $width;
        $handle->image_ratio_y = true;
        $handle->file_overwrite = true;
        $handle->process(_TADBOOK3_BOOK_DIR);
        $handle->auto_create_dir = true;
        if ($handle->processed) {
            $sql = 'UPDATE `' . $xoopsDB->prefix('tad_book3') . '`
            SET `pic_name` = ?
            WHERE `tbsn` = ?';

            $params = ["book_{$tbsn}.png", $tbsn];
            Utility::query($sql, 'si', $params);

        }

        $handle->file_new_name_body = "fb_book_{$tbsn}";
        $handle->image_x = 200;
        $handle->process(_TADBOOK3_BOOK_DIR);
        if ($handle->processed) {
            $handle->clean();
            return true;
        }

        die($handle->error);
    }

    return false;
}

//檢查文章密碼
function check_passwd($tbsn = '')
{
    global $xoopsDB;
    $sql = 'SELECT `passwd` FROM `' . $xoopsDB->prefix('tad_book3') . '` WHERE `tbsn` =?';
    $result = Utility::query($sql, 'i', [$tbsn]) or Utility::web_error($sql, __FILE__, __LINE__);

    list($passwd) = $xoopsDB->fetchRow($result);
    if ($_POST['passwd'] == $passwd) {
        $_SESSION['passwd'] = $passwd;
    }
    header('location:' . XOOPS_URL . "/modules/tad_book3/index.php?op=list_docs&tbsn=$tbsn");
    exit;
}

//以流水號取得某筆tad_book3資料
function get_tad_book3($tbsn = '')
{
    global $xoopsDB;
    if (empty($tbsn)) {
        return;
    }

    $sql = 'SELECT * FROM `' . $xoopsDB->prefix('tad_book3') . '` WHERE `tbsn` = ?';
    $result = Utility::query($sql, 'i', [$tbsn]) or Utility::web_error($sql, __FILE__, __LINE__);

    $data = $xoopsDB->fetchArray($result);

    return $data;
}

//分類選單
function cate_select($def_tbcsn = '')
{
    $all_cate = Tools::all_cate();
    $main = '';
    foreach ($all_cate as $tbcsn => $title) {
        $selected = ($tbcsn == $def_tbcsn) ? 'selected' : '';
        $main .= "<option value='$tbcsn' $selected>$title</option>";
    }

    return $main;
}

//取得所有書名
function all_books()
{
    global $xoopsDB;
    $sql = 'SELECT `tbsn`, `title` FROM `' . $xoopsDB->prefix('tad_book3') . '` ORDER BY `sort`';
    $result = Utility::query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    while (list($tbsn, $title) = $xoopsDB->fetchRow($result)) {
        $main[$tbsn] = $title;
    }

    return $main;
}

//書名選單
function book_select($book_sn = '')
{
    $all_books = all_books();
    foreach ($all_books as $tbsn => $title) {
        $selected = ($book_sn == $tbsn) ? 'selected' : '';
        $main .= "<option value=$tbsn $selected>$title</option>";
    }

    return $main;
}

//產生章節選單
function category_menu($num = '')
{
    $opt = '';
    for ($i = 0; $i <= 50; $i++) {
        $selected = ($num == $i) ? 'selected' : '';
        $opt .= "<option value='{$i}' $selected>$i</option>";
    }

    return $opt;
}

//取得前後文章
function near_docs($tbsn = '', $doc_sn = '')
{
    global $xoopsDB, $tad_book3_adm;
    $and_enable = $tad_book3_adm ? '' : 'AND `enable`=?';
    $sql = 'SELECT `tbdsn`, `title`, `category`, `page`, `paragraph`, `sort` FROM `' . $xoopsDB->prefix('tad_book3_docs') . '` WHERE `tbsn`=? AND (`content` != \'\' OR `from_tbdsn` != 0) ' . $and_enable . ' ORDER BY `category`, `page`, `paragraph`, `sort`';

    $params = $tad_book3_adm ? [$tbsn] : [$tbsn, 1];
    $format = $tad_book3_adm ? 's' : 'ss';

    $result = Utility::query($sql, $format, $params) or Utility::web_error($sql, __FILE__, __LINE__);

    $get_next = false;
    while (list($tbdsn, $title, $category, $page, $paragraph, $sort) = $xoopsDB->fetchRow($result)) {
        $doc_sort = Tools::mk_category($category, $page, $paragraph, $sort);
        if ($doc_sn == $tbdsn) {
            $doc['main'] = "{$tbdsn};{$doc_sort['main']} {$title}";
            $get_next = true;
        } elseif ($get_next) {
            $doc['next'] = "{$tbdsn};{$doc_sort['main']} {$title}";

            return $doc;
            break;
        } else {
            $doc['prev'] = "{$tbdsn};{$doc_sort['main']} {$title}";
        }
    }

    return $doc;
}

//文章選單
function doc_select($tbsn = '', $doc_sn = '')
{
    global $xoopsDB, $xoopsUser;

    if (empty($xoopsUser)) {
        $andenable = "AND `enable` = '1'";
        $now_uid = 0;
    } else {
        $andenable = '';
        $now_uid = $xoopsUser->uid();
    }

    $sql = 'SELECT `tbdsn`, `title`, `category`, `page`, `paragraph`, `sort`, `enable`, `uid`
            FROM `' . $xoopsDB->prefix('tad_book3_docs') . '`
            WHERE `tbsn` = ? ' . $andenable . '
            ORDER BY `category`, `page`, `paragraph`, `sort`';

    $params = [$tbsn];
    $result = Utility::query($sql, 's', $params) or Utility::web_error($sql, __FILE__, __LINE__);

    $main = '';
    while (list($tbdsn, $title, $category, $page, $paragraph, $sort, $enable, $uid) = $xoopsDB->fetchRow($result)) {
        $selected = ($doc_sn == $tbdsn) ? 'selected' : '';
        $doc_sort = Tools::mk_category($category, $page, $paragraph, $sort);

        $stat = '';
        if ('1' != $enable) {
            if ($now_uid != $uid) {
                continue;
            }
            $style = " style='color:gray;'";
            $stat = '[' . _MD_TADBOOK3_UNABLE . '] ';
        } else {
            $style = " style='color:black;'";
        }

        $main .= "<option value=$tbdsn $selected $style>" . str_repeat('&nbsp;', ($doc_sort['level'] - 1) * 2) . "{$doc_sort['main']} {$stat}{$title}</option>";
    }

    return $main;
}

function decode_category($doc_sort = '')
{
    if (mb_strpos($doc_sort, '-')) {
        $doc_sort_arr = explode('-', $doc_sort);
        $all['category'] = isset($doc_sort_arr[0]) ? $doc_sort_arr[0] : '';
        $all['page'] = isset($doc_sort_arr[1]) ? $doc_sort_arr[1] : '';
        $all['paragraph'] = isset($doc_sort_arr[2]) ? $doc_sort_arr[2] : '';
        $all['sort'] = isset($doc_sort_arr[3]) ? $doc_sort_arr[3] : '';
    } else {
        $all['category'] = str_replace('.', '', $doc_sort);
        $all['page'] = '';
        $all['paragraph'] = '';
        $all['sort'] = '';
    }

    return $all;
}

//判斷本文是否允許該用戶編輯
function chk_edit_power($uid_txt = '')
{
    global $xoopsDB, $xoopsUser;
    if (empty($uid_txt)) {
        return false;
    }

    //取得目前使用者的所屬群組
    if ($xoopsUser) {
        $user_id = $xoopsUser->uid();
    } else {
        $user_id = [];
    }

    $uid_arr = explode(',', $uid_txt);
    foreach ($uid_arr as $uid) {
        $uid_arr[] = (int) $uid;
    }

    if (in_array($user_id, $uid_arr)) {
        return true;
    }

    return false;
}

/********************* 預設函數 ********************
 * @param string $tbdsn
 */
//刪除tad_book3_docs某筆資料資料
function delete_tad_book3_docs($tbdsn = '')
{
    global $xoopsDB;
    check_update_cpps_del($tbdsn);
    $sql = 'DELETE FROM `' . $xoopsDB->prefix('tad_book3_docs') . '` WHERE `tbdsn`=?';
    Utility::query($sql, 'i', [$tbdsn]) or Utility::web_error($sql, __FILE__, __LINE__);

}

//檢查是否有相同的章節數，若有其他章節往前移動（刪除之意）
function check_update_cpps_del($tbdsn = 0)
{
    global $xoopsDB, $tad_book3_adm;

    $sql = 'SELECT `tbsn`, `category`, `page`, `paragraph`, `sort`, `uid` FROM `' . $xoopsDB->prefix('tad_book3_docs') . '` WHERE `tbdsn`=?';
    $result = Utility::query($sql, 'i', [$tbdsn]) or Utility::web_error($sql, __FILE__, __LINE__);

    list($tbsn, $category, $page, $paragraph, $sort, $uid) = $xoopsDB->fetchRow($result);

    if (!$tad_book3_adm) {
        if (!chk_edit_power($uid)) {
            header('location:index.php');
            exit;
        }
    }
    $updateField = '';
    $whereConditions = ["`tbsn` = ?"];
    $params = [$tbsn];

    if (!empty($category)) {
        if (!empty($page)) {
            if (!empty($paragraph)) {
                if (!empty($sort)) {
                    $updateField = '`sort` = `sort` - 1';
                    $whereConditions[] = "`category` = ? AND `page` = ? AND `paragraph` = ? AND `sort` > ?";
                    $params = array_merge($params, [$category, $page, $paragraph, $sort]);
                } else {
                    $updateField = '`paragraph` = `paragraph` - 1';
                    $whereConditions[] = "`category` = ? AND `page` = ? AND `paragraph` > ?";
                    $params = array_merge($params, [$category, $page, $paragraph]);
                }
            } else {
                $updateField = '`page` = `page` - 1';
                $whereConditions[] = "`category` = ? AND `page` > ?";
                $params = array_merge($params, [$category, $page]);
            }
        } else {
            $updateField = '`category` = `category` - 1';
            $whereConditions[] = "`category` > ?";
            $params[] = $category;
        }
    }

    if ($updateField) {
        $sql = 'UPDATE `' . $xoopsDB->prefix('tad_book3_docs') . '`
                SET ' . $updateField . '
                WHERE ' . implode(' AND ', $whereConditions);

        $result = Utility::query($sql, str_repeat('i', count($params)), $params) or Utility::web_error($sql, __FILE__, __LINE__);
    }

}

//刪除tad_book3 某筆資料資料
function delete_tad_book3($tbsn = '')
{
    global $xoopsDB, $tad_book3_adm;

    if (!$tad_book3_adm) {
        $book = get_tad_book3($tbsn);
        if (!chk_edit_power($book['author'])) {
            header('location:index.php');
            exit;
        }
    }

    $sql = 'DELETE FROM `' . $xoopsDB->prefix('tad_book3_docs') . '` WHERE `tbsn`=?';
    Utility::query($sql, 'i', [$tbsn]) or Utility::web_error($sql, __FILE__, __LINE__);

    //先刪除底下所有連結
    $sql = 'DELETE FROM `' . $xoopsDB->prefix('tad_book3') . '` WHERE `tbsn`=?';
    Utility::query($sql, 'i', [$tbsn]) or Utility::web_error($sql, __FILE__, __LINE__);

}

//刪除tad_book3_cate某筆資料資料
function delete_tad_book3_cate($tbcsn = '')
{
    global $xoopsDB;
    //先刪除底下所有連結
    $sql = 'DELETE FROM `' . $xoopsDB->prefix('tad_book3') . '` WHERE `tbcsn` = ?';
    Utility::query($sql, 'i', [$tbcsn]) or Utility::web_error($sql, __FILE__, __LINE__);

    $sql = 'DELETE FROM `' . $xoopsDB->prefix('tad_book3_cate') . '` WHERE `tbcsn` = ?';
    Utility::query($sql, 'i', [$tbcsn]) or Utility::web_error($sql, __FILE__, __LINE__);

}

function secondsToTime($seconds = 0)
{
    $seconds = (int) $seconds;
    if (empty($seconds)) {
        return;
    }

    $s = sprintf("%02d", $seconds % 60);
    $m = sprintf("%02d", floor(($seconds % 3600) / 60));
    $h = sprintf("%02d", floor(($seconds % 86400) / 3600));
    $d = sprintf("%02d", floor(($seconds % 2592000) / 86400));
    $M = sprintf("%02d", floor($seconds / 2592000));

    $MM = $M != '00' ? "$M 月" : '';
    $dd = $d != '00' ? "$d 日" : '';
    $hh = $h != '00' ? "$h 時" : '';
    $mm = $m != '00' ? "$m 分" : '';
    $ss = $s != '00' ? "$s 秒" : '';

    return "{$MM}{$dd}{$hh}{$mm}{$ss}";
}
