<?php
//引入TadTools的函式庫
if (!file_exists(XOOPS_ROOT_PATH . "/modules/tadtools/tad_function.php")) {
    redirect_header("http://campus-xoops.tn.edu.tw/modules/tad_modules/index.php?module_sn=1", 3, _TAD_NEED_TADTOOLS);
}
include_once XOOPS_ROOT_PATH . "/modules/tadtools/tad_function.php";

define("_TADBOOK3_BOOK_DIR", XOOPS_ROOT_PATH . "/uploads/tad_book3");
define("_TADBOOK3_BOOK_URL", XOOPS_URL . "/uploads/tad_book3");

//取得路徑
function get_tad_book3_cate_path($the_tbcsn = "", $include_self = true)
{
    global $xoopsDB;

    $arr[0]['tbcsn'] = "0";
    $arr[0]['title'] = "<i class='fa fa-home'></i>";
    $arr[0]['sub']   = get_tad_book3_sub_cate(0);
    if (!empty($the_tbcsn)) {

        $tbl = $xoopsDB->prefix("tad_book3_cate");
        $sql = "SELECT t1.tbcsn AS lev1, t2.tbcsn as lev2, t3.tbcsn as lev3, t4.tbcsn as lev4, t5.tbcsn as lev5, t6.tbcsn as lev6, t7.tbcsn as lev7
            FROM `{$tbl}` t1
            LEFT JOIN `{$tbl}` t2 ON t2.of_tbsn = t1.tbcsn
            LEFT JOIN `{$tbl}` t3 ON t3.of_tbsn = t2.tbcsn
            LEFT JOIN `{$tbl}` t4 ON t4.of_tbsn = t3.tbcsn
            LEFT JOIN `{$tbl}` t5 ON t5.of_tbsn = t4.tbcsn
            LEFT JOIN `{$tbl}` t6 ON t6.of_tbsn = t5.tbcsn
            LEFT JOIN `{$tbl}` t7 ON t7.of_tbsn = t6.tbcsn
            WHERE t1.of_tbsn = '0'";
        $result = $xoopsDB->query($sql) or web_error($sql);
        while ($all = $xoopsDB->fetchArray($result)) {
            if (in_array($the_tbcsn, $all)) {
                //$main.="-";
                foreach ($all as $tbcsn) {
                    if (!empty($tbcsn)) {
                        if (!$include_self and $tbcsn == $the_tbcsn) {
                            break;
                        }
                        $arr[$tbcsn]        = get_tad_book3_cate($tbcsn);
                        $arr[$tbcsn]['sub'] = get_tad_book3_sub_cate($tbcsn);
                        if ($tbcsn == $the_tbcsn) {
                            break;
                        }
                    }
                }
                //$main.="<br>";
                break;
            }
        }
    }
    return $arr;
}

function get_tad_book3_sub_cate($tbcsn = "0")
{
    global $xoopsDB;
    $sql       = "select tbcsn,title from " . $xoopsDB->prefix("tad_book3_cate") . " where of_tbsn='{$tbcsn}'";
    $result    = $xoopsDB->query($sql) or web_error($sql);
    $tbcsn_arr = "";
    while (list($tbcsn, $title) = $xoopsDB->fetchRow($result)) {
        $tbcsn_arr[$tbcsn] = $title;
    }
    return $tbcsn_arr;
}

//以流水號取得某筆tad_book3_cate資料
function get_tad_book3_cate($tbcsn = "")
{
    global $xoopsDB;
    if (empty($tbcsn)) {
        return;
    }
    $counter       = tad_book3_cate_count();
    $sql           = "select * from " . $xoopsDB->prefix("tad_book3_cate") . " where tbcsn='$tbcsn'";
    $result        = $xoopsDB->query($sql) or web_error($sql);
    $data          = $xoopsDB->fetchArray($result);
    $data['count'] = $counter[$tbcsn];
    return $data;
}

//以流水號取得某筆tad_book3_docs資料
function get_tad_book3_docs($tbdsn = "")
{
    global $xoopsDB;
    if (empty($tbdsn)) {
        return;
    }

    $sql    = "select * from " . $xoopsDB->prefix("tad_book3_docs") . " where tbdsn='$tbdsn'";
    $result = $xoopsDB->query($sql) or web_error($sql);
    $data   = $xoopsDB->fetchArray($result);
    return $data;
}

