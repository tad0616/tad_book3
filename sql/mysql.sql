CREATE TABLE tad_book3 (
  `tbsn` SMALLINT(5) UNSIGNED NOT NULL auto_increment,
  `tbcsn` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  `sort` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `author` VARCHAR(255) NOT NULL DEFAULT '',
  `read_group` VARCHAR(255) NOT NULL DEFAULT '',
  `video_group` VARCHAR(255) NOT NULL DEFAULT '',
  `passwd` VARCHAR(255) NOT NULL DEFAULT '',
  `enable` enum('1', '0') NOT NULL DEFAULT '1',
  `pic_name` VARCHAR(255) NOT NULL DEFAULT '',
  `counter` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  `create_date` datetime NOT NULL,
  PRIMARY KEY (`tbsn`),
  KEY tbcsn (`tbcsn`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8;

CREATE TABLE tad_book3_cate (
  `tbcsn` SMALLINT(5) UNSIGNED NOT NULL auto_increment,
  `of_tbsn` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  `sort` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  PRIMARY KEY (`tbcsn`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8;

CREATE TABLE tad_book3_docs (
  `tbdsn` INT(10) UNSIGNED NOT NULL auto_increment,
  `tbsn` SMALLINT(6) UNSIGNED NOT NULL DEFAULT 0,
  `category` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `page` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `paragraph` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `sort` SMALLINT(6) NOT NULL DEFAULT 0,
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `add_date` VARCHAR(255) NOT NULL DEFAULT '',
  `last_modify_date` VARCHAR(255) NOT NULL DEFAULT '',
  `uid` MEDIUMINT(8) NOT NULL DEFAULT 0,
  `count` SMALLINT(6) NOT NULL DEFAULT 0,
  `enable` enum('1', '0') NOT NULL DEFAULT '1',
  `read_group` VARCHAR(255) NOT NULL DEFAULT '',
  `video_group` VARCHAR(255) NOT NULL DEFAULT '',
  `from_tbdsn` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`tbdsn`),
  KEY category (`category`, `page`, `paragraph`),
  KEY book_sn (`tbdsn`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8;

CREATE TABLE `tad_book3_files_center` (
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
) ENGINE = MyISAM;

CREATE TABLE `tad_book3_data_center` (
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
) ENGINE = MyISAM DEFAULT CHARSET = utf8;