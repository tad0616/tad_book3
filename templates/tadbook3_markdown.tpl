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