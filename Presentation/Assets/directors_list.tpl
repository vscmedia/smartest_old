{placeholder name="main-body-text"}

{if empty($this.navigation.child_pages)}
  
{else}

<div class="child-pages-list">

<h4>{if strlen($this.fields.child_page_list_heading)}{field name="child_page_list_heading"}{else}Read More{/if}</h4>

<table border="0" cellpadding="0" cellspacing="0" style="width:480px">
  {foreach from=$this.navigation.child_pages item="sub_page"}
  {capture name="sub_page_link" assign="sub_page_link"}page:{$sub_page.name}{/capture}
  <tr>
    <td valign="top">{if $sub_page.icon_image}<img style="float:left;display:block;margin-right:8px" src="{$domain}Resources/Images/{$sub_page.icon_image}" alt="" />{else}&nbsp;{/if}</td>
    <td style="padding-bottom:10px"><h5 style="color:#444">{$sub_page.title}</h5><p>{$sub_page.description|nl2br}</p>
      <a href="{url to=$sub_page_link}" style="font-size:12px">Read More</a><br /></td>
  </tr>
  {/foreach}
</table>

</div>

{/if}