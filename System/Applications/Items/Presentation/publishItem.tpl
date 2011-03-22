<div id="work-area">
  
  <h3>Publish options: {$item.name}</h3>
  
  <form action="{$domain}{$section}/publishItem" method="post">
    
    <input type="hidden" name="item_id" value="{$item.id}" />
    {if $request_parameters.page_id}<input type="hidden" name="page_id" value="{$request_parameters.page_id}" />{/if}
    {if $request_parameters.from}<input type="hidden" name="from" value="{$request_parameters.from}" />{/if}
    
    {if empty($metapages)}
    
    {if $metapages_publish_warning}
    <div class="warning">
      <p><strong>Warning: no {$item._model.name|strtolower} meta-pages</strong></p>
      <p>{$item._model.plural_name} can be listed or referred to on other pages, but won't have hyperlinks or URLs of their own, because the '{$item._model.name}' model does not have any meta-pages.</p>
      <p>You can continue to publish it, or you can hit 'Cancel' and create a metapage for it.</p>
      <div><input type="checkbox" value="1" name="hide_no_metapage_warning" id="hide-no-metapage-warning" />&nbsp;<label for="hide-no-metapage-warning">Don't show this message again</label></div>
    </div>
    {else}
    <div class="instruction">Are you sure you want to publish this {$item._model.name|strtolower}?</div>
    {/if}
    
    {else}
    
    {if $show_page_publish_option}
    <div class="edit-form-row">
      <div class="form-section-label">{if $meta_page.is_published == 'TRUE'}Re-publish{else}Publish{/if} meta-page where this {$item._model.name|strtolower} is viewed?</div>
      <select name="update_pages">
        <option value="IGNORE">No thanks</option>
        {if $meta_page.is_published == 'TRUE'}<option value="PERITEM"{if $meta_page_has_changed} selected="selected"{/if}>Only publish changes that affect this {$item._model.name|strtolower}</option>{/if}
        <option value="PUBLISH"{if $meta_page.is_published != 'TRUE'} selected="selected"{/if}>Yes, {if $meta_page.is_published == 'TRUE'}re-{/if}publish {if $meta_page_has_changed && $meta_page.is_published == 'TRUE'}entire {/if}meta-page '{$meta_page.title}'</option>
      </select>
      <input type="hidden" name="metapage_id" value="{$meta_page.id}" />
    </div>
    {else}
      {if $meta_page.is_published == 'TRUE'}{else}<div class="warning"><strong>Warning: Meta-page unavailable</strong><br />The default meta-page for this item is not currently published, but you don't have permission to publish it. You can publish this {$item._model.name|strtolower}, but most hyperlinks or URLs that correspond to it will not work until this page is published.</div>{/if}
    {/if}
    
    {if !empty($itemspaces)}
    <div class="edit-form-row">
      <div class="form-section-label">Update itemspaces where this {$item._model.name|strtolower} is chosen in draft?</div>
      <select name="update_itemspaces">
        <option value="IGNORE">No, I'll do that manually</option>
        <option value="PUBLISHIS" selected="selected">Yes{if $show_page_publish_option}, but don't publish the pages they appear on{else} please{/if}</option>
        {if $show_page_publish_option}<option value="PUBLISHPAGE" selected="selected">Yes, and publish the pages they appear on</option>{/if}
      </select>
    </div>
    {/if}
    
    {/if}
    
    <div class="buttons-bar">
      <input type="submit" value="Publish" />
      <input type="button" value="Cancel" onclick="cancelForm()" />
    </div>
    
  </form>
  
</div>
