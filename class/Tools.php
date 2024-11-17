<?php
namespace XoopsModules\Tad_book3;

use XoopsModules\Tadtools\TadDataCenter;
use XoopsModules\Tadtools\Utility;

/**
 * Class Update
 */
class Tools
{

    //判斷本文是否允許該用戶之所屬群組觀看
    public static function chk_power($book_enable_group = '', $page_enable_group = '')
    {
        global $xoopsUser;

        // 如果兩個群組都為空，直接返回 true
        if (empty($book_enable_group) && empty($page_enable_group)) {
            return true;
        }

        // 獲取用戶群組，如果用戶未登錄則為空數組
        $userGroups = $xoopsUser ? $xoopsUser->getGroups() : [];

        // 如果用戶沒有群組，直接返回 false
        if (empty($userGroups)) {
            return false;
        }

        // 將字符串轉換為數組，並過濾掉空值
        $bookGroups = array_filter(explode(',', $book_enable_group));
        $pageGroups = array_filter(explode(',', $page_enable_group));

        // 檢查用戶是否在允許的群組中
        $inBookGroup = empty($bookGroups) || array_intersect($userGroups, $bookGroups);
        $inPageGroup = empty($pageGroups) || array_intersect($userGroups, $pageGroups);

        // 同時滿足兩個條件才返回 true
        return $inBookGroup && $inPageGroup;
    }

    public static function get_start_ts($tbdsn = '', $type = 'read', $ok_group = '')
    {
        global $xoopsUser;

        $TadDataCenter = new TadDataCenter('tad_book3');
        if ($type == 'video') {
            $TadDataCenter->set_col('video_tbdsn_date', $tbdsn);
        } else {
            $TadDataCenter->set_col('read_tbdsn_date', $tbdsn);
        }
        $start_date_arr = $TadDataCenter->getData();

        //取得目前使用者的所屬群組
        if ($xoopsUser) {
            $User_Groups = $xoopsUser->getGroups();
        } else {
            $User_Groups = [];
        }

        $ok_group_arr = explode(',', $ok_group);
        $start_time = 0;
        $view = false;
        foreach ($User_Groups as $gid) {
            if (in_array($gid, $ok_group_arr) && isset($start_date_arr[$gid])) {
                $view = true;
                $new_start_time = strtotime($start_date_arr[$gid][0]);
                if ($start_time == 0 || $new_start_time < $start_time) {
                    $start_time = $new_start_time;
                }
            }
        }

        if ($view) {
            return $start_time;
        } else {
            return false;
        }
    }

//更新書籍計數器
    public static function add_book_counter($tbsn = '')
    {
        global $xoopsDB;
        $sql = 'UPDATE `' . $xoopsDB->prefix('tad_book3') . '` SET `counter` = `counter`+1 WHERE `tbsn` = ?';
        Utility::query($sql, 'i', [$tbsn]) or Utility::web_error($sql, __FILE__, __LINE__);

    }

//取得所有分類
    public static function all_cate()
    {
        global $xoopsDB;
        $sql = 'SELECT `tbcsn`, `title` FROM `' . $xoopsDB->prefix('tad_book3_cate') . '` ORDER BY `sort`';
        $result = Utility::query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

        while (list($tbcsn, $title) = $xoopsDB->fetchRow($result)) {
            $main[$tbcsn] = $title;
        }

        return $main;
    }

//章節格式化
    public static function mk_category($category = '', $page = '', $paragraph = '', $sort = '')
    {
        if (!empty($sort)) {
            $main = "{$category}-{$page}-{$paragraph}-{$sort}";
            $ttid = "{$category}0{$page}0{$paragraph}0{$sort}";
            $parent = "{$category}0{$page}0{$paragraph}";
            $level = 4;
        } elseif (!empty($paragraph)) {
            $main = "{$category}-{$page}-{$paragraph}";
            $ttid = "{$category}0{$page}0{$paragraph}";
            $parent = "{$category}0{$page}";
            $level = 3;
        } elseif (!empty($page)) {
            $main = "{$category}-{$page}";
            $ttid = "{$category}0{$page}";
            $parent = "{$category}";
            $level = 2;
        } elseif (!empty($category)) {
            $main = "{$category}.";
            $ttid = "{$category}";
            $parent = '';
            $level = 1;
        } else {
            $main = '';
            $ttid = '';
            $level = 0;
            $parent = '';
        }
        $all['main'] = $main;
        $all['level'] = $level;
        $all['ttid'] = $ttid;
        $all['parent'] = $parent;

        return $all;
    }

//檢查有無底下文章
    public static function have_sub($tbsn = 0, $category = 0, $page = 0, $paragraph = 0, $sort = 0)
    {
        global $xoopsDB;
        if (!empty($sort)) {
            return 0;
        }

        $and_category = $category ? "AND `category` = ?" : '';
        $and_page = $page ? "AND `page` = ?" : '';
        $and_paragraph = $paragraph ? "AND `paragraph` = ?" : '';

        $sql = 'SELECT COUNT(*)
                FROM `' . $xoopsDB->prefix('tad_book3_docs') . '`
                WHERE `tbsn` = ? ' . $and_category . ' ' . $and_page . ' ' . $and_paragraph;

        $params = array_filter([$tbsn, $category, $page, $paragraph]);
        $result = Utility::query($sql, str_repeat('i', count($params)), $params) or Utility::web_error($sql, __FILE__, __LINE__);

        list($count) = $xoopsDB->fetchRow($result);
        $count--;

        return $count;
    }

    //book陰影
    public static function book_shadow($books = [])
    {
        global $xoopsUser, $tad_book3_adm;

        $uid = $xoopsUser ? $xoopsUser->uid() : 0;

        $authors = explode(',', $books['author']);
        $tool = ((!empty($uid) && in_array($uid, $authors)) || $tad_book3_adm) ? true : false;
        $books['tool'] = $tool;

        $pic = (empty($books['pic_name'])) ? XOOPS_URL . '/modules/tad_book3/images/blank.png' : XOOPS_URL . "/uploads/tad_book3/{$books['pic_name']}";
        $books['pic'] = $pic;

        $pic_fb = (empty($books['pic_name'])) ? XOOPS_URL . '/modules/tad_book3/images/blank.png' : XOOPS_URL . "/uploads/tad_book3/fb_{$books['pic_name']}";
        $books['pic_fb'] = $pic_fb;

        $description = isset($description) ? strip_tags($description) : '';
        $books['description'] = $description;

        return $books;
    }

}