//分類底下的書籍數
function tad_book3_cate_count()
{
    global $xoopsDB;
    $sql    = "select tbcsn,count(*) from " . $xoopsDB->prefix("tad_book3") . " group by tbcsn";
    $result = $xoopsDB->query($sql) or web_error($sql);
    while (list($tbcsn, $count) = $xoopsDB->fetchRow($result)) {
        $all[$tbcsn] = (int) ($count);
    }

    return $all;
}

//秀出所有分類及書籍
function list_all_cate_book($isAdmin = "")
{
    global $xoopsDB, $xoopsTpl, $xoopsUser;

    $i      = 0;
    $sql    = "select * from  " . $xoopsDB->prefix("tad_book3_cate") . " order by sort";
    $result = $xoopsDB->query($sql) or web_error($sql);
    while ($data = $xoopsDB->fetchArray($result)) {
        $cate[$i] = $data;

        $sql     = "select * from  " . $xoopsDB->prefix("tad_book3") . " where tbcsn='{$data['tbcsn']}' and enable='1' order by sort";
        $result2 = $xoopsDB->query($sql) or web_error($sql);
        $j       = 0;
        $books   = "";
        while ($data2 = $xoopsDB->fetchArray($result2)) {
            if (!chk_power($data2['read_group'])) {
                continue;
            }
            $books[$j] = book_shadow($data2);
            $j++;
        }
        $cate[$i]['books'] = $books;
        $i++;
    }
    //die(var_export($cate));
    $xoopsTpl->assign('jquery', get_jquery(true));
    $xoopsTpl->assign('cate', $cate);

    //刪除書籍
    if (!file_exists(XOOPS_ROOT_PATH . "/modules/tadtools/sweet_alert.php")) {
        redirect_header("index.php", 3, _MA_NEED_TADTOOLS);
    }
    include_once XOOPS_ROOT_PATH . "/modules/tadtools/sweet_alert.php";
    $sweet_alert_book      = new sweet_alert();
    $sweet_alert_book_code = $sweet_alert_book->render("delete_tad_book3_func", "admin/main.php?op=delete_tad_book3&tbsn=", 'tbsn');
    $xoopsTpl->assign('sweet_alert_book_code', $sweet_alert_book_code);
}

//book陰影
function book_shadow($books = array())
{
    global $xoopsUser;

    if ($xoopsUser) {
        $uid = $xoopsUser->uid();
    } else {
        $uid = 0;
    }
    $authors       = explode(',', $books['author']);
    $tool          = ((!empty($uid) and in_array($uid, $authors)) or $isAdmin) ? true : false;
    $books['tool'] = $tool;

    $pic                  = (empty($books['pic_name'])) ? XOOPS_URL . "/modules/tad_book3/images/blank.png" : _TADBOOK3_BOOK_URL . "/{$books['pic_name']}";
    $books['pic']         = $pic;
    $description          = strip_tags($description);
    $books['description'] = $description;

    return $books;
}

