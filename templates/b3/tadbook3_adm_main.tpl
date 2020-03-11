<div class="container-fluid">
  <div id="save_msg"></div>
  <div class="row">
    <div class="col-sm-3">
      <{$ztree_code}>

      <{if $tbcsn!="" and $op!="tad_book3_cate_form"}>
        <div>
          <h3><{$cate.title}></h3>
          <ul>
            <li style="line-height:2;"><{$smarty.const._MA_TADBOOK3_COUNT}><{$smarty.const._TAD_FOR}><{$total}></li>
          </ul>
        </div>
      <{/if}>

      <div class="text-center">
        <a href="main.php?op=tad_book3_cate_form" class="btn btn-info btn-block"><{$smarty.const._MA_TADBOOK3_ADD_CATE}></a>
      </div>
    </div>
    <div class="col-sm-9">
      <{if $tbcsn!="" and $op!="tad_book3_cate_form"}>
        <div class="row">
          <div class="col-sm-4">
            <h3>
              <{$cate.title}>
            </h3>
          </div>
          <div class="col-sm-8 text-right">
            <div style="margin-top: 10px;">
              <{if $now_op!="tad_book3_cate_form" and $tbcsn}>
                <a href="javascript:delete_tad_book3_cate_func(<{$cate.tbcsn}>);" class="btn btn-danger <{if $cate.count > 0}>disabled<{/if}>"><{$smarty.const._TAD_DEL}></a>
                <a href="main.php?op=tad_book3_cate_form&tbcsn=<{$tbcsn}>" class="btn btn-warning"><{$smarty.const._TAD_EDIT}></a>
              <{/if}>
            </div>
          </div>
        </div>

        <{if $cate.description}>
          <div class="row">
            <div class="col-sm-12">
              <div class="alert alert-success"><{$cate.description}></div>
            </div>
          </div>
        <{/if}>
      <{/if}>

      <{if $op=="tad_book3_cate_form"}>
        <h3><{$smarty.const._MA_TADBOOK3_CATE_FORM}></h3>

        <form action="main.php" method="post" id="myForm" enctype="multipart/form-data" class="form-horizontal" role="form">
          <div class="form-group">
            <label class="col-sm-2 control-label">
              <{$smarty.const._MA_TADBOOK3_CATE_TITLE}>
            </label>
            <div class="col-sm-10">
              <input type="text" name="title" size="20" value="<{$title}>" id="title" class="validate[required] form-control">
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label">
              <{$smarty.const._MA_TADBOOK3_CATE_DESCRIPTION}>
            </label>
            <div class="col-sm-10">
              <{$editor}>
            </div>
          </div>


          <div class="form-group">
            <label class="col-sm-2 control-label">
            </label>
            <div class="col-sm-10">
              <input type="hidden" name="tbcsn" value="<{$tbcsn}>">
              <input type="hidden" name="sort" value="<{$sort}>">
              <input type="hidden" name="op" value="<{$next_op}>">
              <button type="submit" class="btn btn-primary"><{$smarty.const._TAD_SAVE}></button>
            </div>
          </div>

        </form>
      <{elseif $books}>
        <form action="main.php" method="post" class="form-horizontal" role="form">
          <table class="table table-striped table-bordered">
            <tr>
              <th nowrap><{$smarty.const._MA_TADBOOK3_TITLE}></th>
              <th nowrap><{$smarty.const._MA_TADBOOK3_READ_GROUP}></th>
              <th nowrap><{$smarty.const._MA_TADBOOK3_AUTHOR}></th>
              <th nowrap><{$smarty.const._TAD_FUNCTION}></th>
            </tr>
            <tbody>
              <{foreach item=book from=$books}>
                <tr>
                  <td>
                    <a href="<{$xoops_url}>/modules/tad_book3/index.php?tbsn=<{$book.tbsn}>"><{$book.title}></a>
                    <span style="color:gray;font-size: 75%;"> (<{$book.counter}>)</span>
                    <{$book.create_date}>
                    <{$book.passwd}>
                  </td>
                  <td><{$book.read_groups}></td>
                  <td><{$book.author}></td>
                  <td>
                    <a href="javascript:delete_tad_book3_func(<{$book.tbsn}>);" class="btn btn-xs btn-danger" id="del<{$book.tbsn}>"><{$smarty.const._TAD_DEL}></a>
                    <a href="<{$xoops_url}>/modules/tad_book3/index.php?op=tad_book3_form&tbsn=<{$book.tbsn}>" class="btn btn-xs btn-info" id="update<{$book.tbsn}>"><{$smarty.const._TAD_EDIT}></a>
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