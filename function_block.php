<?php

//判斷本文是否允許該用戶之所屬群組觀看
if (!function_exists('chk_power')) {
    function chk_power($enable_group = '')
    {
        global $xoopsDB, $xoopsUser;
        if (empty($enable_group)) {
            return true;
        }

        //取得目前使用者的所屬群組
        if ($xoopsUser) {
            $User_Groups = $xoopsUser->getGroups();
        } else {
            $User_Groups = [];
        }

        $news_enable_group = explode(',', $enable_group);
        foreach ($User_Groups as $gid) {
            if (in_array($gid, $news_enable_group)) {
                return true;
            }
        }

        return false;
    }
}

//更新書籍計數器
if (!function_exists('add_book_counter')) {
    function add_book_counter($tbsn = '')
    {
        global $xoopsDB;
        $sql = 'update ' . $xoopsDB->prefix('tad_book3') . " set  `counter` = `counter`+1 where tbsn='$tbsn'";
        $xoopsDB->queryF($sql) or web_error($sql, __FILE__, __LINE__);
    }
}

//取得所有分類
if (!function_exists('all_cate')) {
    function all_cate()
    {
        global $xoopsDB, $xoopsModule;
        $sql = 'SELECT tbcsn,title FROM ' . $xoopsDB->prefix('tad_book3_cate') . ' ORDER BY sort';
        $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
        while (list($tbcsn, $title) = $xoopsDB->fetchRow($result)) {
            $main[$tbcsn] = $title;
        }

        return $main;
    }
}

//章節格式化
if (!function_exists('mk_category')) {
    function mk_category($category = '', $page = '', $paragraph = '', $sort = '')
    {
        if (!empty($sort)) {
            $main = "{$category}-${page}-{$paragraph}-{$sort}";
            $level = 4;
        } elseif (!empty($paragraph)) {
            $main = "{$category}-${page}-{$paragraph}";
            $level = 3;
        } elseif (!empty($page)) {
            $main = "{$category}-${page}";
            $level = 2;
        } elseif (!empty($category)) {
            $main = "{$category}.";
            $level = 1;
        } else {
            $main = '';
            $level = 0;
        }
        $all['main'] = $main;
        $all['level'] = $level;

        return $all;
    }
}

//檢查有無底下文章
if (!function_exists('have_sub')) {
    function have_sub($tbsn = 0, $category = 0, $page = 0, $paragraph = 0, $sort = 0)
    {
        global $xoopsDB;
        if (!empty($sort)) {
            return 0;
        }

        $and_category = $category ? "and `category`= $category" : '';
        $and_page = $page ? "and `page`= $page" : '';
        $and_paragraph = $paragraph ? "and `paragraph`= $paragraph" : '';

        $sql = 'select count(*) from ' . $xoopsDB->prefix('tad_book3_docs') . " where tbsn='{$tbsn}' $and_category $and_page $and_paragraph";
        $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
        list($count) = $xoopsDB->fetchRow($result);
        $count--;

        return $count;
    }
}

//book陰影
if (!function_exists('book_shadow')) {
    function book_shadow($books = [])
    {
        global $xoopsUser, $isAdmin;

        if ($xoopsUser) {
            $uid = $xoopsUser->uid();
        } else {
            $uid = 0;
        }
        $authors = explode(',', $books['author']);
        $tool = ((!empty($uid) && in_array($uid, $authors)) || $isAdmin) ? true : false;
        $books['tool'] = $tool;

        $pic = (empty($books['pic_name'])) ? XOOPS_URL . '/modules/tad_book3/images/blank.png' : XOOPS_URL . "/uploads/tad_book3/{$books['pic_name']}";
        $books['pic'] = $pic;
        $description = isset($description) ? strip_tags($description) : '';
        $books['description'] = $description;

        return $books;
    }
}