//列出某書資料
function list_docs($def_tbsn = "")
{
    global $xoopsDB, $xoopsUser, $xoopsModule, $xoopsTpl;

    if ($xoopsUser) {
        $uid = $xoopsUser->uid();
    } else {
        $uid = 0;
    }

    add_book_counter($def_tbsn);

    $xoopsTpl->assign('now_op', 'list_docs');

    $all_cate = all_cate();

    $sql    = "select * from " . $xoopsDB->prefix("tad_book3") . " where tbsn='$def_tbsn' and enable='1'";
    $result = $xoopsDB->query($sql) or web_error($sql);

    $data = $xoopsDB->fetchArray($result);
    foreach ($data as $k => $v) {
        $$k = $v;
    }

    if (!chk_power($read_group)) {
        header("location:index.php");
        exit;
    }

    $needpasswd = 0;
    if (!empty($passwd) and $_SESSION['passwd'] != $passwd) {
        $needpasswd = 1;
    }

    $enable_txt = ($enable == '1') ? _MD_TADBOOK3_ENABLE : _MD_TADBOOK3_UNABLE;

    $read_group = txt_to_group_name($read_group, _MD_TADBOOK3_ALL_OPEN);

    //共同編輯者
    $author_arr = explode(",", $author);
    $my         = in_array($uid, $author_arr);
    $xoopsTpl->assign('my', $my);
    foreach ($author_arr as $uid) {
        $uidname    = XoopsUser::getUnameFromId($uid, 1);
        $uidname    = (empty($uidname)) ? XoopsUser::getUnameFromId($uid, 0) : $uidname;
        $uid_name[] = $uidname;
    }
    $author   = implode(" , ", $uid_name);
    $uid_name = "";

    $create_date = date("Y-m-d H:i:s", xoops_getUserTimestamp(strtotime($create_date)));

    $cate = (empty($all_cate[$tbcsn])) ? _MD_TADBOOK3_NOT_CLASSIFIED : $all_cate[$tbcsn];

    $book = book_shadow($data);

    $xoopsTpl->assign('book', $book);
    $xoopsTpl->assign('tbsn', $def_tbsn);
    $xoopsTpl->assign('cate', $cate);
    $xoopsTpl->assign('title', $title);
    $xoopsTpl->assign('description', $description);
    $xoopsTpl->assign('sort', $sort);
    $xoopsTpl->assign('read_group', $read_group);
    $xoopsTpl->assign('author', $author);
    $xoopsTpl->assign('passwd', $passwd);
    $xoopsTpl->assign('needpasswd', $needpasswd);
    $xoopsTpl->assign('enable', $enable);
    $xoopsTpl->assign('enable_txt', $enable_txt);
    $xoopsTpl->assign('counter', $counter);
    $xoopsTpl->assign('create_date', $create_date);
    $xoopsTpl->assign('push_url', push_url());
    $xoopsTpl->assign("book_content", sprintf(_MD_TADBOOK3_BOOK_CONTENT, $title));

    $xoopsTpl->assign('xoops_pagetitle', $title);
    $xoopsTpl->assign("fb_description", strip_tags($description));
    $xoopsTpl->assign("logo_img", $book['pic']);

    $i      = 0;
    $docs   = "";
    $sql    = "select * from " . $xoopsDB->prefix("tad_book3_docs") . " where tbsn='{$tbsn}' order by category,page,paragraph,sort";
    $result = $xoopsDB->query($sql) or web_error($sql);
    while (list($tbdsn, $tbsn, $category, $page, $paragraph, $sort, $title, $content, $add_date, $last_modify_date, $uid, $count, $enable) = $xoopsDB->fetchRow($result)) {
        $doc_sort         = mk_category($category, $page, $paragraph, $sort);
        $last_modify_date = date("Y-m-d H:i:s", xoops_getUserTimestamp($last_modify_date));

        if ($enable != '1' and !$my) {
            continue;
        }

        $enable_txt = ($enable == '1') ? "" : "[" . _MD_TADBOOK3_UNABLE . "] ";

        $docs[$i]['tbdsn']            = $tbdsn;
        $docs[$i]['last_modify_date'] = $last_modify_date;
        $docs[$i]['doc_sort_level']   = $doc_sort['level'];
        $docs[$i]['doc_sort_main']    = $doc_sort['main'];
        $docs[$i]['title']            = $title;
        $docs[$i]['count']            = $count;
        $docs[$i]['enable']           = $enable;
        $docs[$i]['enable_txt']       = $enable_txt;
        $i++;
    }

    $xoopsTpl->assign('docs', $docs);

    //刪除書籍
    if (!file_exists(XOOPS_ROOT_PATH . "/modules/tadtools/sweet_alert.php")) {
        redirect_header("index.php", 3, _MA_NEED_TADTOOLS);
    }
    include_once XOOPS_ROOT_PATH . "/modules/tadtools/sweet_alert.php";
    $sweet_alert_book      = new sweet_alert();
    $sweet_alert_book_code = $sweet_alert_book->render("delete_tad_book3_func", "admin/main.php?op=delete_tad_book3&tbsn=", 'tbsn');
    $xoopsTpl->assign('sweet_alert_book_code', $sweet_alert_book_code);

    $sweet_alert_docs      = new sweet_alert();
    $sweet_alert_docs_code = $sweet_alert_docs->render("delete_tad_book3_docs_func", "index.php?op=delete_tad_book3_docs&tbsn={$def_tbsn}&tbdsn=", 'tbdsn');
    $xoopsTpl->assign('sweet_alert_docs_code', $sweet_alert_docs_code);
}

