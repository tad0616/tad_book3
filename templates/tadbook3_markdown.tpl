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
    <div class="row" style="background-image: url(images/relink_bg.gif); padding: 10px 0px;">
      <div class="col-md-4 text-left"><{$p}></div>
      <div class="col-md-4 text-center">
        <select onChange="window.location.href='markdown.php?tbdsn='+this.value">
          <{$doc_select}>
        </select>
      </div>
      <div class="col-md-4 text-right"><{$n}></div>
    </div>

    <textarea name="markdown" id="markdown" rows="100" class="form-control"><{$markdown}></textarea>
<{/if}>