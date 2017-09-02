<{$toolbar}>

<{if $needpasswd=='1'}>
  <div class="alert alert-danger">
    <form action="page.php" method="post" id="myForm">
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
<{else}>

  <link rel="stylesheet" type="text/css" media="screen" href="module.css" >
  <link rel="stylesheet" type="text/css" media="screen" href="reset.css" >
  <link rel="stylesheet" type="text/css" media="screen" href="<{$xoops_url}>/modules/tadtools/css/kbdfun.css" >

  <div class="row" style="background-image: url(images/relink_bg.gif); padding: 10px 0px;">
    <div class="col-sm-4 text-left"><{$p}></div>
    <div class="col-sm-4 text-center">
      <select onChange="window.location.href='page.php?tbdsn='+this.value" class="form-control">
        <{$doc_select}>
      </select>
    </div>
    <div class="col-sm-4 text-right"><{$n}></div>
  </div>


  <div class="row ">
    <div class="col-sm-12 page">
      <div class="page_title">
        <a href="index.php?op=list_docs&tbsn=<{$tbsn}>"><{$book_title}></a>
      </div>
      <div class="page_content">
      <h<{$doc_sort_level}>><{$doc_sort_main}> <{$title}></h<{$doc_sort_level}>>
        <{$content}>
      </div>
    </div>
  </div>

  <div class="row" style="background-image: url(images/relink_bg.gif); padding: 10px 0px;">
    <div class="col-sm-4 text-left"><{$p}></div>
    <div class="col-sm-4 text-center">
      <select onChange="window.location.href='page.php?tbdsn='+this.value" class="form-control">
        <{$doc_select}>
      </select>
    </div>
    <div class="col-sm-4 text-right"><{$n}></div>
  </div>

  <br>

  <div class="row">
    <div class="col-sm-6" style="text-align:left;vertical-align:bottom;">
      <a href="<{$xoops_url}>/modules/tad_book3/html.php?tbdsn=<{$tbdsn}>"><i class="fa fa-print"></i> <{$smarty.const._MD_TADBOOK3_DL_HTML}></a>
      <a href="<{$xoops_url}>/modules/tad_book3/pdf.php?tbdsn=<{$tbdsn}>"><i class="fa fa-file-pdf-o"></i> <{$smarty.const._MD_TADBOOK3_DL_PDF}></a>

      <a href="<{$xoops_url}>/modules/tad_book3/markdown.php?tbdsn=<{$tbdsn}>"><i class="fa fa-github-alt"></i> <{$smarty.const._MD_TADBOOK3_DL_MARKDOWN}></a>
    </div>
    <div class="col-sm-6" style="text-align:right;vertical-align:bottom;">
      <{$push_url}>
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
  <{if $comment_mode == "flat"}>
    <{include file="db:system_comments_flat.html"}>
  <{elseif $comment_mode == "thread"}>
    <{include file="db:system_comments_thread.html"}>
  <{elseif $comment_mode == "nest"}>
    <{include file="db:system_comments_nest.html"}>
  <{/if}>
  <!-- end comments loop -->
  </div>
  </p>
<{/if}>