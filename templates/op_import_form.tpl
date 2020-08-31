<div class="row">
    <h2 class="my"><{$smarty.const._MD_INPUT_BOOK_FORM}></h2>
    <div class="alert alert-info">
        <{$upload_note}>
    </div>
    <form action="<{$action}>" method="post" id="myForm" enctype="multipart/form-data">

        <div class="row">
            <div class="col-sm-3">
                <label>
                    <{$smarty.const._MD_TADBOOK3_TBCSN_MENU}>
                </label>
                <select name="tbcsn" size=1 class="form-control">
                    <{$cate_select}>
                </select>
                <input type="text" name="new_tbcsn" class="form-control" placeholder="<{$smarty.const._MD_TADBOOK3_NEW_PCSN}>">
            </div>

            <div class="col-sm-3">
                <label><{$smarty.const._MD_TADBOOK3_AUTHOR}></label>
                <{$user_menu}>
            </div>
            <div class="col-sm-3">
                <label><{$smarty.const._MD_TADBOOK3_READ_GROUP}></label>
                <{$group_menu}>
            </div>
            <div class="col-sm-3">
                <label><{$smarty.const._MD_TADBOOK3_IMPORT1}></label>
                <input type="file" name="book" class="form-control">
                <label><{$smarty.const._MD_TADBOOK3_IMPORT2}></label>
                <input type="file" name="docs" class="form-control">
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <label class="checkbox">
                    <input type="checkbox" name="abs_path" value="1" <{$checked}>>
                    <{$new_path}>
                </label>

                <input type="hidden" name="tbsn" value="<{$tbsn}>">
                <input type="hidden" name="op" value="import_book">
                <button type="submit" class="btn btn-primary"><{$smarty.const._MD_TADBOOK3_IMPORT}></button>
            </div>
        </div>

    </form>
</div>