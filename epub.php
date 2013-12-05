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

mk_dir(XOOPS_ROOT_PATH.'/uploads/tad_book3/epub/');
mk_dir(XOOPS_ROOT_PATH.'/uploads/tad_book3/epub/'.$tbsn);
$fileDir = XOOPS_ROOT_PATH."/uploads/tad_book3/epub/{$tbsn}/";

include_once("class/epub/EPub.php");

$book = new EPub();

// Title and Identifier are mandatory!
$book->setTitle($tadbook['title']);
$book->setIdentifier($_SERVER['PHP_SELF'], EPub::IDENTIFIER_URI); // Could also be the ISBN number, prefered for published books, or a UUID.
$book->setLanguage("zh"); // Not needed, but included for the example, Language is mandatory, but EPub defaults to "en". Use RFC3066 Language codes, such as "en", "da", "fr" etc.
$book->setDescription(strip_tags($tadbook['description']));
$book->setAuthor($tadbook['author']);
$book->setPublisher($xoopsConfig['sitename'], XOOPS_URL); // I hope this is a non existant address :)
$book->setDate(time()); // Strictly not needed as the book date defaults to time().
$book->setRights("Copyright and licence information specific for the book."); // As this is generated, this _could_ contain the name or licence information of the user who purchased the book, if needed. If this is used that way, the identifier must also be made unique for the book.
$book->setSourceURL($_SERVER['PHP_SELF']);

$cssData = "body {\n  margin-left: .5em;\n  margin-right: .5em;\n  text-align: justify;\n}\n\np {\n  font-family: serif;\n  font-size: 10pt;\n  text-align: justify;\n  text-indent: 1em;\n  margin-top: 0px;\n  margin-bottom: 1ex;\n}\n\nh1, h2 {\n  font-family: sans-serif;\n  font-style: italic;\n  text-align: center;\n  background-color: #6b879c;\n  color: white;\n  width: 100%;\n}\n\nh1 {\n    margin-bottom: 2px;\n}\n\nh2 {\n    margin-top: -2px;\n    margin-bottom: 2px;\n}\n";

$book->addCSSFile("styles.css", "css1", $cssData);

// This test requires you have an image, change "images/_cover_.jpg" to match your location.
//$book->setCoverImage("Cover.jpg", file_get_contents("images/_cover_.jpg"), "image/jpeg");

// A better way is to let EPub handle the image itself, as it may need resizing. Most Ebooks are only about 600x800
//  pixels, adding megapix images is a waste of place and spends bandwidth. setCoverImage can resize the image.
//  When using this method, the given image path must be the absolute path from the servers Document root.
//$book->setCoverImage("/test/images/_cover_.jpg");
// setCoverImage can only be called once per book, but can be called at any point in the book creation.

// ePub uses XHTML 1.1, preferably strict.
$content_start =
	"<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"
	. "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"\n"
	. "    \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n"
	. "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n"
	. "<head>"
	. "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n"
	. "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\" />\n"
	. "<title>Test Book</title>\n"
	. "</head>\n"
	. "<body>\n";

$cover = $content_start . "<h1>{$tadbook['title']}</h1>\n<h2>By: {$tadbook['author']}</h2>\n"
	. "</body>\n</html>\n";
$book->addChapter("Notices", "Cover.html", $cover);



$sql = "select * from ".$xoopsDB->prefix("tad_book3_docs")." where tbsn='$tbsn'";
$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
while(list($tbdsn,$tbsn,$category,$page,$paragraph,$sort,$title,$content,$add_date,$last_modify_date,$uid,$count,$enable)=$xoopsDB->fetchRow($result)){

	$doc_sort=mk_category($category,$page,$paragraph,$sort);
	$last_modify_date=date("Y-m-d H:i:s",xoops_getUserTimestamp($last_modify_date));

	$book_content=$content_start."<h1>{$title}</h1>\n{$content}</body>\n</html>\n";
	$book->addChapter("{$doc_sort} {$title}", "Chapter{$doc_sort}.html", $book_content);
}

