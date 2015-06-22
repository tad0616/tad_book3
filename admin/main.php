<?php
/*-----------引入檔案區--------------*/
$xoopsOption['template_main'] = "tadbook3_adm_main.html";
include_once "header.php";
include_once "../function.php";

/*-----------function區--------------*/
//tad_book3_cate編輯表單
function tad_book3_cate_form($tbcsn = "")
{
    global $xoopsDB, $xoopsUser, $xoopsTpl;

    //抓取預設值
    if (!empty($tbcsn)) {
        $DBV = get_tad_book3_cate($tbcsn);
    } else {
        $DBV = array();
    }

    //預設值設定

    //設定「tbcsn」欄位預設值
    $tbcsn = (!isset($DBV['tbcsn'])) ? "" : $DBV['tbcsn'];

    //設定「of_tbsn」欄位預設值
    $of_tbsn = (!isset($DBV['of_tbsn'])) ? "" : $DBV['of_tbsn'];

    //設定「title」欄位預設值
    $title = (!isset($DBV['title'])) ? "" : $DBV['title'];

    //設定「sort」欄位預設值
    $sort = (!isset($DBV['sort'])) ? tad_book3_cate_max_sort() : $DBV['sort'];

    //設定「description」欄位預設值
    $description = (!isset($DBV['description'])) ? "" : $DBV['description'];

    $op = (empty($tbcsn)) ? "insert_tad_book3_cate" : "update_tad_book3_cate";
    //$op="replace_tad_book3_cate";

    if (!file_exists(TADTOOLS_PATH . "/formValidator.php")) {
        redirect_header("index.php", 3, _MA_NEED_TADTOOLS);
    }
    include_once TADTOOLS_PATH . "/formValidator.php";
    $formValidator      = new formValidator("#myForm", true);
    $formValidator_code = $formValidator->render();

    $xoopsTpl->assign('op', 'tad_book3_cate_form');
    $xoopsTpl->assign('next_op', $op);
    $xoopsTpl->assign('tbcsn', $tbcsn);
    $xoopsTpl->assign('sort', $sort);
    $xoopsTpl->assign('title', $title);
    $xoopsTpl->assign('description', $description);
    $xoopsTpl->assign('formValidator_code', $formValidator_code);
}

//新增資料到tad_book3_cate中
function insert_tad_book3_cate()
{
    global $xoopsDB, $xoopsUser;

    $myts                 = &MyTextSanitizer::getInstance();
    $_POST['title']       = $myts->addSlashes($_POST['title']);
    $_POST['description'] = $myts->addSlashes($_POST['description']);

    $sql = "insert into " . $xoopsDB->prefix("tad_book3_cate") . "
    (`of_tbsn` , `title` , `sort` , `description`)
    values('{$_POST['of_tbsn']}' , '{$_POST['title']}' , '{$_POST['sort']}' , '{$_POST['description']}')";
    $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());

    //取得最後新增資料的流水編號
    $tbcsn = $xoopsDB->getInsertId();

    return $tbcsn;
}

//更新tad_book3_cate某一筆資料
function update_tad_book3_cate($tbcsn = "")
{
    global $xoopsDB, $xoopsUser;

    $myts                 = &MyTextSanitizer::getInstance();
    $_POST['title']       = $myts->addSlashes($_POST['title']);
    $_POST['description'] = $myts->addSlashes($_POST['description']);

    $sql = "update " . $xoopsDB->prefix("tad_book3_cate") . " set
     `of_tbsn` = '{$_POST['of_tbsn']}' ,
     `title` = '{$_POST['title']}' ,
     `sort` = '{$_POST['sort']}' ,
     `description` = '{$_POST['description']}'
    where tbcsn='$tbcsn'";
    $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());

    return $tbcsn;
}

//取得tad_book3_cate無窮分類列表
function list_tad_book3_cate($show_tbcsn = 0)
{
    global $xoopsTpl, $xoopsDB;
    $path     = get_tad_book3_cate_path($show_tbcsn);
    $path_arr = array_keys($path);
    $sql      = "select tbcsn,of_tbsn,title from " . $xoopsDB->prefix("tad_book3_cate") . " order by sort";
    $result   = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());

    $count  = tad_book3_cate_count();
    $data[] = "{ id:0, pId:0, name:'All', url:'index.php', target:'_self', open:true}";
    while (list($tbcsn, $of_tbsn, $title) = $xoopsDB->fetchRow($result)) {
        $font_style      = $show_tbcsn == $tbcsn ? ", font:{'background-color':'yellow', 'color':'black'}" : '';
        $open            = in_array($tbcsn, $path_arr) ? 'true' : 'false';
        $display_counter = empty($count[$tbcsn]) ? "" : " ({$count[$tbcsn]})";
        $data[]          = "{ id:{$tbcsn}, pId:{$of_tbsn}, name:'{$title}{$display_counter}', url:'main.php?op=tad_book3_cate_form&tbcsn={$tbcsn}', target:'_self', open:{$open} {$font_style}}";
    }
    $json = implode(',', $data);

    if (!file_exists(XOOPS_ROOT_PATH . "/modules/tadtools/ztree.php")) {
        redirect_header("index.php", 3, _MA_NEED_TADTOOLS);
    }
    include_once XOOPS_ROOT_PATH . "/modules/tadtools/ztree.php";
    $ztree      = new ztree("link_tree", $json, "", "save_sort.php", "of_tbsn", "tbcsn");
    $ztree_code = $ztree->render();
    $xoopsTpl->assign('ztree_code', $ztree_code);

}

//刪除tad_book3_cate某筆資料資料
function delete_tad_book3_cate($tbcsn = "")
{
    global $xoopsDB;
    //先刪除底下所有連結
    $sql = "delete from " . $xoopsDB->prefix("tad_book3") . " where tbcsn='$tbcsn'";
    $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());

    $sql = "delete from " . $xoopsDB->prefix("tad_book3_cate") . " where tbcsn='$tbcsn'";
    $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());
}

/*-----------執行動作判斷區----------*/
include_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
$op      = system_CleanVars($_REQUEST, 'op', '', 'string');
$tbcsn   = system_CleanVars($_REQUEST, 'tbcsn', 0, 'int');
$link_sn = system_CleanVars($_REQUEST, 'link_sn', 0, 'int');

switch ($op) {
    /*---判斷動作請貼在下方---*/
    //替換資料
    case "replace_tad_book3_cate":
        replace_tad_book3_cate();
        header("location: {$_SERVER['PHP_SELF']}");
        exit;
        break;

    //新增資料
    case "insert_tad_book3_cate":
        $tbcsn = insert_tad_book3_cate();
        header("location: {$_SERVER['PHP_SELF']}");
        exit;
        break;

    //更新資料
    case "update_tad_book3_cate":
        update_tad_book3_cate($tbcsn);
        header("location: {$_SERVER['PHP_SELF']}");
        exit;
        break;
    //輸入表格
    case "tad_book3_cate_form":
        list_tad_book3_cate($tbcsn);
        tad_book3_cate_form($tbcsn);
        break;

    //刪除資料
    case "delete_tad_book3_cate":
        delete_tad_book3_cate($tbcsn);
        header("location: {$_SERVER['PHP_SELF']}");
        exit;
        break;

    //預設動作
    default:
        list_tad_book3_cate();
        break;

    /*---判斷動作請貼在上方---*/
}

/*-----------秀出結果區--------------*/
include_once 'footer.php';
