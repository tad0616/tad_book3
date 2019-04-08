<{foreach from=$block item=book}>
  <div style="text-align:center;">
    <a href="<{$xoops_url}>/modules/tad_book3/index.php?op=list_docs&tbsn=<{$book.tbsn}>">
      <img src="<{$book.pic}>" alt="<{$book.title}>">
    </a>
    <br>
    <{$book.booktitle}>
  </div>
<{/foreach}>
