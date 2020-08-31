<div id="save_msg"></div>

<{foreach from=$cate key=id item=cate}>
    <{if $cate.books}>
        <{if $smarty.session.tad_book3_adm}>
            <script type="text/javascript">
                $(document).ready(function(){
                    $('#books_sort_<{$id}>').sortable({ opacity: 0.6, cursor: 'move', update: function() {
                        var order = $(this).sortable('serialize');
                        $.post('save_book_sort.php', order, function(theResponse){
                            $('#save_msg').html(theResponse);
                        });
                    }
                    });
                });
            </script>
        <{/if}>

        <h2 class="my"><{$cate.title}></h2>

        <div id="books_sort_<{$id}>">
            <{foreach from=$cate.books item=book}>
                <{if $book}>
                    <{includeq file="$xoops_rootpath/modules/tad_book3/templates/sub_tadbook3_book_shadow.tpl"}>
                <{/if}>
            <{/foreach}>
        </div>
        <div class="clearfix"></div>
    <{/if}>
<{/foreach}>