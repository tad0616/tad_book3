<{if $block|default:false}>
    <{foreach from=$block item=book}>
        <div>
            <div>
                <span class="badge badge-success bg-success">
                    <a href="<{$xoops_url}>/modules/tad_book3/index.php?tbsn=<{$book.tbsn}>" style="color: white;"><{$book.book_title}></a>
                </span>
            </div>
            <p>
                <{$book.doc_sort}>
                <a href="<{$xoops_url}>/modules/tad_book3/page.php?tbsn=<{$tbsn|default:''}>&tbdsn=<{$book.tbdsn}>"><{$book.title}></a>
            </p>
        </div>
    <{/foreach}>
<{/if}>