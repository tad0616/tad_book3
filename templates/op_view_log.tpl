<h2><{$book.title}> <{$smarty.const._MD_TADBOOK3_READING_STATUS}></h2>
<style>
    #tad_book3_log td{
        font-size: 0.825rem;
    }
</style>
<{foreach from=$group_users key=group_name item=users name=group_users}>
    <h3><{$group_name}></h3>
    <table class="table table-sm table-bordered table-responsive" id="tad_book3_log">
        <tbody>
            <tr>
                <th class="c" rowspan=4>學員</th>
                <{foreach from=$level key=category item=category_docs}>
                    <td class="c" colspan=<{$count1.$category}>><{$category}></td>
                <{/foreach}>
            </tr>
            <tr>
                <{foreach from=$level key=category item=category_docs}>
                    <{foreach from=$category_docs key=page item=page_docs}>
                        <td class="c" colspan=<{$count2.$category.$page}>><{if $page}><{$page}><{/if}></td>
                    <{/foreach}>
                <{/foreach}>
            </tr>
            <tr>
                <{foreach from=$level key=category item=category_docs}>
                    <{foreach from=$category_docs key=page item=page_docs}>
                        <{foreach from=$page_docs key=paragraph item=paragraph_docs}>
                            <td class="c" colspan=<{$paragraph_docs|@count}>><{if $paragraph}><{$paragraph}><{/if}></td>
                        <{/foreach}>
                    <{/foreach}>
                <{/foreach}>
            </tr>
            <tr>
                <{foreach from=$level key=category item=category_docs}>
                    <{foreach from=$category_docs key=page item=page_docs}>
                        <{foreach from=$page_docs key=paragraph item=paragraph_docs}>
                            <{foreach from=$paragraph_docs key=sort item=doc}>
                                <td class="c"><{if $sort}><{$sort}><{/if}></td>
                            <{/foreach}>
                        <{/foreach}>
                    <{/foreach}>
                <{/foreach}>
            </tr>
            <{foreach from=$users key=uid item=user}>
                <tr>
                    <td nowrap><{$user.name}></td>
                    <{foreach from=$level key=category item=category_docs}>
                        <{foreach from=$category_docs key=page item=page_docs}>
                            <{foreach from=$page_docs key=paragraph item=paragraph_docs}>
                                <{foreach from=$paragraph_docs key=sort item=doc}>
                                    <{assign var=tbdsn value=$doc.tbdsn}>
                                    <td class="c">
                                        <{if $user.log.$tbdsn && $doc.lengths}>
                                            <{assign var=v value=$user.log.$tbdsn/$doc.lengths}>
                                            <{assign var=vv value=$v|round:2}>
                                            <{assign var=percentage value=$vv*100}>
                                            <{$percentage}>
                                        <{elseif $doc.lengths}>
                                        <{else}>
                                            -
                                        <{/if}>
                                    </td>
                                <{/foreach}>
                            <{/foreach}>
                        <{/foreach}>
                    <{/foreach}>
                </tr>
            <{/foreach}>
        </tbody>
    </table>
<{/foreach}>