//tad_book3編輯表單
function tad_book3_form($tbsn = "")
{
    global $xoopsDB, $xoopsUser, $xoopsTpl;
    include_once XOOPS_ROOT_PATH . "/class/xoopsformloader.php";

    //抓取預設值
    if (!empty($tbsn)) {
        $DBV = get_tad_book3($tbsn);
    } else {
        $DBV = array();
    }

    //預設值設定

    $tbsn        = (!isset($DBV['tbsn'])) ? "" : $DBV['tbsn'];
    $tbcsn       = (!isset($DBV['tbcsn'])) ? "" : $DBV['tbcsn'];
    $sort        = (!isset($DBV['sort'])) ? get_max_doc_sort($tbcsn) : $DBV['sort'];
    $title       = (!isset($DBV['title'])) ? "" : $DBV['title'];
    $description = (!isset($DBV['description'])) ? "" : $DBV['description'];
    $author      = (!isset($DBV['author'])) ? "" : $DBV['author'];
    $read_group  = (!isset($DBV['read_group'])) ? "" : $DBV['read_group'];
    $passwd      = (!isset($DBV['passwd'])) ? "" : $DBV['passwd'];
    $enable      = (!isset($DBV['enable'])) ? "1" : $DBV['enable'];
    $pic_name    = (!isset($DBV['pic_name'])) ? "" : $DBV['pic_name'];
    $counter     = (!isset($DBV['counter'])) ? "" : $DBV['counter'];
    $create_date = (!isset($DBV['create_date'])) ? "" : $DBV['create_date'];

    if (!file_exists(XOOPS_ROOT_PATH . "/modules/tadtools/ck.php")) {
        redirect_header("http://campus-xoops.tn.edu.tw/modules/tad_modules/index.php?module_sn=1", 3, _TAD_NEED_TADTOOLS);
    }
    include_once XOOPS_ROOT_PATH . "/modules/tadtools/ck.php";
    $ck = new CKEditor("tad_book3", "description", $description);
    $ck->setHeight(400);
    $editor = $ck->render();

    $author_arr = (empty($author)) ? array($xoopsUser->getVar("uid")) : explode(",", $author);

    $cate_select = cate_select($tbcsn);

    $member_handler = xoops_gethandler('member');
    $usercount      = $member_handler->getUserCount(new Criteria('level', 0, '>'));

    if ($usercount < 1000) {

        $select = new XoopsFormSelect('', 'author', $author_arr, 5, true);
        $select->setExtra("class='form-control'");
        $member_handler = xoops_gethandler('member');
        $criteria       = new CriteriaCompo();
        $criteria->setSort('uname');
        $criteria->setOrder('ASC');
        $criteria->setLimit(1000);
        $criteria->setStart(0);

        $select->addOptionArray($member_handler->getUserList($criteria));
        $user_menu = $select->render();
    } else {
        $user_menu = "<textarea name='author_str' style='width:100%;'>$author</textarea>
    <div>user uid, ex:\"1,27,103\"</div>";
    }

    $group_arr   = (empty($read_group)) ? array("") : explode(",", $read_group);
    $SelectGroup = new XoopsFormSelectGroup("", "read_group", false, $group_arr, 5, true);
    $SelectGroup->setExtra("class='form-control'");
    $SelectGroup->addOption("", _MD_TADBOOK3_ALL_OPEN, false);
    $group_menu = $SelectGroup->render();

    $op = (empty($tbsn)) ? "insert_tad_book3" : "update_tad_book3";

    $xoopsTpl->assign('action', $_SERVER['PHP_SELF']);
    $xoopsTpl->assign('tbsn', $tbsn);
    $xoopsTpl->assign('cate_select', $cate_select);
    $xoopsTpl->assign('sort', $sort);
    $xoopsTpl->assign('title', $title);
    $xoopsTpl->assign('editor', $editor);
    $xoopsTpl->assign('user_menu', $user_menu);
    $xoopsTpl->assign('group_menu', $group_menu);
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
        $tbcsn = $_POST['tbcsn'];
    }

    if (!empty($_POST['author_str'])) {
        $author = $_POST['author_str'];
    } else {
        $author = implode(",", $_POST['author']);
    }

    $myts                 = MyTextSanitizer::getInstance();
    $_POST['title']       = $myts->addSlashes($_POST['title']);
    $_POST['description'] = $myts->addSlashes($_POST['description']);

    $read_group = (in_array("", $_POST['read_group'])) ? "" : implode(",", $_POST['read_group']);
    $now        = date("Y-m-d H:i:s", xoops_getUserTimestamp(time()));
    $sql        = "insert into " . $xoopsDB->prefix("tad_book3") . " (`tbcsn`,`sort`,`title`,`description`,`author`,`read_group`,`passwd`,`enable`,`pic_name`,`counter`,`create_date`) values('{$tbcsn}','{$_POST['sort']}','{$_POST['title']}','{$_POST['description']}','{$author}','{$read_group}','{$_POST['passwd']}','{$_POST['enable']}','{$_POST['pic_name']}','{$_POST['counter']}','{$now}')";
    $xoopsDB->query($sql) or web_error($sql);
    //取得最後新增資料的流水編號
    $tbsn = $xoopsDB->getInsertId();

    if (!empty($_FILES['pic_name']['name'])) {
        mk_thumb($tbsn, "pic_name", 120);
    }

    return $tbsn;
}

