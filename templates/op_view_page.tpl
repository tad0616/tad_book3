<{if $needpasswd=='1'}>
    <h3 class="sr-only visually-hidden">Need Password</h3>
    <div class="alert alert-danger">
        <form action="page.php" method="post" id="myForm">
            <div class="input-group">
                <div class="input-group-prepend input-group-addon">
                    <span class="input-group-text"><{$smarty.const._MD_TADBOOK3_INPUT_PASSWD}></span>
                </div>
                <input type="text" name="passwd" class="form-control" >
                <div class="input-group-append input-group-btn">
                    <input type="hidden" name="tbsn" value=<{$tbsn|default:''}>>
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
        <div class="col-sm-4 text-left text-start">
            <{$p|default:''}>
        </div>
        <div class="col-sm-4 text-center">
            <select onChange="window.location.href='page.php?tbsn=<{$tbsn|default:''}>&tbdsn='+this.value" class="form-control" title="Select Document">
                <{$doc_select|default:''}>
            </select>
        </div>
        <div class="col-sm-4 text-right text-end">
            <{$n|default:''}>
        </div>
    </div>


    <div class="row page">
        <div class="col-sm-12">
            <div class="page_title">
                <{if $book_title|default:false}>
                    <a href="index.php?op=list_docs&tbsn=<{$tbsn|default:''}>"><{$book_title|default:''}></a>
                <{else}>
                    <a href="index.php?op=list_docs&tbsn=<{$tbsn|default:''}>"><{$tbsn|default:''}></a>
                <{/if}>
            </div>
            <div class="page_content">
                <{if $doc_sort_level|default:false}>
                    <h<{$doc_sort_level|default:''}>>
                        <{$doc_sort_main|default:''}>
                        <{$title|default:''}>
                    </h<{$doc_sort_level|default:''}>>
                <{else}>
                    <h3 class="sr-only visually-hidden">Empty Title</h3>
                <{/if}>

                <{if $player|default:false}>
                    <{if $now_uid|default:false}>
                        <{if $view_video|default:false}>
                            <{if $view_video_ts && $view_video_ts > $now}>
                                <div class="card bg-dark text-white">
                                    <img class="card-img" src="<{$video_thumb|default:''}>" alt="<{$smarty.const._MD_TADBOOK3_CANT_VIEW_VIDEO}>">
                                    <div class="card-img-overlay row align-items-center justify-content-center text-center">
                                        <div class="alert alert-danger">
                                            <h2 class="card-title"><{$smarty.const._MD_TADBOOK3_VIDEO_DATE|sprintf:$view_video_date}></h2>
                                        </div>
                                    </div>
                                </div>
                            <{else}>
                                <{$player|default:''}>
                            <{/if}>
                        <{else}>
                            <div class="card bg-dark text-white">
                                <img class="card-img" src="<{$video_thumb|default:''}>" alt="<{$smarty.const._MD_TADBOOK3_CANT_VIEW_VIDEO}>">
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
                            <img class="card-img" src="<{$video_thumb|default:''}>" alt="<{$smarty.const._MD_TADBOOK3_CANT_VIEW_VIDEO}>">
                            <div class="card-img-overlay row align-items-center justify-content-center text-center">
                                <div class="alert alert-danger">
                                    <h2 class="card-title"><{$smarty.const._MD_TADBOOK3_CANT_VIEW_VIDEO}></h2>
                                    <p class="card-text"><{$smarty.const._MD_TADBOOK3_LOGIN_TO_VIEW_VIDEO}></p>
                                </div>
                            </div>
                        </div>
                    <{/if}>
                <{/if}>
                <{$content|default:''}>
            </div>
        </div>
    </div>

    <div class="row" style="background-image: url(images/relink_bg.gif); padding: 10px 0px;">
        <div class="col-sm-4 text-left text-start">
            <{$p|default:''}>
        </div>
        <div class="col-sm-4 text-center">
            <select onChange="window.location.href='page.php?tbsn=<{$tbsn|default:''}>&tbdsn='+this.value" class="form-control" title="Select Document">
                <{$doc_select|default:''}>
            </select>
        </div>
        <div class="col-sm-4 text-right text-end">
            <{$n|default:''}>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-sm-6" style="text-align:left;vertical-align:bottom;">
            <a href="https://www.addtoany.com/add_to/printfriendly?linkurl=<{$xoops_url}>%2Fmodules%2Ftad_book3%2Fhtml.php%3Ftbdsn%3D<{$tbdsn|default:''}>&amp;linkname="
                target="_blank">
                <i class="fa fa-file-pdf-o"></i>
                <{$smarty.const._MD_TADBOOK3_DL_HTML}> &
                <{$smarty.const._MD_TADBOOK3_DL_PDF}>
            </a>

            <a href="<{$xoops_url}>/modules/tad_book3/markdown.php?tbdsn=<{$tbdsn|default:''}>">
                <i class="fa fa-github-alt"></i>
                <{$smarty.const._MD_TADBOOK3_DL_MARKDOWN}>
            </a>
        </div>
        <div class="col-sm-6" style="text-align:right;vertical-align:bottom;">
            <{if $use_social_tools1|default:false}>
                <{$push_url|default:''}>
            <{/if}>
        </div>
    </div>
<{/if}>