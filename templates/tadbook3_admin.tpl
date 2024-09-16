<div class="container-fluid">
    <div id="save_msg"></div>
    <div class="row">
        <div class="col-sm-3">
            <{$ztree_code}>

            <{if $tbcsn!="" and $now_op!="tad_book3_cate_form"}>
                <div>
                    <h3 class="my"><{$cate.title}></h3>
                    <ul>
                        <li style="line-height:2;"><{$smarty.const._MA_TADBOOK3_COUNT}><{$smarty.const._TAD_FOR}><{$total}></li>
                    </ul>
                </div>
            <{/if}>

            <div class="text-center d-grid gap-2">
                <a href="main.php?op=tad_book3_cate_form" class="btn btn-info btn-block"><{$smarty.const._MA_TADBOOK3_ADD_CATE}></a>
            </div>
        </div>
        <div class="col-sm-9">
            <{if $tbcsn!="" and $now_op!="tad_book3_cate_form"}>
                <div class="row">
                    <div class="col-sm-4">
                    <h3>
                        <{$cate.title}>
                    </h3>
                    </div>
                    <div class="col-sm-8 text-right text-end">
                    <div style="margin-top: 10px;">
                        <{if $now_op!="tad_book3_cate_form" and $tbcsn}>
                        <a href="javascript:delete_tad_book3_cate_func(<{$cate.tbcsn}>);" class="btn btn-sm btn-danger <{if $cate.count > 0}>disabled<{/if}>"><{$smarty.const._TAD_DEL}></a>
                        <a href="main.php?op=tad_book3_cate_form&tbcsn=<{$tbcsn}>" class="btn btn-sm btn-warning"><{$smarty.const._TAD_EDIT}></a>
                        <{/if}>
                    </div>
                    </div>
                </div>

                <{if $cate.description|default:false}>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="alert alert-success"><{$cate.description}></div>
                        </div>
                    </div>
                <{/if}>
            <{/if}>

            <{if $now_op=="tad_book3_cate_form"}>
                <{include file="$xoops_rootpath/modules/tad_book3/templates/op_`$now_op`.tpl"}>
            <{elseif $books}>
                <form action="main.php" method="post" role="form">
                    <table class="table table-striped table-bordered">
                    <tr>
                        <th nowrap><{$smarty.const._MA_TADBOOK3_TITLE}></th>
                        <th nowrap><{$smarty.const._MA_TADBOOK3_READ_GROUP}></th>
                        <th nowrap><{$smarty.const._MA_TADBOOK3_VIDEO_GROUP}></th>
                        <th nowrap><{$smarty.const._MA_TADBOOK3_AUTHOR}></th>
                        <th nowrap><{$smarty.const._TAD_FUNCTION}></th>
                    </tr>
                    <tbody>
                        <{foreach from=$books item=book}>
                        <tr>
                            <td>
                            <a href="<{$xoops_url}>/modules/tad_book3/index.php?tbsn=<{$book.tbsn}>"><{$book.title}></a>
                            <span style="color:gray;font-size: 75%;"> (<{$book.counter}>)</span>
                            <{$book.create_date}>
                            <{$book.passwd}>
                            </td>
                            <td><{$book.read_groups}></td>
                            <td><{$book.video_groups}></td>
                            <td><{$book.author}></td>
                            <td>
                            <a href="javascript:delete_tad_book3_func(<{$book.tbsn}>);" class="btn btn-sm btn-danger" id="del<{$book.tbsn}>"><{$smarty.const._TAD_DEL}></a>
                            <a href="<{$xoops_url}>/modules/tad_book3/index.php?op=tad_book3_form&tbsn=<{$book.tbsn}>" class="btn btn-sm btn-info" id="update<{$book.tbsn}>"><{$smarty.const._TAD_EDIT}></a>
                            </td>
                        </tr>
                        <{/foreach}>
                    </tbody>
                    </table>
                    <{$bar}>
                </form>
            <{else}>
                <div class="alert alert-danger text-center">
                    <h3><{$smarty.const._MA_TADBOOK3_NO_BOOKS}></h3>
                </div>
            <{/if}>
        </div>
    </div>
</div>