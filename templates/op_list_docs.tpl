<div class="row">
    <div class="col-sm-3">
        <{if $book}>
            <div class="text-center">
                <{includeq file="$xoops_rootpath/modules/tad_book3/templates/sub_tadbook3_book_shadow.tpl"}>
            </div>
        <{/if}>
    </div>
    <div class="clearfix"></div>

    <div class="col-sm-9">
        <h2 class="my">
            <{$title}>
        </h2>

        <div style="font-size: 0.8rem; margin: 10px 0px;">
            <span class="badge badge-success"><{$cate}></span>
            <{$smarty.const._MD_TADBOOK3_CREATE_DATE}> <{$create_date}>
        </div>

        <{if $description}>
            <div class="alert alert-info"><{$description}></div>
        <{/if}>

        <div class="text-right">
            <{if $my}>
                <!--a href="index.php?op=tad_book3_export&tbsn=<{$tbsn}>" class="btn btn-sm btn-info"><{$smarty.const._MD_TADBOOK3_EXPORT}></a-->
            <{/if}>
            <{if $use_social_tools1}>
                <{$push_url}>
            <{/if}>
        </div>
    </div>
</div>


<{if $needpasswd=='1'}>
    <div class="alert alert-danger">
        <form action="index.php" method="post" id="myForm">
            <div class="input-group">
                <div class="input-group-prepend input-group-addon">
                    <span class="input-group-text"><{$smarty.const._MD_TADBOOK3_INPUT_PASSWD}></span>
                </div>
                <input type="text" name="passwd" class="form-control">
                <div class="input-group-append input-group-btn">
                    <input type="hidden" name="tbsn" value=<{$tbsn}>>
                    <input type="hidden" name="op" value="check_passwd">
                    <button type="submit" class="btn btn-primary"><{$smarty.const._TAD_SUBMIT}></button>
                </div>
            </div>
        </form>
    </div>
<{elseif $docs}>
    <div class="row">
        <div class="col-sm-6">
            <h3 class="my">
                <{$book_content}>
            </h3>
        </div>
        <div class="col-sm-6">

            <div class="text-right">
                <a href="https://www.addtoany.com/add_to/printfriendly?linkurl=<{$xoops_url}>%2Fmodules%2Ftad_book3%2Fhtml_all.php%3Ftbsn%3D<{$tbsn}>&amp;linkname="
                    target="_blank" class="btn btn-success">
                    <i class="fa fa-file-pdf-o"></i>
                    <{$smarty.const._MD_TADBOOK3_DL_HTML}> &
                    <{$smarty.const._MD_TADBOOK3_DL_PDF}>
                </a>
            </div>
        </div>
    </div>

    <form action="index.php" method="post">
        <table class="table table-hover">
            <{foreach from=$docs item=doc}>
            <tr>
                <td>
                    <span class="doc_sort_<{$doc.doc_sort_level}>">
                        <{if $doc.doc_sort_main==$doc.new_sort.main}>
                            <{$doc.doc_sort_main}>
                        <{else}>
                            <span style="color:red;" title="<{$doc.doc_sort_main}>"><{$doc.new_sort.main}></span>
                            <input type="hidden" name="update_sort[<{$doc.tbdsn}>]" value="<{$doc.new_sort.main}>">
                        <{/if}>
                        <{$doc.enable_txt}>
                        <a href="<{$xoops_url}>/modules/tad_book3/page.php?tbsn=<{$tbsn}>&tbdsn=<{$doc.tbdsn}>"><{$doc.title}></a>
                    </span>
                </td>
                <td style="font-size: 0.765rem; color: gray; text-align: right;white-space: nowrap;">
                    <{if $doc.time}>
                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                        <{$doc.percentage}>%
                        <i class="fa fa-youtube-play" aria-hidden="true"></i>
                        <{$doc.time}>
                    <{/if}>
                </td>
                <td style="font-size: 0.765rem; color: gray; text-align: right;white-space: nowrap;">
                    <{$doc.count}>
                    <i class="fa fa-user"></i>
                    <{$doc.last_modify_date}>
                </td>

                <{if $my}>
                    <td style="white-space: nowrap;">
                        <a href="<{$xoops_url}>/modules/tad_book3/post.php?op=tad_book3_docs_form&tbdsn=<{$doc.tbdsn}>" class="btn btn-sm btn-xs btn-warning"><{$smarty.const._TAD_EDIT}></a>

                        <{if $doc.enable=='1'}>
                            <a href="<{$xoops_url}>/modules/tad_book3/index.php?op=change_enable&enable=0&tbdsn=<{$doc.tbdsn}>&tbsn=<{$tbsn}>" class="btn btn-sm btn-xs btn-secondary btn-default"><{$smarty.const._TAD_UNABLE}></a>
                        <{else}>
                            <a href="<{$xoops_url}>/modules/tad_book3/post.php?op=change_enable&enable=1&tbdsn=<{$doc.tbdsn}>&tbsn=<{$tbsn}>" class="btn btn-sm btn-xs btn-success"><{$smarty.const._TAD_ENABLE}></a>
                        <{/if}>

                        <{if $doc.have_sub == 0}>
                            <a href="javascript:delete_tad_book3_docs_func(<{$doc.tbdsn}>);" class="btn btn-sm btn-xs btn-danger"><{$smarty.const._TAD_DEL}></a>
                        <{/if}>
                    </td>
                <{/if}>

            </tr>
            <{/foreach}>
        </table>
        <{if $my}>
            <div class="bar">
                <input type="hidden" name="tbsn" value="<{$tbsn}>">
                <input type="hidden" name="op" value="update_docs_sort">
                <button type="submit" class="btn btn-primary"><{$smarty.const._MD_TADBOOK3_MODIFY_ORDER}></button>
            </div>
        <{/if}>
    </form>
<{/if}>