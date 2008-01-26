{placeholder name="category_masthead_banner"}
{placeholder name="main-body-text"}

{if empty($this.navigation.child_pages)}
  
{else}

<div class="child-pages-list">

<h4>{if strlen($this.fields.child_page_list_heading)}{field name="child_page_list_heading"}{else}Read More{/if}</h4>

<ul>
  {foreach from=$this.navigation.child_pages item="sub_page"}
  {capture name="sub_page_link" assign="sub_page_link"}page:{$sub_page.name}{/capture}
  <li><h5>{$sub_page.title}</h5><p>{$sub_page.description|nl2br}</p>{link to=$sub_page_link with="Read More"}</li>
  {/foreach}
</ul>

</div>

{/if}