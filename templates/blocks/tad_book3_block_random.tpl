<{foreach from=$block item=book}>
    <div style="width: 145px; height: 230px; display:inline-block; padding: 0px; border: 0px; margin: 5px 10px 10px; text-align: center;" id="book_<{$book.tbsn}>" class="pull-left float-left">

        <a class="book-container" href="<{$xoops_url}>/modules/tad_book3/index.php?op=list_docs&tbsn=<{$book.tbsn}>" rel="noreferrer noopener">
            <div class="book">
                <img alt="<{$book.title}>" src="<{$book.pic}>"><span class="sr-only">book:<{$book.title}></span>
            </div>
        </a>
        <{if $book.show_title}>
            <div style="margin: 10px auto;text-align:center;line-height: 1.5;margin-bottom: 10px;">
                <a href="<{$xoops_url}>/modules/tad_book3/index.php?op=list_docs&tbsn=<{$book.tbsn}>"><{$book.title}></a> (<{$book.counter}>)
            </div>
        <{/if}>
    </div>
<{/foreach}>
<div class="clearfix"></div>
