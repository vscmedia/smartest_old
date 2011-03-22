  <h3>Create a New Page</h3>
  
  <div class="instruction">Step 1: Choose which type of page you're going to make</div>
  <form action="{$domain}smartest/page/new" method="post">
    
    {if $parent_page}<input type="hidden" name="page_parent" value="{$parent_page.id}" />{/if}
    <input type="hidden" name="stage" value="2">
    
    <div class="edit-form-row">
      <div class="form-section-label">Title</div>
  	  <input type="text" name="page_title" id="page_title" value="Untitled Page" />
  	  {literal}<script type="text/javascript">var titleChanged=false;$('page_title').observe('change', function(){titleChanged=true});</script>{/literal}
  	</div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Type</div>
      <select name="page_type">
        <option value="NORMAL" selected="selected">Regular Web-page</option>
        <option value="ITEMCLASS">Object Meta-page</option>
        {* <option value="TAG">Tag list-page</option> *}
      </select>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Position in your site hierarchy</div>
  	  {if $parent_page}
  	  Child of page "{$parent_page.title}"
  	  {else}
  	  <select name="page_parent">
{foreach from=$parent_pages item="available_parent"}
        <option value="{$available_parent.info.id}">+{section name="dashes" loop=$available_parent.treeLevel}-{/section} Child of page "{$available_parent.info.title}"</option>
{/foreach}
  	  </select>
  	  {/if}
  	</div>
    
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="submit" value="Next &gt;&gt;" />
      </div>
    </div>
    
  </form>