<h3 class="my"><{$smarty.const._MD_INPUT_DOC_FORM}></h3>

<form action="<{$action|default:''}>" method="post" id="myForm" enctype="multipart/form-data" role="form" class="form-horizontal">

    <div class="form-group row mb-3">
        <label class="col-sm-1 control-label col-form-label text-md-right">
            <{$smarty.const._MD_TADBOOK3_TITLE}>
        </label>
        <div class="col-sm-5">
            <select name="tbsn" class="form-control">
                <{$book_select|default:''}>
            </select>
        </div>

        <label class="col-sm-1 control-label col-form-label text-md-right">
            <{$smarty.const._MD_TADBOOK3_STATUS}>
        </label>
        <div class="col-sm-2">
            <div class="form-check-inline radio-inline">
                <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="enable" id="enable1" value="1" <{if $enable=="1"}>checked<{/if}>>
                    <{$smarty.const._MD_TADBOOK3_ENABLE}>
                </label>
            </div>
            <div class="form-check-inline radio-inline">
                <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="enable" id="enable0" value="0" <{if $enable=="0"}>checked<{/if}>>
                    <{$smarty.const._MD_TADBOOK3_UNABLE}>
                </label>
            </div>
        </div>
        <label class="col-sm-1 control-label col-form-label text-md-right">
            <{if $from_tbdsn|default:false}>
                <a href="page.php?tbdsn=<{$from_tbdsn|default:''}>" target="_blank"><{$smarty.const._MD_TADBOOK3_FROM_TBDSN}></a>
            <{else}>
                <{$smarty.const._MD_TADBOOK3_FROM_TBDSN}>
            <{/if}>
        </label>
        <div class="col-sm-2">
            <input type="text" name="from_tbdsn" id="from_tbdsn" value="<{$from_tbdsn|default:''}>" class="form-control" placeholder="<{$smarty.const._MD_TADBOOK3_FROM_TBDSN_DESC}>">
        </div>
    </div>

    <div class="form-group row mb-3">
        <label class="col-sm-1 control-label col-form-label text-md-right">
            <{$smarty.const._MD_TADBOOK3_DOC_TITLE}>
        </label>
        <div class="col-sm-5">
            <input type="text" name="title" id="title" value="<{$title|default:''}>" class="form-control" placeholder="<{$smarty.const._MD_TADBOOK3_DOC_TITLE}>">
        </div>

        <label class="col-sm-1 control-label col-form-label text-md-right">
        <{$smarty.const._MD_TADBOOK3_CATEGORY}>
        </label>
        <div class="col-sm-5">
            <select name="category" size=1 class="form-control" style="width: 23%; display: inline;">
                <{$category_menu_category|default:''}>
            </select>
            <input type="hidden" name="category_old" value="<{$category|default:''}>">
            <select name="page" size=1 class="form-control" style="width: 23%; display: inline;">
                <{$category_menu_page|default:''}>
            </select>
            <input type="hidden" name="page_old" value="<{$page|default:''}>">
            <select name="paragraph" size=1 class="form-control" style="width: 23%; display: inline;">
                <{$category_menu_paragraph|default:''}>
            </select>
            <input type="hidden" name="paragraph_old" value="<{$paragraph|default:''}>">
            <select name="sort" size=1 class="form-control" style="width: 23%; display: inline;">
                <{$category_menu_sort|default:''}>
            </select>
            <input type="hidden" name="sort_old" value="<{$sort|default:''}>">
        </div>
    </div>


    <div class="form-group row mb-3">
        <div class="col-sm-12">
        <{$editor|default:''}>
        </div>
    </div>

    <div class="form-group row mb-3">
        <label class="col-sm-1 control-label col-form-label text-md-right">
            <{$smarty.const._MD_TADBOOK3_MP4}>
        </label>
        <div class="col-sm-11">
            <{$upform|default:''}>
        </div>
    </div>

    <div class="form-group row mb-3">
        <label class="col-sm-1 control-label col-form-label text-md-right">
            <{$smarty.const._MD_TADBOOK3_VTT}>
        </label>
        <div class="col-sm-11">
            <{$upform_vtt|default:''}>
        </div>
    </div>

    <{if $upform_pic|default:false}>
        <div class="form-group row mb-3">
            <label class="col-sm-1 control-label col-form-label text-md-right">
                <{$smarty.const._MD_TADBOOK3_SCREENSHOT}>
            </label>
            <div class="col-sm-11">
                <{$upform_pic|default:''}>
            </div>
        </div>
    <{/if}>

    <div class="form-group row mb-3">
        <label class="col-sm-1 control-label col-form-label text-md-right">
            <{$smarty.const._MD_TADBOOK3_READ_GROUP}>
        </label>
        <div class="col-md-3">
            <{$group_menu|default:''}>
        </div>
        <div class="col-md-7">
            <ol>
                <li><{$smarty.const._MD_TADBOOK3_READ_GROUP}><{$smarty.const._MD_TADBOOK3_START_DATE_SETUP}>
                    <table class="table table-sm">
                        <tbody>
                            <{foreach from=$read_group_arr key=i item=gid name=read_group_arr}>
                                <{if $gid==''}>
                                    <tr>
                                        <td style="width:30%"><{$smarty.const._MD_TADBOOK3_ALL_GROUP}></td>
                                        <td style="width:70%"><input class="form-control form-control-sm" type="text" name="read_group_date[]" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss' , startDate:'%y-%M-%d %H:%m:00}'})" value="<{if $read_group_date.0}><{$read_group_date.0.0}><{else}><{$now|default:''}><{/if}>"></td>
                                    </tr>
                                <{else}>
                                    <tr>
                                        <td style="width:30%"><{$groups.$gid}></td>
                                        <td style="width:70%"><input class="form-control form-control-sm" type="text" name="read_group_date[<{$gid|default:''}>]" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss' , startDate:'%y-%M-%d %H:%m:00}'})" value="<{if $read_group_date.$gid}><{$read_group_date.$gid.0}><{else}><{$now|default:''}><{/if}>"></td>
                                    </tr>
                                <{/if}>
                            <{/foreach}>
                        </tbody>
                    </table>
                </li>
            <ol>
        </div>
    </div>

    <div class="form-group row mb-3">
        <label class="col-sm-1 control-label col-form-label text-md-right">
            <{$smarty.const._MD_TADBOOK3_VIDEO_GROUP}>
        </label>
        <div class="col-md-3">
            <{$video_group_menu|default:''}>
        </div>
        <div class="col-md-7">
            <ol>
                <li><{$smarty.const._MD_TADBOOK3_VIDEO_GROUP}><{$smarty.const._MD_TADBOOK3_START_DATE_SETUP}>
                    <table class="table table-sm">
                        <tbody>
                            <{foreach from=$video_group_arr key=i item=gid name=video_group_arr}>
                                <{if $gid==''}>
                                    <tr>
                                        <td style="width:30%"><{$smarty.const._MD_TADBOOK3_ALL_GROUP}></td>
                                        <td style="width:70%"><input class="form-control form-control-sm" type="text" name="video_group_date[]" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss' , startDate:'%y-%M-%d %H:%m:00}'})" value="<{if $video_group_date.0}><{$video_group_date.0.0}><{else}><{$now|default:''}><{/if}>"></td>
                                    </tr>
                                <{else}>
                                    <tr>
                                        <td style="width:30%"><{$groups.$gid}></td>
                                        <td style="width:70%"><input class="form-control form-control-sm" type="text" name="video_group_date[<{$gid|default:''}>]" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss' , startDate:'%y-%M-%d %H:%m:00}'})" value="<{if $video_group_date.$gid}><{$video_group_date.$gid.0}><{else}><{$now|default:''}><{/if}>"></td>
                                    </tr>
                                <{/if}>
                            <{/foreach}>
                        </tbody>
                    </table>
                </li>
            <ol>
        </div>
    </div>

    <div class="form-group row mb-3">
        <label class="col-sm-1 control-label col-form-label text-md-right">
            <{$smarty.const._MD_TADBOOK3_ABOUT}>
        </label>
        <div class="col-md-10">
            <ol>
            <li><{$smarty.const._MD_TADBOOK3_ABOUT_1}></li>
            <li><{$smarty.const._MD_TADBOOK3_ABOUT_2}></li>
            <{if $tbdsn|default:false}>
                <li><{$smarty.const._MD_TADBOOK3_ABOUT_3}>
                    <div class="form-check-inline checkbox-inline">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="update_child_power" value="1" checked>
                            <{$smarty.const._MD_TADBOOK3_APPLY_ALL}>
                        </label>
                    </div>
                </li>
            <{/if}>
            <ol>
        </div>
    </div>


    <div class="bar">
        <input type="hidden" name="tbdsn" value="<{$tbdsn|default:''}>">
        <input type="hidden" name="op" value="<{$op|default:''}>">
        <button type="submit" class="btn btn-primary"><{$smarty.const._TAD_SAVE}></button>
    </div>

</form>