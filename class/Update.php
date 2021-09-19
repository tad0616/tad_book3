<?php

namespace XoopsModules\Tad_book3;

use XoopsModules\Tadtools\Utility;

/*
Update Class Definition

You may not change or alter any portion of this comment or credits of
supporting developers from this source code or any supporting source code
which is considered copyrighted (c) material of the original comment or credit
authors.

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @license      http://www.fsf.org/copyleft/gpl.html GNU public license
 * @copyright    https://xoops.org 2001-2017 &copy; XOOPS Project
 * @author       Mamba <mambax7@gmail.com>
 */

/**
 * Class Update
 */
class Update
{
    //新增文章來源欄位
    public static function chk_chk1()
    {
        global $xoopsDB;
        $sql = 'SELECT count(`from_tbdsn`) FROM ' . $xoopsDB->prefix('tad_book3_docs');
        $result = $xoopsDB->query($sql);
        if (empty($result)) {
            return true;
        }

        return false;
    }

    public static function go_update1()
    {
        global $xoopsDB;
        $sql = 'ALTER TABLE ' . $xoopsDB->prefix('tad_book3_docs') . ' ADD `from_tbdsn` INT(10) UNSIGNED NOT NULL DEFAULT 0';
        $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);

        return true;
    }

    //新增files_center
    public static function chk_chk2()
    {
        global $xoopsDB;
        $sql = "SHOW TABLES LIKE '" . $xoopsDB->prefix('tad_book3_files_center') . "'";
        $result = $xoopsDB->query($sql);
        $total = $xoopsDB->getRowsNum($result);
        if (empty($total)) {
            return true;
        }

        return false;
    }

    public static function go_update2()
    {
        global $xoopsDB;
        $sql = "CREATE TABLE `" . $xoopsDB->prefix('tad_book3_files_center') . "` (
            `files_sn` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '檔案流水號',
            `col_name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '欄位名稱',
            `col_sn` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '欄位編號',
            `sort` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
            `kind` enum('img', 'file') NOT NULL DEFAULT 'img' COMMENT '檔案種類',
            `file_name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '檔案名稱',
            `file_type` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '檔案類型',
            `file_size` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '檔案大小',
            `description` text NOT NULL COMMENT '檔案說明',
            `counter` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT '下載人次',
            `original_filename` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '檔案名稱',
            `hash_filename` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '加密檔案名稱',
            `sub_dir` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '檔案子路徑',
            `upload_date` datetime NOT NULL COMMENT '上傳時間',
            `uid` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上傳者',
            `tag` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '註記',
            PRIMARY KEY (`files_sn`)
          ) ENGINE = MyISAM;";
        $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);

        return true;
    }

    //新增files_center
    public static function chk_chk3()
    {
        global $xoopsDB;
        $sql = "SHOW TABLES LIKE '" . $xoopsDB->prefix('tad_book3_data_center') . "'";
        $result = $xoopsDB->query($sql);
        $total = $xoopsDB->getRowsNum($result);
        if (empty($total)) {
            return true;
        }

        return false;
    }

    public static function go_update3()
    {
        global $xoopsDB;
        $sql = "CREATE TABLE `" . $xoopsDB->prefix('tad_book3_data_center') . "` (
            `mid` mediumint(9) unsigned NOT NULL AUTO_INCREMENT COMMENT '模組編號',
            `col_name` varchar(100) NOT NULL DEFAULT '' COMMENT '欄位名稱',
            `col_sn` mediumint(9) unsigned NOT NULL DEFAULT '0' COMMENT '欄位編號',
            `data_name` varchar(100) NOT NULL DEFAULT '' COMMENT '資料名稱',
            `data_value` text NOT NULL COMMENT '儲存值',
            `data_sort` mediumint(9) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
            `col_id` varchar(100) NOT NULL COMMENT '辨識字串',
            `sort` mediumint(9) unsigned COMMENT '顯示順序',
            `update_time` datetime NOT NULL COMMENT '更新時間',
            PRIMARY KEY (
              `mid`,
              `col_name`,
              `col_sn`,
              `data_name`,
              `data_sort`
            )
          ) ENGINE = MyISAM;";
        $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);

        return true;
    }

    //新增影片群組欄位
    public static function chk_chk4()
    {
        global $xoopsDB;
        $sql = 'SELECT count(`video_group`) FROM ' . $xoopsDB->prefix('tad_book3');
        $result = $xoopsDB->query($sql);
        if (empty($result)) {
            return true;
        }

        return false;
    }

    public static function go_update4()
    {
        global $xoopsDB;
        $sql = 'ALTER TABLE ' . $xoopsDB->prefix('tad_book3') . " ADD `video_group` VARCHAR(255) NOT NULL DEFAULT '' AFTER `read_group`";
        $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);

        return true;
    }

    //新增影片群組欄位
    public static function chk_chk5()
    {
        global $xoopsDB;
        $sql = 'SELECT count(`video_group`) FROM ' . $xoopsDB->prefix('tad_book3_docs');
        $result = $xoopsDB->query($sql);
        if (empty($result)) {
            return true;
        }

        return false;
    }

    public static function go_update5()
    {
        global $xoopsDB;
        $sql = 'ALTER TABLE ' . $xoopsDB->prefix('tad_book3_docs') . " ADD `read_group` VARCHAR(255) NOT NULL DEFAULT '' AFTER `enable`, ADD `video_group` VARCHAR(255) NOT NULL DEFAULT '' AFTER `read_group`";
        $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);

        return true;
    }

    //刪除錯誤的重複欄位及樣板檔
    public static function chk_tad_book3_block()
    {
        global $xoopsDB;
        //die(var_export($xoopsConfig));
        require XOOPS_ROOT_PATH . '/modules/tad_book3/xoops_version.php';

        //先找出該有的區塊以及對應樣板
        foreach ($modversion['blocks'] as $i => $block) {
            $show_func = $block['show_func'];
            $tpl_file_arr[$show_func] = $block['template'];
            $tpl_desc_arr[$show_func] = $block['description'];
        }

        //找出目前所有的樣板檔
        $sql = 'SELECT bid,name,visible,show_func,template FROM `' . $xoopsDB->prefix('newblocks') . "`
        WHERE `dirname` = 'tad_book3' ORDER BY `func_num`";
        $result = $xoopsDB->query($sql);
        while (list($bid, $name, $visible, $show_func, $template) = $xoopsDB->fetchRow($result)) {
            //假如現有的區塊和樣板對不上就刪掉
            if ($template != $tpl_file_arr[$show_func]) {
                $sql = 'delete from ' . $xoopsDB->prefix('newblocks') . " where bid='{$bid}'";
                $xoopsDB->queryF($sql);

                //連同樣板以及樣板實體檔案也要刪掉
                $sql = 'delete from ' . $xoopsDB->prefix('tplfile') . ' as a
            left join ' . $xoopsDB->prefix('tplsource') . "  as b on a.tpl_id=b.tpl_id
            where a.tpl_refid='$bid' and a.tpl_module='tad_book3' and a.tpl_type='block'";
                $xoopsDB->queryF($sql);
            } else {
                $sql = 'update ' . $xoopsDB->prefix('tplfile') . "
            set tpl_file='{$template}' , tpl_desc='{$tpl_desc_arr[$show_func]}'
            where tpl_refid='{$bid}'";
                $xoopsDB->queryF($sql);
            }
        }
    }

    //修正uid欄位
    public static function chk_uid()
    {
        global $xoopsDB;
        $sql = "SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_name = '" . $xoopsDB->prefix('tad_book3_docs') . "' AND COLUMN_NAME = 'uid'";
        $result = $xoopsDB->query($sql);
        list($type) = $xoopsDB->fetchRow($result);
        if ('smallint' === $type) {
            return true;
        }

        return false;
    }

    //執行更新
    public static function go_update_uid()
    {
        global $xoopsDB;
        $sql = 'ALTER TABLE `' . $xoopsDB->prefix('tad_book3_docs') . '` CHANGE `uid` `uid` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0';
        $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);

        return true;
    }

    //做縮圖
    public static function thumbnail($filename = '', $thumb_name = '', $type = 'image/jpeg', $width = '120')
    {
        ini_set('memory_limit', '50M');
        // Get new sizes
        list($old_width, $old_height) = getimagesize($filename);

        $percent = ($old_width > $old_height) ? round($width / $old_width, 2) : round($width / $old_height, 2);

        $newwidth = ($old_width > $old_height) ? $width : $old_width * $percent;
        $newheight = ($old_width > $old_height) ? $old_height * $percent : $width;

        // Load
        $thumb = imagecreatetruecolor($newwidth, $newheight);
        if ('image/jpeg' === $type or 'image/jpg' === $type or 'image/pjpg' === $type or 'image/pjpeg' === $type) {
            $source = imagecreatefromjpeg($filename);
            $type = 'image/jpeg';
        } elseif ('image/png' === $type) {
            $source = imagecreatefrompng($filename);
            $type = 'image/png';
        } elseif ('image/gif' === $type) {
            $source = imagecreatefromgif($filename);
            $type = 'image/gif';
        }

        // Resize
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $old_width, $old_height);

        header('Content-type: image/png');
        imagepng($thumb, $thumb_name);

        return;
        exit;
    }

}
