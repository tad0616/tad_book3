<{$toolbar}>
<link rel="stylesheet" type="text/css" media="screen" href="module.css" />

<{if $now_op=="tad_book3_form"}>
  <div class="row">
    <div class="col-md-12">
      <h2><{$smarty.const._MD_INPUT_BOOK_FORM}></h2>
      <form action="<{$action}>" method="post" id="myForm" enctype="multipart/form-data" class="form-horizontal" role="form">

      <div class="row">
        <div class="form-group">
          <label class="col-md-2 control-label"><{$smarty.const._MD_TADBOOK3_TBCSN_MENU}></label>
          <div class="col-md-3">
            <select name="tbcsn" size=1 class="form-control">
              <{$cate_select}>
            </select>
          </div>
          <div class="col-md-2">
            <input type="text" name="new_tbcsn"  class="form-control" placeholder="<{$smarty.const._MD_TADBOOK3_NEW_PCSN}>">
          </div>
          <label class="col-md-1 control-label">
            <{$smarty.const._MD_TADBOOK3_STATUS}>
          </label>
          <div class="col-md-4">
            <label class="radio-inline">
              <input type="radio" name="enable" id="enable1" value="1" <{if $enable=="1"}>checked<{/if}>><{$smarty.const._MD_TADBOOK3_ENABLE}>
            </label>
            <label class="radio-inline">
              <input type="radio" name="enable" id="enable0" value="0" <{if $enable=="0"}>checked<{/if}>><{$smarty.const._MD_TADBOOK3_UNABLE}>
            </label>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="form-group">
          <label class="col-md-2 control-label">
            <{$smarty.const._MD_TADBOOK3_TITLE}>
          </label>
          <div class="col-md-5">
            <input type="text" name="title" value="<{$title}>" class="form-control" placeholder="<{$smarty.const._MD_TADBOOK3_TITLE}>">
          </div>

          <label class="col-md-1 control-label">
            <{$smarty.const._MD_TADBOOK3_PIC_NAME}>
          </label>
          <div class="col-md-4">
            <input type="file" name="pic_name" placeholder="<{$smarty.const._MD_TADBOOK3_PIC_NAME}>">
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <{$editor}>
        </div>
      </div>

      <br>

      <div class="row">
        <div class="form-group">

          <label class="col-md-1 control-label"><{$smarty.const._MD_TADBOOK3_AUTHOR}></label>
          <div class="col-md-3">
            <{$user_menu}>
          </div>

          <label class="col-md-1 control-label"><{$smarty.const._MD_TADBOOK3_READ_GROUP}></label>
          <div class="col-md-3">
            <{$group_menu}>
          </div>

          <label class="col-md-1 control-label">
            <{$smarty.const._MD_TADBOOK3_PASSWD}>
          </label>
          <div class="col-md-3">
            <input type="text" name="passwd" class="form-control" value="<{$passwd}>">


            <p class="text-center">
              <input type="hidden" name="sort"  value="<{$sort}>">
              <input type="hidden" name="tbsn" value="<{$tbsn}>">
              <input type="hidden" name="op" value="<{$op}>">
              <button type="submit" class="btn btn-primary"><{$smarty.const._TAD_SAVE}></button>
            </p>
          </div>
        </div>
      </div>
      </form>
    </div>
  </div>
