<{if $block.book|default:false}>
    <div style="width: 145px; height: 230px; display:inline-block; padding: 0px; border: 0px; margin: 5px 10px 10px; text-align: center;" id="book_<{$block.book.tbsn}>">
        <div class="book">
            <a class="book-container" href="<{$xoops_url}>/modules/tad_book3/index.php?op=list_docs&tbsn=<{$block.book.tbsn}>" rel="noreferrer noopener">
                <img alt="<{$block.book.title|default:''}>" src="<{$block.book.pic}>">
                <span class="sr-only visually-hidden">book:<{$block.book.title|default:''}></span>
            </a>
        </div>
        <div style="margin: 10px auto;text-align:center;line-height: 1.5;margin-bottom: 10px;">
            <a href="<{$xoops_url}>/modules/tad_book3/index.php?op=list_docs&tbsn=<{$block.book.tbsn}>"><{$block.book.title}></a>
        </div>
        <span style="font-size: 0.8rem; margin: 10px 0px;display: inline-block;"><{$smarty.const._MB_TADBOOK3_CREATE_DATE}> <{$block.create_date}></span>
        <{if $block.description|default:false}><div class="alert alert-info"><{$block.description}></div><{/if}>
    </div>
<{/if}>


<{if $block.needpasswd|default:''=='1'}>
    <div class="alert alert-danger">
        <form action="<{$xoops_url}>/modules/tad_book3/index.php" method="post" id="myForm">
            <div class="input-group">
                <div class="input-group-prepend input-group-addon">
                    <span class="input-group-text"><{$smarty.const._MB_TADBOOK3_INPUT_PASSWD}></span>
                </div>
                <input type="text" name="passwd" class="form-control">
                <div class="input-group-append input-group-btn">
                    <input type="hidden" name="tbsn" value=<{$tbsn|default:''}>>
                    <input type="hidden" name="op" value="check_passwd">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i>  <{$smarty.const._TAD_SUBMIT}></button>
                </div>
            </div>
        </form>
    </div>
<{elseif $block.docs|default:false}>
    <h3 class="my"><{$block.book_content|default:''}></h3>
    <table class="table table-hover">
        <{foreach from=$block.docs item=doc}>
            <tr>
                <td>
                    <span class="doc_sort_<{$doc.doc_sort_level}>">
                        <{if $doc.doc_sort_main==$doc.new_sort.main}>
                            <{$doc.doc_sort_main}>
                        <{else}>
                            <span style="color:red;" title="<{$doc.doc_sort_main}>"><{$doc.new_sort.main}></span>
                            <input type="hidden" name="update_sort[<{$doc.tbdsn}>]" value="<{$doc.new_sort.main}>">
                        <{/if}>
                    </span>
                    <{$doc.enable_txt}>
                    <{if $doc.content|default:false || $doc.from_tbdsn|default:false}>
                        <a href="<{$xoops_url}>/modules/tad_book3/page.php?tbsn=<{$tbsn|default:''}>&tbdsn=<{$doc.tbdsn}>"><{$doc.title}></a>
                    <{else}>
                        <{$doc.title}>
                    <{/if}>
                </td>
            </tr>
        <{/foreach}>
    </table>
<{/if}>