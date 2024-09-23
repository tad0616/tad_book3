<h2 class="my"><{$smarty.const._MD_INPUT_BOOK_FORM}></h2>
<form action="<{$action|default:''}>" method="post" id="myForm" enctype="multipart/form-data" role="form" class="form-horizontal">
    <div class="form-group row mb-3">
        <label class="col-sm-2 control-label col-form-label text-md-right"><{$smarty.const._MD_TADBOOK3_TBCSN_MENU}></label>
        <div class="col-sm-3">
            <select name="tbcsn" size=1 class="form-control">
            <{$cate_select|default:''}>
            </select>
        </div>
        <div class="col-sm-3">
            <input type="text" name="new_tbcsn"  class="form-control" placeholder="<{$smarty.const._MD_TADBOOK3_NEW_PCSN}>">
        </div>
        <label class="col-sm-1 control-label col-form-label text-md-right">
            <{$smarty.const._MD_TADBOOK3_STATUS}>
        </label>
        <div class="col-sm-3">
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
    </div>

    <div class="form-group row mb-3">
        <label class="col-sm-2 control-label col-form-label text-md-right">
            <{$smarty.const._MD_TADBOOK3_TITLE}>
        </label>
        <div class="col-sm-6">
            <input type="text" name="title" value="<{$title|default:''}>" class="form-control" placeholder="<{$smarty.const._MD_TADBOOK3_TITLE}>">
        </div>

        <label class="col-sm-1 control-label col-form-label text-md-right">
            <{$smarty.const._MD_TADBOOK3_PIC_NAME}>
        </label>
        <div class="col-sm-3">
            <input type="file" name="pic_name" class="form-control" placeholder="<{$smarty.const._MD_TADBOOK3_PIC_NAME}>">
        </div>
    </div>

    <div class="form-group row mb-3">
        <div class="col-sm-12">
            <{$editor|default:''}>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <label><{$smarty.const._MD_TADBOOK3_AUTHOR}></label>
            <{$user_menu|default:''}>
        </div>
        <div class="col-md-3">
            <label><{$smarty.const._MD_TADBOOK3_READ_GROUP}></label>
            <{$group_menu|default:''}>
        </div>
        <div class="col-md-3">
            <label><{$smarty.const._MD_TADBOOK3_VIDEO_GROUP}></label>
            <{$video_group_menu|default:''}>
        </div>
        <div class="col-md-3">
            <label><{$smarty.const._MD_TADBOOK3_PASSWD}></label>

            <input type="text" name="passwd" class="form-control" value="<{$passwd|default:''}>">

            <div class="bar">
                <input type="hidden" name="sort"  value="<{$sort|default:''}>">
                <input type="hidden" name="tbsn" value="<{$tbsn|default:''}>">
                <input type="hidden" name="op" value="<{$op|default:''}>">
                <button type="submit" class="btn btn-primary"><{$smarty.const._TAD_SAVE}></button>
            </div>
        </div>
    </div>
</form>