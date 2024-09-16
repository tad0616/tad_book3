<h2>
<a href="index.php?op=list_docs&tbsn=<{$book.tbsn}>" target="_blank"><{$book.title}></a>
<{$smarty.const._MD_TADBOOK3_READING_STATUS}>
</h2>

<{if $group_users|default:false}>

    <link href="class/ScrollTable/superTables.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="class/ScrollTable/superTables.js"></script>
    <script type="text/javascript" src="class/ScrollTable/jquery.superTable.js"></script>
    <script>
        $(document).ready(function(){
            <{foreach from=$group_users key=group_name item=users name=group_users}>
                <{assign var='cc' value=$users|@count}>
                <{assign var='rowcount' value=$cc+4}>
                <{assign var='hh' value=$rowcount*28}>
                <{if $hh > 600}>
                    <{assign var='hh' value=600}>
                <{/if}>
                $('#tad_book3_log<{$smarty.foreach.group_users.index}>').toSuperTable({'width': '100%','height': '<{$hh}>px', 'headerRows':4 , fixedCols: 1 });
            <{/foreach}>
        });
    </script>
    <style>
        #tad_book3_log td{
            font-size: 0.825rem;
        }
    </style>
    <{foreach from=$group_users key=group_name item=users name=group_users}>

        <h3><{$group_name}>(<{$users|@count}>)</h3>
        <table id="tad_book3_log<{$smarty.foreach.group_users.index}>">
            <tbody>
                <tr>
                    <th class="c" rowspan=4><{$smarty.const._MD_TADBOOK3_VIEWER}></th>
                    <{foreach from=$level key=category item=category_docs}>
                        <{if $category%2}>
                            <{assign var='bgcolor' value='#fff'}>
                        <{else}>
                            <{assign var='bgcolor' value='#f2fbfc'}>
                        <{/if}>
                        <td class="c" style="background:<{$bgcolor}>;" colspan=<{$count1.$category}>><{$category}> (<{$category_log.$group_name.$category|@count}>)</td>
                    <{/foreach}>
                </tr>
                <tr>
                    <{foreach from=$level key=category item=category_docs}>
                        <{if $category%2}>
                            <{assign var='bgcolor' value='#fff'}>
                        <{else}>
                            <{assign var='bgcolor' value='#f2fbfc'}>
                        <{/if}>
                        <{foreach from=$category_docs key=page item=page_docs}>
                            <td class="c" style="background:<{$bgcolor}>;" colspan=<{$count2.$category.$page}>><{if $page|default:false}><{$page}><{/if}></td>
                        <{/foreach}>
                    <{/foreach}>
                </tr>
                <tr>
                    <{foreach from=$level key=category item=category_docs}>
                        <{if $category%2}>
                            <{assign var='bgcolor' value='#fff'}>
                        <{else}>
                            <{assign var='bgcolor' value='#f2fbfc'}>
                        <{/if}>
                        <{foreach from=$category_docs key=page item=page_docs}>
                            <{foreach from=$page_docs key=paragraph item=paragraph_docs}>
                                <td class="c" style="background:<{$bgcolor}>;" colspan=<{$paragraph_docs|@count}>><{if $paragraph|default:false}><{$paragraph}><{/if}></td>
                            <{/foreach}>
                        <{/foreach}>
                    <{/foreach}>
                </tr>
                <tr>
                    <{foreach from=$level key=category item=category_docs}>
                        <{if $category%2}>
                            <{assign var='bgcolor' value='#fff'}>
                        <{else}>
                            <{assign var='bgcolor' value='#f2fbfc'}>
                        <{/if}>
                        <{foreach from=$category_docs key=page item=page_docs}>
                            <{foreach from=$page_docs key=paragraph item=paragraph_docs}>
                                <{foreach from=$paragraph_docs key=sort item=doc}>
                                    <td class="c" style="background:<{$bgcolor}>;"><{if $sort|default:false}><{$sort}><{/if}></td>
                                <{/foreach}>
                            <{/foreach}>
                        <{/foreach}>
                    <{/foreach}>
                </tr>
                <{foreach from=$users key=uid item=user}>
                    <tr>
                        <td nowrap><{$user.name}></td>
                        <{foreach from=$level key=category item=category_docs}>
                            <{if $category%2}>
                                <{assign var='bgcolor' value='#fff'}>
                            <{else}>
                                <{assign var='bgcolor' value='#f2fbfc'}>
                            <{/if}>
                            <{foreach from=$category_docs key=page item=page_docs}>
                                <{foreach from=$page_docs key=paragraph item=paragraph_docs}>
                                    <{foreach from=$paragraph_docs key=sort item=doc}>
                                        <{assign var="tbdsn" value=$doc.tbdsn}>
                                        <td class="c" style="background:<{$bgcolor}>;">
                                            <{if $user.log.$tbdsn && $doc.lengths}>
                                                <{assign var="v" value=$user.log.$tbdsn/$doc.lengths}>
                                                <{assign var="vv" value=$v|round:2}>
                                                <{assign var="percentage" value=$vv*100}>
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

<{else}>
    <div class="alert alert-warning">
        <{$smarty.const._MD_TADBOOK3_UNABLE_LOG}>
    </div>
<{/if}>