<h3 class="my"><{$smarty.const._MA_TADBOOK3_CATE_FORM}></h3>

<form action="main.php" method="post" id="myForm" enctype="multipart/form-data" role="form" class="form-horizontal">
    <div class="form-group row mb-3">
        <label class="col-sm-2 control-label col-form-label text-md-right text-md-end">
            <{$smarty.const._MA_TADBOOK3_CATE_TITLE}>
        </label>
        <div class="col-sm-10">
            <input type="text" name="title" size="20" value="<{$title|default:''}>" id="title" class="validate[required] form-control">
        </div>
    </div>

    <div class="form-group row mb-3">
        <label class="col-sm-2 control-label col-form-label text-md-right text-md-end">
            <{$smarty.const._MA_TADBOOK3_CATE_DESCRIPTION}>
        </label>
        <div class="col-sm-10">
            <{$editor|default:''}>
        </div>
    </div>


    <div class="bar">
        <input type="hidden" name="tbcsn" value="<{$tbcsn|default:''}>">
        <input type="hidden" name="sort" value="<{$sort|default:''}>">
        <input type="hidden" name="op" value="<{$next_op|default:''}>">
        <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-disk" aria-hidden="true"></i>  <{$smarty.const._TAD_SAVE}></button>
    </div>

</form>