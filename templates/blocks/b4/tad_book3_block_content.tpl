<link rel="stylesheet" type="text/css" media="screen" href="<{$xoops_url}>/modules/tad_book3/module.css">
<div class="row">
    <div class="col-sm-3 text-center">
        <{if $block.book}>
            <div style="width: 145px; height: 250px; float: left; padding: 0px; border: 0px; margin: 5px 10px 40px; text-align: center;" id="tr_<{$block.book.tbsn}>">
                <a href="<{$xoops_url}>/modules/tad_book3/index.php?op=list_docs&tbsn=<{$block.book.tbsn}>">
                    <img src="<{$block.book.pic}>" alt="<{$block.book.title}>" style="width: 120px; height: 170px;" class="img-thumbnail img-fluid">
                </a>
                <div style="text-align:center;line-height: 1.5;margin-bottom: 10px;">
                    <a href="index.php?op=list_docs&tbsn=<{$block.book.tbsn}>"><{$block.book.title}></a>
                </div>
            </div>
        <{/if}>
    </div>

    <div class="col-sm-9">
        <h3>
            <span class="badge badge-success"><{$block.cate}></span>
            <{$block.title}>
        </h3>
        <div style="font-size:  68.75%; margin: 10px 0px;"><{$smarty.const._MB_TADBOOK3_CREATE_DATE}> <{$block.create_date}></div>
        <{if $block.description}><div class="alert alert-info"><{$block.description}></div><{/if}>
    </div>
</div>


<{if $block.needpasswd=='1'}>
    <div class="alert alert-danger">
        <form action="<{$xoops_url}>/modules/tad_book3/index.php" method="post" id="myForm">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><{$smarty.const._MB_TADBOOK3_INPUT_PASSWD}></span>
                </div>
                <input type="text" name="passwd" class="form-control">
                <span class="input-group-btn">
                    <input type="hidden" name="tbsn" value=<{$tbsn}>>
                    <input type="hidden" name="op" value="check_passwd">
                    <button type="submit" class="btn btn-primary"><{$smarty.const._TAD_SUBMIT}></button>
                </span>
            </div>
        </form>
    </div>
<{elseif $block.docs}>
    <h2><{$block.book_content}></h2>
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
                    <a href="<{$xoops_url}>/modules/tad_book3/page.php?tbdsn=<{$doc.tbdsn}>"><{$doc.title}></a>
                </td>
                <td style="font-size: 62.5%; color: gray; text-align: right;">
                    <{$doc.count}>
                    <i class="fa fa-user"></i>
                    <{$doc.last_modify_date}>
                </td>
            </tr>
        <{/foreach}>
    </table>
<{/if}>