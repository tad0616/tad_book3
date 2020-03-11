<div style="width: 145px; height: 250px; float: left; padding: 0px; border: 0px; margin: 5px 10px 40px; text-align: center;" id="tr_<{$book.tbsn}>">

  <a href="<{$xoops_url}>/modules/tad_book3/index.php?op=list_docs&tbsn=<{$book.tbsn}>">
    <img src="<{$book.pic}>" alt="<{$book.title}>" style="width: 120px; height: 170px;" class="img-thumbnail img-responsive">
  </a>

  <{if $book.tool}>
    <div style="margin: 10px auto; width: auto; font-size: 75%; font-weight: normal;">
      <a href="<{$xoops_url}>/modules/tad_book3/index.php?op=tad_book3_form&tbsn=<{$book.tbsn}>" class="btn btn-xs btn-warning" title="<{$smarty.const._TAD_EDIT}>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
      <a href="javascript:delete_tad_book3_func(<{$book.tbsn}>);" class="btn btn-xs btn-danger" title="<{$smarty.const._TAD_DEL}>"><i class="fa fa-times" aria-hidden="true"></i></a>
      <a href="<{$xoops_url}>/modules/tad_book3/post.php?tbsn=<{$book.tbsn}>&op=tad_book3_docs_form" class="btn btn-xs btn-primary" title="<{$smarty.const._MD_TADBOOK3_ADD_DOC}>"><i class="fa fa-plus" aria-hidden="true"></i></a>
    </div>
  <{/if}>

  <div style="text-align:center;line-height: 1.5;margin-bottom: 10px;">
    <a href="index.php?op=list_docs&tbsn=<{$book.tbsn}>"><{$book.title}></a>
  </div>
</div>