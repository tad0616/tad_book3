<?php
include_once "header.php";
set_time_limit(0);
ini_set("memory_limit","150M");
$op=(empty($_REQUEST['op']))?"":$_REQUEST['op'];
$tbsn=(empty($_REQUEST['tbsn']))?"":intval($_REQUEST['tbsn']);
$tadbook=get_tad_book3($tbsn);
if(!chk_power($tadbook['read_group'])){
  header("location:index.php");
  exit;
}

if(!empty($tadbook['passwd']) and $_SESSION['passwd']!=$tadbook['passwd']){
  $data.=_MI_TADBOOK3_INPUT_PASSWD;
  return $data;
  exit;
}
$epub_root=XOOPS_ROOT_PATH.'/uploads/tad_book3/epub/';
$epub_book_root="{$epub_root}{$tbsn}/";
$METAINF_dir="{$epub_book_root}META-INF/";
$OEBPS_dir="{$epub_book_root}OEBPS/";

mk_dir($epub_root);
mk_dir($epub_book_root);
mk_dir($METAINF_dir);
mk_dir($OEBPS_dir);

copy('module.css',"{$OEBPS_dir}style.css");

//製作 mimetype
if(!file_exists("{$epub_book_root}mimetype")){
  file_put_contents("{$epub_book_root}mimetype", "application/epub+zip");
}

//製作 META-INF/container.xml
if(!file_exists("{$METAINF_dir}container.xml")){
$container='<?xml version="1.0"?>
<container version="1.0" xmlns="urn:oasis:names:tc:opendocument:xmlns:container">
    <rootfiles>
        <rootfile full-path="OEBPS/content.opf" media-type="application/oebps-package+xml"/>
   </rootfiles>
</container>';
  file_put_contents("{$METAINF_dir}container.xml", $container);
}



//高亮度語法
if(!file_exists(TADTOOLS_PATH."/syntaxhighlighter.php")){
 redirect_header("index.php",3, _MD_NEED_TADTOOLS);
}
include_once TADTOOLS_PATH."/syntaxhighlighter.php";
$syntaxhighlighter= new syntaxhighlighter();
$syntaxhighlighter_code=$syntaxhighlighter->render();

$main='
<link rel="stylesheet" type="text/css" media="screen" href="reset.css" />
<link rel="stylesheet" type="text/css" media="screen" href="module.css" />'.$syntaxhighlighter_code;

$sql = "select * from ".$xoopsDB->prefix("tad_book3_docs")." where tbsn='$tbsn'";
$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
$ncx=$manifest=$navPoint="";
$i=1;
while(list($tbdsn,$tbsn,$category,$page,$paragraph,$sort,$title,$content,$add_date,$last_modify_date,$uid,$count,$enable)=$xoopsDB->fetchRow($result)){

  $doc_sort=mk_category($category,$page,$paragraph,$sort);
  if(substr($doc_sort['main'],-1)=='.'){
    $doc_sort['main']=substr($doc_sort['main'],0,-1);
  }
  $filename="chap{$doc_sort['main']}.xhtml";

  $last_modify_date=date("Y-m-d H:i:s",xoops_getUserTimestamp($last_modify_date));

  $main="
<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\""._LANGCODE."\" lang=\""._LANGCODE."\">
<head>
  <link rel=\"stylesheet\" href=\"style.css\" type=\"text/css\" />
  <title>{$tadbook['title']}</title>
</head>

<body>
  <h{$doc_sort['level']}>{$doc_sort['main']} {$title}</h{$doc_sort['level']}>
</body>
</html>
  ";


  file_put_contents("{$OEBPS_dir}{$filename}", $main);

  $manifest.="
    <item id=\"chap{$doc_sort['main']}\" href=\"{$filename}\" media-type=\"application/xhtml+xml\"/>\n";
  $ncx.="
    <itemref idref=\"chap{$doc_sort['main']}\" />";

  $navPoint.="
  <navPoint class=\"section\" id=\"navPoint-{$i}\" playOrder=\"{$i}\">
    <navLabel>
      <text>{$title}</text>
    </navLabel>
    <content src=\"{$filename}\"/>
  </navPoint>";
  $i++;
}

$author=XoopsUser::getUnameFromId($tadbook['author'],1);
$author=(empty($author))?XoopsUser::getUnameFromId($tadbook['author'],0):$author;

//製作 OEBPS/content.opf
$publication=date("Y-m-d");
$identifier=md5("{$epub_book_root}{$publication}");
$content="<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
<package xmlns=\"http://www.idpf.org/2007/opf\" unique-identifier=\"BookID\" version=\"2.0\" xml:lang=\""._LANGCODE."\">
  <metadata xmlns:dc=\"http://purl.org/dc/elements/1.1/\" xmlns:opf=\"http://www.idpf.org/2007/opf\">
    <dc:title>{$tadbook['title']}</dc:title>
    <dc:rights> (c) ".XOOPS_URL."</dc:rights>
    <dc:creator opf:role=\"aut\">".XOOPS_URL."</dc:creator>
    <dc:type>Web page</dc:type>
    <dc:publisher>{$xoopsConfig['sitename']} (".XOOPS_URL.")</dc:publisher>
    <dc:source>".XOOPS_URL."/modules/tad_book3/epub.php?tbsn={$tbsn}</dc:source>
    <dc:date opf:event=\"publication\">{$publication}</dc:date>
    <dc:language>"._LANGCODE."</dc:language>
    <dc:identifier id=\"BookID\" opf:scheme=\"CustomID\">epub-{$identifier}</dc:identifier>
  </metadata>
  <manifest>
    <item id=\"ncx\" href=\"toc.ncx\" media-type=\"application/x-dtbncx+xml\"/>
    <item id=\"style\" href=\"style.css\" media-type=\"text/css\"/>
    {$manifest}
  </manifest>
  <spine toc=\"ncx\">
    {$ncx}
  </spine>
  <guide/>
</package>";
file_put_contents("{$OEBPS_dir}content.opf", $content);


//製作 OEBPS/toc.ncx
$content="<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
<!DOCTYPE ncx PUBLIC \"-//NISO//DTD ncx 2005-1//EN\"
   \"http://www.daisy.org/z3986/2005/ncx-2005-1.dtd\">

<ncx xmlns=\"http://www.daisy.org/z3986/2005/ncx/\" version=\"2005-1\" xml:lang=\""._LANGCODE."\">
  <head>
    <meta name=\"dtb:uid\" content=\"epub-{$identifier}\"/>
    <meta name=\"dtb:depth\" content=\"1\"/>
    <meta name=\"dtb:totalPageCount\" content=\"0\"/>
    <meta name=\"dtb:maxPageNumber\" content=\"0\"/>
  </head>
  <docTitle>
    <text>{$tadbook['title']}</text>
  </docTitle>
  <docAuthor>
    <text>{$author}</text>
  </docAuthor>

  <navMap>
    $navPoint
  </navMap>
</ncx>";
file_put_contents("{$OEBPS_dir}toc.ncx", $content);

?>