//新增資料到tad_book3_cate中
function add_tad_book3_cate()
{
    global $xoopsDB, $xoopsModuleConfig;
    if (empty($_POST['new_tbcsn'])) {
        return;
    }

    $myts  = MyTextSanitizer::getInstance();
    $title = $myts->addSlashes($_POST['new_tbcsn']);
    $sort  = tad_book3_cate_max_sort();
    $sql   = "insert into " . $xoopsDB->prefix("tad_book3_cate") . " (`of_tbsn`,`sort`,`title`) values('0','{$sort}','{$title}')";
    $xoopsDB->query($sql) or web_error($sql);
    //取得最後新增資料的流水編號
    $tbcsn = $xoopsDB->getInsertId();
    return $tbcsn;
}

//自動取得新排序
function tad_book3_cate_max_sort()
{
    global $xoopsDB, $xoopsModule;
    $sql        = "select max(sort) from " . $xoopsDB->prefix("tad_book3_cate") . " where of_tbsn=''";
    $result     = $xoopsDB->query($sql) or web_error($sql);
    list($sort) = $xoopsDB->fetchRow($result);
    return ++$sort;
}

//更新tad_book3某一筆資料
function update_tad_book3($tbsn = "")
{
    global $xoopsDB;

    if (!empty($_POST['new_tbcsn'])) {
        $tbcsn = add_tad_book3_cate();
    } else {
        $tbcsn = $_POST['tbcsn'];
    }

    if (!empty($_POST['author_str'])) {
        $author = $_POST['author_str'];
    } else {
        $author = implode(",", $_POST['author']);
    }

    $myts                 = MyTextSanitizer::getInstance();
    $_POST['title']       = $myts->addSlashes($_POST['title']);
    $_POST['description'] = $myts->addSlashes($_POST['description']);

    $read_group = (in_array("", $_POST['read_group'])) ? "" : implode(",", $_POST['read_group']);
    $sql        = "update " . $xoopsDB->prefix("tad_book3") . " set  `tbcsn` = '{$tbcsn}', `sort` = '{$_POST['sort']}', `title` = '{$_POST['title']}', `description` = '{$_POST['description']}', `author` = '{$author}', `read_group` = '{$read_group}', `passwd` = '{$_POST['passwd']}', `enable` = '{$_POST['enable']}' where tbsn='$tbsn'";
    $xoopsDB->queryF($sql) or web_error($sql);

    if (!empty($_FILES['pic_name']['name'])) {
        mk_thumb($tbsn, "pic_name", 120);
    }
    return $tbsn;
}

//自動取得新排序
function get_max_doc_sort($tbcsn = "")
{
    global $xoopsDB, $xoopsModule;
    $sql        = "select max(sort) from " . $xoopsDB->prefix("tad_book3") . " where tbcsn='{$tbcsn}'";
    $result     = $xoopsDB->query($sql) or web_error($sql);
    list($sort) = $xoopsDB->fetchRow($result);
    return ++$sort;
}

//縮圖上傳
function mk_thumb($tbsn = "", $col_name = "", $width = 100)
{
    global $xoopsDB;
    include_once XOOPS_ROOT_PATH . "/modules/tadtools/upload/class.upload.php";

    if (file_exists(_TADBOOK3_BOOK_DIR . "/book_{$tbsn}.png")) {
        unlink(_TADBOOK3_BOOK_DIR . "/book_{$tbsn}.png");
    }
    $handle = new upload($_FILES[$col_name]);
    if ($handle->uploaded) {
        $handle->file_new_name_body = "book_{$tbsn}";
        $handle->image_convert      = 'png';
        $handle->image_resize       = true;
        $handle->image_x            = $width;
        $handle->image_ratio_y      = true;
        $handle->file_overwrite     = true;
        $handle->process(_TADBOOK3_BOOK_DIR);
        $handle->auto_create_dir = true;
        if ($handle->processed) {
            $handle->clean();
            $sql = "update " . $xoopsDB->prefix("tad_book3") . " set pic_name = 'book_{$tbsn}.png' where tbsn='$tbsn'";
            $xoopsDB->queryF($sql);
            return true;
        } else {
            die($handle->error);
        }
    }
    return false;
}

