CREATE TABLE tad_book3 (
  `tbsn` smallint(5) unsigned NOT NULL  auto_increment,
  `tbcsn` smallint(5) unsigned NOT NULL default 0 ,
  `sort` smallint(5) unsigned NOT NULL  default 0 ,
  `title` varchar(255) NOT NULL default '' ,
  `description` text NOT NULL ,
  `author` varchar(255) NOT NULL default '' ,
  `read_group` varchar(255) NOT NULL default '' ,
  `passwd` varchar(255) NOT NULL default '' ,
  `enable` enum('1','0') NOT NULL default '1' ,
  `pic_name` varchar(255) NOT NULL default '' ,
  `counter` smallint(5) unsigned NOT NULL  default 0 ,
  `create_date` datetime NOT NULL ,
  PRIMARY KEY  (`tbsn`),
  KEY tbcsn (`tbcsn`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE tad_book3_cate (
  `tbcsn` smallint(5) unsigned NOT NULL  auto_increment,
  `of_tbsn` smallint(5) unsigned NOT NULL  default 0 ,
  `sort` smallint(5) unsigned NOT NULL  default 0 ,
  `title` varchar(255) NOT NULL default '' ,
  `description` text NOT NULL ,
  PRIMARY KEY  (`tbcsn`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE tad_book3_docs (
  `tbdsn` int(10) unsigned NOT NULL  auto_increment,
  `tbsn` smallint(6) unsigned NOT NULL  default 0 ,
  `category` tinyint(3) unsigned NOT NULL  default 0 ,
  `page` tinyint(3) unsigned NOT NULL  default 0 ,
  `paragraph` tinyint(3) unsigned NOT NULL  default 0 ,
  `sort` smallint(6) NOT NULL  default 0 ,
  `title` varchar(255) NOT NULL default '' ,
  `content` text NOT NULL ,
  `add_date` varchar(255) NOT NULL default '' ,
  `last_modify_date` varchar(255) NOT NULL default '' ,
  `uid` mediumint(8) NOT NULL  default 0 ,
  `count` smallint(6) NOT NULL  default 0 ,
  `enable` enum('1','0') NOT NULL default '1' ,
  `from_tbdsn` int(10) unsigned NOT NULL default 0,
  PRIMARY KEY  (`tbdsn`),
  KEY category (`category`,`page`,`paragraph`),
  KEY book_sn (`tbdsn`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;


