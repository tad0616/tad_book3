<?php
//區塊主函式 (會自動偵測目前閱讀的書籍，並秀出該書目錄)
function tad_book3_index()
{
    global $xoopsDB;
    $global_tbsn  = isset($_GET['tbsn']) ? intval($_GET['tbsn']) : "";
    $global_tbdsn = isset($_GET['tbdsn']) ? intval($_GET['tbdsn']) : "";

    if (empty($global_tbsn) and !empty($global_tbdsn)) {
        $sql        = "select `tbsn` from " . $xoopsDB->prefix("tad_book3_docs") . " where tbdsn='{$global_tbdsn}'";
        $result     = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
        list($tbsn) = $xoopsDB->fetchRow($result);
    } else {
        $tbsn = $global_tbsn;
    }

    if (empty($tbsn)) {
        return;
    }

    if (!file_exists(XOOPS_ROOT_PATH . "/modules/tadtools/dtree.php")) {
        redirect_header("index.php", 3, _MA_NEED_TADTOOLS);
    }
    include_once XOOPS_ROOT_PATH . "/modules/tadtools/dtree.php";
    $book          = block_get_book_content($tbsn);
    $home['sn']    = 0;
    $home['title'] = _MB_TADBOOK3_BOOK_CONTENT;
    $home['url']   = XOOPS_URL . "/modules/tad_book3/index.php?tbsn=$tbsn";
    $dtree         = new dtree("tad_book3_{$global_tbsn}", $home, $book['title'], $book['father_sn'], $book['url']);
    $block         = $dtree->render();

    return $block;
}

if (!function_exists("mk_category")) {
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
}

if (!function_exists("block_get_book_content")) {
    function block_get_book_content($tbsn)
    {
        global $xoopsDB;

        $sql = "select `tbdsn`,`tbsn`,`category`,`page`,`paragraph`,`sort`,`title` from " . $xoopsDB->prefix("tad_book3_docs") . " where tbsn='{$tbsn}' and enable='1' order by category,page,paragraph,sort";

        $result = $xoopsDB->query($sql);

        $father_sn = $old_sn = $old_level = 0;
        $fsn       = array();
        while (list($tbdsn, $tbsn, $category, $page, $paragraph, $sort, $title) = $xoopsDB->fetchRow($result)) {
            $doc_sort = block_category($tbdsn, $category, $page, $paragraph, $sort);

            $father_sn = 0;
            if ($doc_sort['level'] == 1) {
                $fsn["{$category}"] = $tbdsn;
            } elseif ($doc_sort['level'] == 2) {
                $fsn["{$category}-{$page}"] = $tbdsn;
                $father_sn                  = isset($fsn["{$category}"]) ? $fsn["{$category}"] : "";
            } elseif ($doc_sort['level'] == 3) {
                $fsn["{$category}-{$page}-{$paragraph}"] = $tbdsn;
                $father_sn                               = isset($fsn["{$category}-{$page}"]) ? $fsn["{$category}-{$page}"] : "";
            } elseif ($doc_sort['level'] == 4) {
                $father_sn = isset($fsn["{$category}-{$page}-{$paragraph}"]) ? $fsn["{$category}-{$page}-{$paragraph}"] : "";
            }

            if ($father_sn == '') {
                $father_sn = 0;
            }

            $book['title'][$tbdsn]     = "{$doc_sort['main']}{$title}";
            $book['father_sn'][$tbdsn] = $father_sn;
            $book['url'][$tbdsn]       = XOOPS_URL . "/modules/tad_book3/page.php?tbdsn={$tbdsn}";

        }

        return $book;

    }
}

if (!function_exists("block_category")) {
    //章節格式化
    function block_category($tbdsn, $category = "", $page = "", $paragraph = "", $sort = "")
    {
        if (!empty($sort)) {
            $main  = "{$category}-${page}-{$paragraph}-{$sort}";
            $level = 4;
        } elseif (!empty($paragraph)) {
            $main                                           = "{$category}-${page}-{$paragraph}";
            $level                                          = 3;
            $all['fsn']["{$category}-${page}-{$paragraph}"] = $tbdsn;
        } elseif (!empty($page)) {
            $main                              = "{$category}-${page}";
            $level                             = 2;
            $all['fsn']["{$category}-${page}"] = $tbdsn;
        } elseif (!empty($category)) {
            $main                  = "{$category}.";
            $level                 = 1;
            $all['fsn'][$category] = $tbdsn;
        } else {
            $main  = "";
            $level = 0;
        }

        $all['main']  = $main;
        $all['level'] = $level;
        return $all;
    }
}