//檢查文章密碼
function check_passwd($tbsn = "")
{
    global $xoopsDB;
    $sql          = "select passwd from " . $xoopsDB->prefix("tad_book3") . " where tbsn='$tbsn'";
    $result       = $xoopsDB->query($sql) or web_error($sql);
    list($passwd) = $xoopsDB->fetchRow($result);
    if ($_POST['passwd'] == $passwd) {
        $_SESSION['passwd'] = $passwd;
    }
    header("location:" . XOOPS_URL . "/modules/tad_book3/index.php?op=list_docs&tbsn=$tbsn");
    exit;
}

//以流水號取得某筆tad_book3資料
function get_tad_book3($tbsn = "")
{
    global $xoopsDB;
    if (empty($tbsn)) {
        return;
    }

    $sql    = "select * from " . $xoopsDB->prefix("tad_book3") . " where tbsn='$tbsn'";
    $result = $xoopsDB->query($sql) or web_error($sql);
    $data   = $xoopsDB->fetchArray($result);
    return $data;
}

//取得所有分類
function all_cate()
{
    global $xoopsDB, $xoopsModule;
    $sql    = "select tbcsn,title from " . $xoopsDB->prefix("tad_book3_cate") . " order by sort";
    $result = $xoopsDB->query($sql) or web_error($sql);
    while (list($tbcsn, $title) = $xoopsDB->fetchRow($result)) {
        $main[$tbcsn] = $title;
    }
    return $main;
}

//分類選單
function cate_select($def_tbcsn = "")
{
    $all_cate = all_cate();
    $main     = "";
    foreach ($all_cate as $tbcsn => $title) {
        $selected = ($tbcsn == $def_tbcsn) ? "selected" : "";
        $main .= "<option value='$tbcsn' $selected>$title</option>";
    }
    return $main;
}

//取得所有書名
function all_books()
{
    global $xoopsDB, $xoopsModule;
    $sql    = "select tbsn,title from " . $xoopsDB->prefix("tad_book3") . " order by sort";
    $result = $xoopsDB->query($sql) or web_error($sql);
    while (list($tbsn, $title) = $xoopsDB->fetchRow($result)) {
        $main[$tbsn] = $title;
    }
    return $main;
}

//書名選單
function book_select($book_sn = "")
{
    $all_books = all_books();
    foreach ($all_books as $tbsn => $title) {
        $selected = ($book_sn == $tbsn) ? "selected" : "";
        $main .= "<option value=$tbsn $selected>$title</option>";
    }
    return $main;
}

//產生章節選單
function category_menu($num = "")
{
    $opt = "";
    for ($i = 0; $i <= 50; $i++) {
        $selected = ($num == $i) ? "selected" : "";
        $opt .= "<option value='{$i}' $selected>$i</option>";
    }
    return $opt;
}

