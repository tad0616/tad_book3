<?php

use XoopsModules\Tadtools\Utility;

require_once dirname(dirname(__DIR__)) . '/mainfile.php';

require_once __DIR__ . '/function.php';

//判斷是否對該模組有管理權限
if (!isset($_SESSION['tad_book3_adm'])) {
    $_SESSION['tad_book3_adm'] = ($xoopsUser) ? $xoopsUser->isAdmin() : false;
}
$interface_menu[_MD_TADBOOK3_HOMEPAGE] = 'index.php';

//管理員可以新增書籍
if ($_SESSION['tad_book3_adm']) {
    $interface_menu[_MD_TADBOOK3_ADD_BOOK] = 'index.php?op=tad_book3_form';
    //$interface_menu[_MD_TADBOOK3_IMPORT]   = "index.php?op=import_form";
}

if (\Xmf\Request::hasVar('tbdsn', 'GET')) {
    $tbdsn = \Xmf\Request::getInt('tbdsn', 0, 'GET');
}
if (\Xmf\Request::hasVar('tbsn', 'GET')) {
    $tbsn = \Xmf\Request::getInt('tbsn', 0, 'GET');
}

if (!empty($tbdsn) or !empty($tbsn)) {
    if (!empty($tbdsn)) {
        $sql = 'SELECT a.tbsn, a.title, b.author, a.category, a.page, a.paragraph, a.sort FROM `' . $xoopsDB->prefix('tad_book3_docs') . '` AS a LEFT JOIN `' . $xoopsDB->prefix('tad_book3') . '` AS b ON a.tbsn=b.tbsn WHERE a.tbdsn=?';
        $result = Utility::query($sql, 'i', [$tbdsn]) or Utility::web_error($sql, __FILE__, __LINE__);

        list($tbsn, $title, $author, $category, $page, $paragraph, $sort) = $xoopsDB->fetchRow($result);

        $all_books = all_books();
        $txt = sprintf(_MD_TADBOOK3_BOOK_CONTENT, $all_books[$tbsn]);
        $interface_menu[$txt] = "index.php?op=list_docs&tbsn={$tbsn}";

        if (chk_edit_power($author)) {
            $interface_menu[_MD_TADBOOK3_ADD_DOC] = "post.php?op=tad_book3_docs_form&tbsn={$tbsn}";
            $interface_menu[_MD_TADBOOK3_MODIFY_DOC] = "post.php?op=tad_book3_docs_form&tbsn={$tbsn}&tbdsn={$tbdsn}";
        }

        $category = mk_category($category, $page, $paragraph, $sort);
    } elseif (!empty($tbsn)) {
        $sql = 'SELECT `tbsn`, `author` FROM `' . $xoopsDB->prefix('tad_book3') . '` WHERE `tbsn` = ?';
        $result = Utility::query($sql, 'i', [$tbsn]) or Utility::web_error($sql, __FILE__, __LINE__);

        list($tbsn, $author) = $xoopsDB->fetchRow($result);
        if (chk_edit_power($author)) {
            $interface_menu[_MD_TADBOOK3_ADD_DOC] = "post.php?op=tad_book3_docs_form&tbsn={$tbsn}";
        }
    }
}

if ($_SESSION['tad_book3_adm']) {
    $interface_menu[_TAD_TO_ADMIN] = 'admin/main.php';
}
