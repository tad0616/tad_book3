<?php
use XoopsModules\Tadtools\Dtree;
use XoopsModules\Tadtools\Utility;

if (!class_exists('XoopsModules\Tadtools\Utility')) {
    require XOOPS_ROOT_PATH . '/modules/tadtools/preloads/autoloader.php';
}

//區塊主函式 (會自動偵測目前閱讀的書籍，並秀出該書目錄)
function tad_book3_index()
{
    global $xoopsDB;
    require_once XOOPS_ROOT_PATH . '/modules/tad_book3/function_block.php';
    $global_tbsn = isset($_GET['tbsn']) ? (int) $_GET['tbsn'] : '';
    $global_tbdsn = isset($_GET['tbdsn']) ? (int) $_GET['tbdsn'] : '';

    if (empty($global_tbsn) and !empty($global_tbdsn)) {
        $sql = 'select `tbsn` from ' . $xoopsDB->prefix('tad_book3_docs') . " where tbdsn='{$global_tbdsn}'";
        $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
        list($tbsn) = $xoopsDB->fetchRow($result);
    } else {
        $tbsn = $global_tbsn;
    }

    if (empty($tbsn)) {
        return;
    }

    $book = block_get_book_content($tbsn);
    $home['sn'] = 0;
    $home['title'] = _MB_TADBOOK3_BOOK_CONTENT;
    $home['url'] = XOOPS_URL . "/modules/tad_book3/index.php?tbsn=$tbsn";
    $Dtree = new Dtree("tad_book3_{$global_tbsn}", $home, $book['title'], $book['father_sn'], $book['url']);
    $block = $Dtree->render();

    return $block;
}

if (!function_exists('block_get_book_content')) {
    function block_get_book_content($tbsn)
    {
        global $xoopsDB;

        $sql = 'select `tbdsn`,`tbsn`,`category`,`page`,`paragraph`,`sort`,`title`,`content` from ' . $xoopsDB->prefix('tad_book3_docs') . " where tbsn='{$tbsn}' and enable='1' order by category,page,paragraph,sort";

        $result = $xoopsDB->query($sql);

        $father_sn = $old_sn = $old_level = 0;
        $fsn = [];
        while (list($tbdsn, $tbsn, $category, $page, $paragraph, $sort, $title, $content) = $xoopsDB->fetchRow($result)) {
            $doc_sort = block_category($tbdsn, $category, $page, $paragraph, $sort);

            $father_sn = 0;
            if (1 == $doc_sort['level']) {
                $fsn[(string) ($category)] = $tbdsn;
            } elseif (2 == $doc_sort['level']) {
                $fsn["{$category}-{$page}"] = $tbdsn;
                $father_sn = isset($fsn[(string) ($category)]) ? $fsn[(string) ($category)] : '';
            } elseif (3 == $doc_sort['level']) {
                $fsn["{$category}-{$page}-{$paragraph}"] = $tbdsn;
                $father_sn = isset($fsn["{$category}-{$page}"]) ? $fsn["{$category}-{$page}"] : '';
            } elseif (4 == $doc_sort['level']) {
                $father_sn = isset($fsn["{$category}-{$page}-{$paragraph}"]) ? $fsn["{$category}-{$page}-{$paragraph}"] : '';
            }

            if ('' == $father_sn) {
                $father_sn = 0;
            }

            $book['title'][$tbdsn] = "{$doc_sort['main']}{$title}";
            $book['father_sn'][$tbdsn] = $father_sn;
            $book['url'][$tbdsn] = $content ? XOOPS_URL . "/modules/tad_book3/page.php?tbsn={$tbsn}&tbdsn={$tbdsn}" : '';
        }

        return $book;
    }
}

if (!function_exists('block_category')) {
    //章節格式化
    function block_category($tbdsn, $category = '', $page = '', $paragraph = '', $sort = '')
    {
        if (!empty($sort)) {
            $main = "{$category}-${page}-{$paragraph}-{$sort}";
            $level = 4;
        } elseif (!empty($paragraph)) {
            $main = "{$category}-${page}-{$paragraph}";
            $level = 3;
            $all['fsn']["{$category}-${page}-{$paragraph}"] = $tbdsn;
        } elseif (!empty($page)) {
            $main = "{$category}-${page}";
            $level = 2;
            $all['fsn']["{$category}-${page}"] = $tbdsn;
        } elseif (!empty($category)) {
            $main = "{$category}.";
            $level = 1;
            $all['fsn'][$category] = $tbdsn;
        } else {
            $main = '';
            $level = 0;
        }

        $all['main'] = $main;
        $all['level'] = $level;

        return $all;
    }
}
