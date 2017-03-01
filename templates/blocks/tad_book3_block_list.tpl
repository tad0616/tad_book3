<ul>
  <{foreach from=$block item=book}>
  <li>
    <a href="<{$xoops_url}>/modules/tad_book3/index.php?op=list_docs&tbsn=<{$book.tbsn}>"><{$book.title}></a><{if $book.show_counter}> (<{$book.counter}>)<{/if}>
  </li>
  <{/foreach}>
</ul>