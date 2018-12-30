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
    $from_tbdsn       = (!isset($DBV['from_tbdsn'])) ? "" : $DBV['from_tbdsn'];

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
    $xoopsTpl->assign('op', $op);
    $xoopsTpl->assign('title', $title);
    $xoopsTpl->assign('category_menu_category', category_menu($category));
    $xoopsTpl->assign('category_menu_page', category_menu($page));
    $xoopsTpl->assign('category_menu_paragraph', category_menu($paragraph));
    $xoopsTpl->assign('category_menu_sort', category_menu($sort));
    $xoopsTpl->assign('editor', $editor);
    $xoopsTpl->assign('from_tbdsn', $from_tbdsn);
}

//新增資料到tad_book3_docs中
function insert_tad_book3_docs()
{
    global $xoopsDB, $xoopsUser;
    $time = time();
    //$time=xoops_getUserTimestamp(time());

    $myts                = MyTextSanitizer::getInstance();
    $_POST['title']      = $myts->addSlashes($_POST['title']);
    $_POST['content']    = $myts->addSlashes($_POST['content']);
    $_POST['from_tbdsn'] = intval($_POST['from_tbdsn']);

    $_POST['category']  = intval($_POST['category']);
    $_POST['page']      = intval($_POST['page']);
    $_POST['paragraph'] = intval($_POST['paragraph']);
    $_POST['sort']      = intval($_POST['sort']);

    check_update_cpps_add($_POST['tbsn'], $_POST['category'], $_POST['page'], $_POST['paragraph'], $_POST['sort']);

    $uid = $xoopsUser->getVar('uid');
    $sql = "insert into " . $xoopsDB->prefix("tad_book3_docs") . " (`tbsn`,`category`,`page`,`paragraph`,`sort`,`title`,`content`,`add_date`,`last_modify_date`,`uid`,`count`,`enable`,`from_tbdsn`) values('{$_POST['tbsn']}','{$_POST['category']}','{$_POST['page']}','{$_POST['paragraph']}','{$_POST['sort']}','{$_POST['title']}','{$_POST['content']}','{$time}','{$time}','{$uid}','0','{$_POST['enable']}','{$_POST['from_tbdsn']}')";
    $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
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
    $myts                = MyTextSanitizer::getInstance();
    $_POST['title']      = $myts->addSlashes($_POST['title']);
    $_POST['content']    = $myts->addSlashes($_POST['content']);
    $_POST['from_tbdsn'] = intval($_POST['from_tbdsn']);

    $_POST['category']  = intval($_POST['category']);
    $_POST['page']      = intval($_POST['page']);
    $_POST['paragraph'] = intval($_POST['paragraph']);
    $_POST['sort']      = intval($_POST['sort']);

    check_update_cpps_add($_POST['tbsn'], $_POST['category'], $_POST['page'], $_POST['paragraph'], $_POST['sort'], $tbdsn);

    $sql = "update " . $xoopsDB->prefix("tad_book3_docs") . " set  `tbsn` = '{$_POST['tbsn']}', `category` = '{$_POST['category']}', `page` = '{$_POST['page']}', `paragraph` = '{$_POST['paragraph']}', `sort` = '{$_POST['sort']}', `title` = '{$_POST['title']}', `content` = '{$_POST['content']}', `last_modify_date` = '{$time}', `enable` = '{$_POST['enable']}', `from_tbdsn` = '{$_POST['from_tbdsn']}' where tbdsn='$tbdsn'";
    $xoopsDB->queryF($sql) or web_error($sql, __FILE__, __LINE__);
    return $tbdsn;
}

//檢查是否有相同的章節數，若有其他章節往後移動（插入之意）
function check_update_cpps_add($tbsn = 0, $category = 0, $page = 0, $paragraph = 0, $sort = 0, $tbdsn = 0)
{
    global $xoopsDB;
    $and_tbdsn   = $tbdsn ? "and `tbdsn`!='{$tbdsn}'" : "";
    $sql         = "select tbdsn from " . $xoopsDB->prefix("tad_book3_docs") . " where tbsn='{$tbsn}' and `category`='{$category}' and `page`='{$page}' and `paragraph`='{$paragraph}' and `sort`='{$sort}' {$and_tbdsn}";
    $result      = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
    list($tbdsn) = $xoopsDB->fetchRow($result);

    if (!empty($tbdsn)) {

        if (!empty($category) and !empty($page) and !empty($paragraph) and !empty($sort)) {
            $sql    = "update " . $xoopsDB->prefix("tad_book3_docs") . " set `sort` = `sort` + 1 where  tbsn='{$tbsn}' and `category` = '{$category}' and `page` = '{$page}' and `paragraph` = '{$paragraph}' and `sort` >= '{$sort}'";
            $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
        } elseif (!empty($category) and !empty($page) and !empty($paragraph) and empty($sort)) {
            $sql    = "update " . $xoopsDB->prefix("tad_book3_docs") . " set `paragraph` = `paragraph` + 1 where tbsn='{$tbsn}' and  `category` = '{$category}' and `page` = '{$page}' and `paragraph` >= '{$paragraph}'";
            $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
        } elseif (!empty($category) and !empty($page) and empty($paragraph) and empty($sort)) {
            $sql    = "update " . $xoopsDB->prefix("tad_book3_docs") . " set `page` = `page` + 1 where  tbsn='{$tbsn}' and `category` = '{$category}' and `page` >= '{$page}'";
            $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
        } elseif (!empty($category) and empty($page) and empty($paragraph) and empty($sort)) {
            $sql    = "update " . $xoopsDB->prefix("tad_book3_docs") . " set `category` = `category` + 1 where tbsn='{$tbsn}' and  `category` >= '{$category}' ";
            $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
        }

    }
}
