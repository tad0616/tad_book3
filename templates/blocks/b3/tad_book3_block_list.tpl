<{if $block.show_pic=='1'}>
    <{foreach from=$block.books item=book}>
        <div class="pull-left" style="width:120px;margin-right:10px;">
            <a href="<{$xoops_url}>/modules/tad_book3/index.php?op=list_docs&tbsn=<{$book.tbsn}>">
                <img src="<{$book.pic}>" alt="<{$book.title}>" class="img-thumbnail">
            </a>
            <div class="text-center">
                <a href="<{$xoops_url}>/modules/tad_book3/index.php?op=list_docs&tbsn=<{$book.tbsn}>"><{$book.title}></a><{if $book.show_counter}> (<{$book.counter}>)<{/if}>
            </div>
        </div>
    <{/foreach}>
    <div class="clearfix"></div>
<{else}>
    <ul class="vertical_menu">
        <{foreach from=$block.books item=book}>
            <li>
                <a href="<{$xoops_url}>/modules/tad_book3/index.php?op=list_docs&tbsn=<{$book.tbsn}>"><{$book.title}></a><{if $book.show_counter}> (<{$book.counter}>)<{/if}>
            </li>
        <{/foreach}>
    </ul>
<{/if}>