// Autosplit a chapter:
$book->setSplitSize(15000); // For this test, we split at approx 15k. Default is 250000 had we left it alone.



//$book->addChapter("Chapter 4: Vivamus bibendum massa split", "Chapter004.html", $chapter4, true);

// More advanced use of the splitter:
// Still using Chapter 4, but as you can see, "Chapter 4" also contains a header for Chapter 5.


/*
include_once 'class/epub/EPubChapterSplitter.php';
$splitter = new EPubChapterSplitter();
$splitter->setSplitSize(15000); // For this test, we split at approx 15k. Default is 250000 had we left it alone.
 */

/* Using the # as regexp delimiter here, it makes writing the regexp easier.
 *  in this case we could have just searched for "Chapter ", or if we were using regexp '#^<h1>Chapter #i',
 *  using regular text (no regexp delimiters) will look for the text after the first tag. Meaning had we used
 *  "Chapter ", any paragraph or header starting with "Chapter " would have matched. The regexp equivalent of
 *  "Chapter " is '#^<.+?>Chapter #'
 * Essentially, the search strnig is looking for lines starting with...
 */


/*
$html2 = $splitter->splitChapter($chapter4, true, "Chapter ");

$idx = 0;
while (list($k, $v) = each($html2)) {
	$idx++;
	// Because we used a string search in the splitter, the returned hits are put in the key part of the array.
	// The entire HTML tag of the line matching the chapter search.

	// find the text inside the tags
	preg_match('#^<(\w+)\ *.*?>(.+)</\ *\1>$#i', $k, $cName);

	// because of the back reference, the tag name is in $cName[1], and the content is in $cName[2]
	// Change any line breakes in the chapter name to " - "
	$cName = preg_replace('#<br.+?>#i', " - ", $cName[2]);
	// Remove any other tags
	$cName = preg_replace('#<.+?>#i', " ", $cName);
	// clean the chapter name by removing any double spaces left behind to single space.
	$cName = preg_replace('#\s+#i', " ", $cName);

	$book->addChapter($cName, "Chapter005_" . $idx . ".html", $v, true);
}
 */


// Notice that Chapter 1 have an image reference in paragraph 2?
// We can tell EPub to automatically load embedded images and other references:
// The parameters for addChapter are:
//  1: Chapter Name
//  2: File Name (in the book)
//  3: Chapter Data (HTML or array of HTML strings making up one chapter)
//  4: Auto Split Chapter (Default false)
//  5: External References, How to handle external references, default is EPub::EXTERNAL_REF_IGNORE
//  6: Base Dir, This is important, as this have to point to the root of the imported HTML, as seen from it's Document root.
//     if you are importing an HTML designed to live in "http://server/story/book.html", $baseDir must be "story"
//     It is used to resolve any links in the HTML.
//$book->addChapter("Chapter 6: Image test", "Chapter006.html", $chapter1, false, EPub::EXTERNAL_REF_ADD, $fileDir);

$book->finalize(); // Finalize the book, and build the archive.

// This is not really a part of the EPub class, but IF you have errors and want to know about them,
//  they would have been written to the output buffer, preventing the book from being sent.
//  This behaviour is desired as the book will then most likely be corrupt.
//  However you might want to dump the output to a log, this example section can do that:
/*
if (ob_get_contents() !== false && ob_get_contents() != '') {
	$f = fopen ('./log.txt', 'a') or die("Unable to open log.txt.");
	fwrite($f, "\r\n" . date("D, d M Y H:i:s T") . ": Error in " . __FILE__ . ": \r\n");
	fwrite($f, ob_get_contents() . "\r\n");
	fclose($f);
}
*/

// Send the book to the client. ".epub" will be appended if missing.
$zipData = $book->sendBook("Book{$tbsn}");

// After this point your script should call exit. If anything is written to the output,
// it'll be appended to the end of the book, causing the epub file to become corrupt.
?>