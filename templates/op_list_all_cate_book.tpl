<div id="save_msg"></div>
<h1 class="sr-only visually-hidden">All Books</h1>
<{foreach from=$cates key=id item=cate}>
        <{if $smarty.session.tad_book3_adm|default:false}>
            <script type="text/javascript">
                $(document).ready(function(){
                    $('#books_sort_<{$id|default:''}>').sortable({ opacity: 0.6, cursor: 'move', update: function() {
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

        <div id="books_sort_<{$id|default:''}>">
            <{foreach from=$cate.books item=book}>
                <{if $book|default:false}>
                    <{include file="$xoops_rootpath/modules/tad_book3/templates/sub_tadbook3_book_shadow.tpl"}>
                <{/if}>
            <{/foreach}>
        </div>
        <div class="clearfix"></div>

<{/foreach}>