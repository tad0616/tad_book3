<div style="width: 145px; height: 250px; display:inline-block; padding: 0px; border: 0px; margin: 5px 10px 40px;text-align: center;" id="book_<{$book.tbsn}>" class="pull-left float-left">

    <a class="book-container" href="<{$xoops_url}>/modules/tad_book3/index.php?op=list_docs&tbsn=<{$book.tbsn}>" rel="noreferrer noopener">
        <div class="book">
            <img alt="<{$book.title}>" src="<{$book.pic}>"><span class="sr-only visually-hidden">book:<{$book.title}></span>
        </div>
    </a>
    <{if $book.tool|default:false}>
        <div style="margin: 10px auto;  ">
            <a href="<{$xoops_url}>/modules/tad_book3/index.php?op=tad_book3_form&tbsn=<{$book.tbsn}>" class="btn btn-sm btn-warning" title="<{$smarty.const._TAD_EDIT}>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
            <a href="javascript:delete_tad_book3_func(<{$book.tbsn}>);" class="btn btn-sm btn-danger" title="<{$smarty.const._TAD_DEL}>"><i class="fa fa-times" aria-hidden="true"></i></a>
            <a href="<{$xoops_url}>/modules/tad_book3/post.php?tbsn=<{$book.tbsn}>&op=tad_book3_docs_form" class="btn btn-sm btn-primary" title="<{$smarty.const._MD_TADBOOK3_ADD_DOC}>"><i class="fa fa-plus" aria-hidden="true"></i></a>
        </div>
    <{/if}>

    <div style="margin: 10px auto;line-height: 1.5;margin-bottom: 10px;">
        <{if $book.title|default:false}>
            <a href="index.php?op=list_docs&tbsn=<{$book.tbsn}>"><{$book.title}></a>
        <{else}>
            <a href="index.php?op=list_docs&tbsn=<{$book.tbsn}>"><{$book.tbsn}></a>
        <{/if}>
    </div>
</div>