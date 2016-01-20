<?php
//tad_book3_docs編輯表單
function tad_book3_docs_form($tbdsn = "", $tbsn = "")
{
    global $xoopsDB, $xoopsUser, $xoopsModule, $xoopsTpl;
    include_once XOOPS_ROOT_PATH . "/class/xoopsformloader.php";

    if ($xoopsUser) {
        $module_id = $xoopsModule->getVar('mid');
        $isAdmin   = $xoopsUser->isAdmin($module_id);
    } else {
        $isAdmin = false;
    }

    //抓取預設值
    if (!empty($tbdsn)) {
        $DBV  = get_tad_book3_docs($tbdsn);
        $tbsn = $DBV['tbsn'];
    } else {
        $DBV = array();
    }

    if (!$isAdmin) {
        $book = get_tad_book3($tbsn);
        //die('author:'.$book['author']);
        if (!chk_edit_power($book['author'])) {
            header("location:index.php");
        }
    }

    //預設值設定

    $tbdsn            = (!isset($DBV['tbdsn'])) ? "" : $DBV['tbdsn'];
    $tbsn             = (!isset($DBV['tbsn'])) ? $tbsn : $DBV['tbsn'];
    $category         = (!isset($DBV['category'])) ? "" : $DBV['category'];
    $page             = (!isset($DBV['page'])) ? "" : $DBV['page'];
    $paragraph        = (!isset($DBV['paragraph'])) ? "" : $DBV['paragraph'];
    $sort             = (!isset($DBV['sort'])) ? "" : $DBV['sort'];
    $title            = (!isset($DBV['title'])) ? "" : $DBV['title'];
    $content          = (!isset($DBV['content'])) ? "" : $DBV['content'];
    $add_date         = (!isset($DBV['add_date'])) ? "" : $DBV['add_date'];
    $last_modify_date = (!isset($DBV['last_modify_date'])) ? "" : $DBV['last_modify_date'];
    $uid              = (!isset($DBV['uid'])) ? "" : $DBV['uid'];
    $count            = (!isset($DBV['count'])) ? "" : $DBV['count'];
    $enable           = (!isset($DBV['enable'])) ? "1" : $DBV['enable'];

    if (!file_exists(XOOPS_ROOT_PATH . "/modules/tadtools/fck.php")) {
        redirect_header("index.php", 3, _MD_NEED_TADTOOLS);
    }

    include_once XOOPS_ROOT_PATH . "/modules/tadtools/ck.php";
    $ck = new CKEditor("tad_book3", "content", $content);
    $ck->setHeight(400);
    $ck->setContentCss(XOOPS_URL . "/modules/tad_book3/reset.css");
    $ck->setContentCss(XOOPS_URL . "/modules/tad_book3/modules.css");
    $editor = $ck->render();

    $op = (empty($tbdsn)) ? "insert_tad_book3_docs" : "update_tad_book3_docs";
    //$op="replace_tad_book3_docs";
    $main = "
	$";

    $xoopsTpl->assign('action', $_SERVER['PHP_SELF']);
    $xoopsTpl->assign('syntaxhighlighter_code', $syntaxhighlighter_code);
    $xoopsTpl->assign('tbdsn', $tbdsn);
    $xoopsTpl->assign('book_select', book_select($tbsn));
    $xoopsTpl->assign('enable', $enable);
    $xoopsTpl->assign('title', $title);
    $xoopsTpl->assign('category_menu_category', category_menu($category));
    $xoopsTpl->assign('category_menu_page', category_menu($page));
    $xoopsTpl->assign('category_menu_paragraph', category_menu($paragraph));
    $xoopsTpl->assign('category_menu_sort', category_menu($sort));
    $xoopsTpl->assign('editor', $editor);
    $xoopsTpl->assign('op', $op);
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

//新增資料到tad_book3_docs中
function insert_tad_book3_docs()
{
    global $xoopsDB, $xoopsUser;
    $time = time();
    //$time=xoops_getUserTimestamp(time());

    $myts             = &MyTextSanitizer::getInstance();
    $_POST['title']   = $myts->addSlashes($_POST['title']);
    $_POST['content'] = $myts->addSlashes($_POST['content']);

    $uid = $xoopsUser->getVar('uid');
    $sql = "insert into " . $xoopsDB->prefix("tad_book3_docs") . " (`tbsn`,`category`,`page`,`paragraph`,`sort`,`title`,`content`,`add_date`,`last_modify_date`,`uid`,`count`,`enable`) values('{$_POST['tbsn']}','{$_POST['category']}','{$_POST['page']}','{$_POST['paragraph']}','{$_POST['sort']}','{$_POST['title']}','{$_POST['content']}','{$time}','{$time}','{$uid}','0','{$_POST['enable']}')";
    $xoopsDB->query($sql) or web_error($sql);
    //取得最後新增資料的流水編號
    $tbdsn = $xoopsDB->getInsertId();
    return $tbdsn;
}

//更新tad_book3_docs某一筆資料
function update_tad_book3_docs($tbdsn = "")
{
    global $xoopsDB;
    $time = time();
    //$time=xoops_getUserTimestamp(time());
    $myts             = &MyTextSanitizer::getInstance();
    $_POST['title']   = $myts->addSlashes($_POST['title']);
    $_POST['content'] = $myts->addSlashes($_POST['content']);

    $sql = "update " . $xoopsDB->prefix("tad_book3_docs") . " set  `tbsn` = '{$_POST['tbsn']}', `category` = '{$_POST['category']}', `page` = '{$_POST['page']}', `paragraph` = '{$_POST['paragraph']}', `sort` = '{$_POST['sort']}', `title` = '{$_POST['title']}', `content` = '{$_POST['content']}', `last_modify_date` = '{$time}', `enable` = '{$_POST['enable']}' where tbdsn='$tbdsn'";
    $xoopsDB->queryF($sql) or web_error($sql);
    return $tbdsn;
}