<{elseif $now_op=="import_form"}>
  <div class="row">
    <h2><{$smarty.const._MD_INPUT_BOOK_FORM}></h2>
    <div class="alert alert-info">
      <{$upload_note}>
    </div>
    <form action="<{$action}>" method="post" id="myForm" enctype="multipart/form-data">

    <div class="row">

      <div class="col-md-3">
        <label>
          <{$smarty.const._MD_TADBOOK3_TBCSN_MENU}>
        </label>
        <select name="tbcsn" size=1 class="col-md-12">
          <{$cate_select}>
        </select>
        <input type="text" name="new_tbcsn" class="col-md-12" placeholder="<{$smarty.const._MD_TADBOOK3_NEW_PCSN}>">
      </div>


      <div class="col-md-3">
        <label><{$smarty.const._MD_TADBOOK3_AUTHOR}></label>
        <{$user_menu}>
      </div>
      <div class="col-md-3">
        <label><{$smarty.const._MD_TADBOOK3_READ_GROUP}></label>
        <{$group_menu}>
      </div>
      <div class="col-md-3">

        <label><{$smarty.const._MD_TADBOOK3_IMPORT1}></label>
        <input type="file" name="book" class="col-md-12">
        <label><{$smarty.const._MD_TADBOOK3_IMPORT2}></label>
        <input type="file" name="docs" class="col-md-12">
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
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
<{elseif $now_op=="list_docs"}>
  <{if $my}>
    <{$sweet_alert_docs_code}>
  <{/if}>

  <div class="row">
    <div class="col-md-3 text-center">
      <{if $book}>
        <{includeq file="db:tadbook3_book_shadow.tpl"}>
      <{/if}>
    </div>

    <div class="col-md-9">
      <h3>
        <span class="label label-success"><{$cate}></span>
        <{$title}>
      </h3>
      <div style="font-size: 11px; margin: 10px 0px;"><{$smarty.const._MD_TADBOOK3_CREATE_DATE}> <{$create_date}></div>

      <{if $description}><div class="alert alert-info"><{$description}></div><{/if}>
      <div class="text-right">
        <{if $my}>
          <!--a href="index.php?op=tad_book3_export&tbsn=<{$tbsn}>" class="btn btn-xs btn-info"><{$smarty.const._MD_TADBOOK3_EXPORT}></a-->
        <{/if}>
        <{$push_url}>
      </div>
    </div>
  </div>


  <{if $needpasswd=='1'}>
    <div class="alert alert-danger">
      <form action="index.php" method="post" id="myForm">
        <div class="input-group">
          <span class="input-group-addon"><{$smarty.const._MD_TADBOOK3_INPUT_PASSWD}></span>
          <input type="text" name="passwd" class="form-control">
          <span class="input-group-btn">
            <input type="hidden" name="tbsn" value=<{$tbsn}>>
            <input type="hidden" name="op" value="check_passwd">
            <button type="submit" class="btn btn-primary"><{$smarty.const._TAD_SUBMIT}></button>
          </span>
        </div>
      </form>
    </div>
  <{elseif $docs}>
    <h2><{$book_content}></h2>
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
              </span>
              <{$doc.enable_txt}>
              <a href="<{$xoops_url}>/modules/tad_book3/page.php?tbdsn=<{$doc.tbdsn}>"><{$doc.title}></a>
            </td>
            <td style="font-size: 10px; color: gray; text-align: right;">
              <{$doc.count}>
              <i class="fa fa-user"></i>
              <{$doc.last_modify_date}>
            </td>

            <{if $my}>
              <td>
                <a href="<{$xoops_url}>/modules/tad_book3/post.php?op=tad_book3_docs_form&tbdsn=<{$doc.tbdsn}>" class="btn btn-xs btn-warning"><{$smarty.const._TAD_EDIT}></a>

                <{if $doc.enable=='1'}>
                  <a href="<{$xoops_url}>/modules/tad_book3/index.php?op=change_enable&enable=0&tbdsn=<{$doc.tbdsn}>&tbsn=<{$tbsn}>" class="btn btn-xs btn-default"><{$smarty.const._TAD_UNABLE}></a>
                <{else}>
                  <a href="<{$xoops_url}>/modules/tad_book3/post.php?op=change_enable&enable=1&tbdsn=<{$doc.tbdsn}>&tbsn=<{$tbsn}>" class="btn btn-xs btn-success"><{$smarty.const._TAD_ENABLE}></a>
                <{/if}>

                <{if $doc.have_sub == 0}>
                  <a href="javascript:delete_tad_book3_docs_func(<{$doc.tbdsn}>);" class="btn btn-xs btn-danger"><{$smarty.const._TAD_DEL}></a>
                <{/if}>
              </td>
            <{/if}>

          </tr>
        <{/foreach}>
      </table>
      <{if $my}>
        <div class="text-center">
          <input type="hidden" name="tbsn" value="<{$tbsn}>">
          <input type="hidden" name="op" value="update_docs_sort">
          <button type="submit" class="btn btn-primary"><{$smarty.const._MD_TADBOOK3_MODIFY_ORDER}></button>
        </div>
      <{/if}>
    </form>
  <{/if}>
<{else}>

  <{$jquery}>

  <div id="save_msg"></div>

  <script language="JavaScript">
    $().ready(function(){
      $("#books_sort").sortable({
        opacity: 0.6,
        cursor: "move",
        update: function() {
          var order = $(this).sortable("serialize") + "&action=updateRecordsListings";
          $.post("save_book_sort.php", order, function(theResponse){
            $("#save_msg").html(theResponse);
          });
        }
      });
    });
  </script>
  <{$sweet_alert_book_code}>

  <{foreach from=$cate item=cate}>
    <{if $cate.books}>
      <h1 style="color:#A0A0A0;margin-top:20px;font-size:24px;"><{$cate.title}></h1>
      <div style='margin-left:20px;' id='books_sort'>

      <{foreach from=$cate.books item=book}>
        <{if $book}>
          <{includeq file="db:tadbook3_book_shadow.tpl"}>
        <{/if}>
      <{/foreach}>
      </div><div style='clear:both;'></div>
    <{/if}>
  <{/foreach}>
<{/if}>