//取得前後文章
function near_docs($tbsn = "", $doc_sn = "")
{
    global $xoopsDB, $isAdmin;
    $and_enable = $isAdmin ? "" : "and enable='1'";
    $sql        = "select tbdsn,title,category,page,paragraph,sort from " . $xoopsDB->prefix("tad_book3_docs") . " where tbsn='$tbsn' $and_enable order by category,page,paragraph,sort";
    $result     = $xoopsDB->query($sql) or web_error($sql);
    $get_next   = false;
    while (list($tbdsn, $title, $category, $page, $paragraph, $sort) = $xoopsDB->fetchRow($result)) {
        $doc_sort = mk_category($category, $page, $paragraph, $sort);
        if ($doc_sn == $tbdsn) {
            $doc['main'] = "{$tbdsn};{$doc_sort['main']} {$title}";
            $get_next    = true;
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
function doc_select($tbsn = "", $doc_sn = "")
{
    global $xoopsDB, $xoopsUser;

    if (empty($xoopsUser)) {
        $andenable = " and `enable`='1'";
        $now_uid   = 0;
    } else {
        $andenable = "";
        $now_uid   = $xoopsUser->uid();
    }

    $main = "";

    $sql    = "select tbdsn,title,category,page,paragraph,sort,enable,uid from " . $xoopsDB->prefix("tad_book3_docs") . " where tbsn='$tbsn' $andenable order by category,page,paragraph,sort";
    $result = $xoopsDB->query($sql) or web_error($sql);
    while (list($tbdsn, $title, $category, $page, $paragraph, $sort, $enable, $uid) = $xoopsDB->fetchRow($result)) {
        $selected = ($doc_sn == $tbdsn) ? "selected" : "";
        $doc_sort = mk_category($category, $page, $paragraph, $sort);

        $stat = '';
        if ($enable != '1') {
            if ($now_uid != $uid) {
                continue;
            } else {
                $style = " style='color:gray;'";
                $stat  = "[" . _MD_TADBOOK3_UNABLE . "] ";
            }
        } else {
            $style = " style='color:black;'";
        }
        $main .= "<option value=$tbdsn $selected $style>" . str_repeat("&nbsp;", ($doc_sort['level'] - 1) * 2) . "{$doc_sort['main']} {$stat}{$title}</option>";
    }
    return $main;
}

//章節格式化
function mk_category($category = "", $page = "", $paragraph = "", $sort = "")
{
    if (!empty($sort)) {
        $main  = "{$category}-${page}-{$paragraph}-{$sort}";
        $level = 4;
    } elseif (!empty($paragraph)) {
        $main  = "{$category}-${page}-{$paragraph}";
        $level = 3;
    } elseif (!empty($page)) {
        $main  = "{$category}-${page}";
        $level = 2;
    } elseif (!empty($category)) {
        $main  = "{$category}.";
        $level = 1;
    } else {
        $main  = "";
        $level = 0;
    }
    $all['main']  = $main;
    $all['level'] = $level;
    return $all;
}

//判斷本文是否允許該用戶之所屬群組觀看
function chk_power($enable_group = "")
{
    global $xoopsDB, $xoopsUser;
    if (empty($enable_group)) {
        return true;
    }

    //取得目前使用者的所屬群組
    if ($xoopsUser) {
        $User_Groups = $xoopsUser->getGroups();
    } else {
        $User_Groups = array();
    }

    $news_enable_group = explode(",", $enable_group);
    foreach ($User_Groups as $gid) {
        if (in_array($gid, $news_enable_group)) {
            return true;
        }
    }
    return false;
}

//判斷本文是否允許該用戶編輯
function chk_edit_power($uid_txt = "")
{
    global $xoopsDB, $xoopsUser;
    if (empty($uid_txt)) {
        return false;
    }

    //取得目前使用者的所屬群組
    if ($xoopsUser) {
        $user_id = $xoopsUser->getVar('uid');
    } else {
        $user_id = array();
    }

    $uid_arr = explode(",", $uid_txt);

    if (in_array($user_id, $uid_arr)) {
        return true;
    }

    return false;
}

/********************* 預設函數 *********************/
//刪除tad_book3_docs某筆資料資料
function delete_tad_book3_docs($tbdsn = "")
{
    global $xoopsDB;
    $sql = "delete from " . $xoopsDB->prefix("tad_book3_docs") . " where tbdsn='$tbdsn'";
    $xoopsDB->queryF($sql) or web_error($sql);
}

//刪除tad_book3 某筆資料資料
function delete_tad_book3($tbsn = "")
{
    global $xoopsDB;

    $sql = "delete from " . $xoopsDB->prefix("tad_book3_docs") . " where tbsn='$tbsn'";
    $xoopsDB->queryF($sql) or web_error($sql);

    //先刪除底下所有連結
    $sql = "delete from " . $xoopsDB->prefix("tad_book3") . " where tbsn='$tbsn'";
    $xoopsDB->queryF($sql) or web_error($sql);
}

//刪除tad_book3_cate某筆資料資料
function delete_tad_book3_cate($tbcsn = "")
{
    global $xoopsDB;
    //先刪除底下所有連結
    $sql = "delete from " . $xoopsDB->prefix("tad_book3") . " where tbcsn='$tbcsn'";
    $xoopsDB->queryF($sql) or web_error($sql);

    $sql = "delete from " . $xoopsDB->prefix("tad_book3_cate") . " where tbcsn='$tbcsn'";
    $xoopsDB->queryF($sql) or web_error($sql);
}
