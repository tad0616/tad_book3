<{if $needpasswd=='1' }>
    <div class="alert alert-danger">
        <form action="page.php" method="post" id="myForm">
            <div class="input-group">
                <div class="input-group-prepend input-group-addon">
                    <span class="input-group-text"><{$smarty.const._MD_TADBOOK3_INPUT_PASSWD}></span>
                </div>
                <input type="text" name="passwd" class="form-control" >
                <div class="input-group-append input-group-btn">
                    <input type="hidden" name="tbsn" value=<{$tbsn}>>
                    <input type="hidden" name="op" value="check_passwd">
                    <button type="submit" class="btn btn-primary">
                    <{$smarty.const._TAD_SUBMIT}>
                    </button>
                </div>
            </div>
        </form>
    </div>

<{else}>

    <div class="row" style="background-image: url('images/relink_bg.gif'); padding: 10px 0px;">
        <div class="col-sm-4 text-left">
            <{$p}>
        </div>
        <div class="col-sm-4 text-center">
            <select onChange="window.location.href='page.php?tbsn=<{$tbsn}>&tbdsn='+this.value" class="form-control" title="Select Document">
                <{$doc_select}>
            </select>
        </div>
        <div class="col-sm-4 text-right">
            <{$n}>
        </div>
    </div>


    <div class="row page">
        <div class="col-sm-12">
            <div class="page_title">
                <a href="index.php?op=list_docs&tbsn=<{$tbsn}>">
                <{$book_title}>
                </a>
            </div>
            <div class="page_content">
                <h<{$doc_sort_level}>>
                    <{$doc_sort_main}>
                    <{$title}>
                </h<{$doc_sort_level}>>
                <{if $player}>
                    <{if $now_uid}>
                        <{if $view_video}>
                            <{$player}>
                        <{else}>
                            <div class="card bg-dark text-white">
                                <img class="card-img" src="<{$video_thumb}>" alt="<{$smarty.const._MD_TADBOOK3_CANT_VIEW_VIDEO}>">
                                <div class="card-img-overlay row align-items-center justify-content-center text-center">
                                    <div class="alert alert-danger">
                                        <h2 class="card-title"><{$smarty.const._MD_TADBOOK3_CANT_VIEW_VIDEO}></h2>
                                        <p class="card-text"><{$smarty.const._MD_TADBOOK3_NOT_VIEW_VIDEO_GROUP|sprintf:$video_group_txt}></p>
                                    </div>
                                </div>
                            </div>
                        <{/if}>
                    <{else}>
                        <div class="card bg-dark text-white">
                            <img class="card-img" src="<{$video_thumb}>" alt="<{$smarty.const._MD_TADBOOK3_CANT_VIEW_VIDEO}>">
                            <div class="card-img-overlay row align-items-center justify-content-center text-center">
                                <div class="alert alert-danger">
                                    <h2 class="card-title"><{$smarty.const._MD_TADBOOK3_CANT_VIEW_VIDEO}></h2>
                                    <p class="card-text">請先登入，登入後，確認您的權限後，即可觀看影片。</p>
                                </div>
                            </div>
                        </div>
                    <{/if}>
                <{/if}>
                <{$content}>
            </div>
        </div>
    </div>

    <div class="row" style="background-image: url(images/relink_bg.gif); padding: 10px 0px;">
        <div class="col-sm-4 text-left">
            <{$p}>
        </div>
        <div class="col-sm-4 text-center">
            <select onChange="window.location.href='page.php?tbsn=<{$tbsn}>&tbdsn='+this.value" class="form-control" title="Select Document">
                <{$doc_select}>
            </select>
        </div>
        <div class="col-sm-4 text-right">
            <{$n}>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-sm-6" style="text-align:left;vertical-align:bottom;">
            <a href="https://www.addtoany.com/add_to/printfriendly?linkurl=<{$xoops_url}>%2Fmodules%2Ftad_book3%2Fhtml.php%3Ftbdsn%3D<{$tbdsn}>&amp;linkname="
                target="_blank">
                <i class="fa fa-file-pdf-o"></i>
                <{$smarty.const._MD_TADBOOK3_DL_HTML}> &
                <{$smarty.const._MD_TADBOOK3_DL_PDF}>
            </a>

            <a href="<{$xoops_url}>/modules/tad_book3/markdown.php?tbdsn=<{$tbdsn}>">
                <i class="fa fa-github-alt"></i>
                <{$smarty.const._MD_TADBOOK3_DL_MARKDOWN}>
            </a>
        </div>
        <div class="col-sm-6" style="text-align:right;vertical-align:bottom;">
            <{if $use_social_tools1}>
                <{$push_url}>
            <{/if}>
        </div>
    </div>

    <{$facebook_comments}>


    <p style="clear: both">
        <div style="text-align: center; padding: 3px; margin: 3px;">
            <{$commentsnav}>
            <{$lang_notice}>
        </div>

        <div style="margin: 3px; padding: 3px;">
            <!-- start comments loop -->
            <{if $comment_mode=="flat" }>
                <{include file="db:system_comments_flat.html" }>
            <{elseif $comment_mode=="thread" }>
                <{include file="db:system_comments_thread.html" }>
            <{elseif $comment_mode=="nest" }>
                <{include file="db:system_comments_nest.html" }>
            <{/if}>
            <!-- end comments loop -->
        </div>
    </p>
<{/if}>