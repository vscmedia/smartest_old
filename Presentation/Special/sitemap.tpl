<ul class="site-map" id="tree-root">
  {defun name="menurecursion" list=$site_tree}
    
    {capture name="foreach_name" assign="foreach_name"}list_{if $page.info.id}{$page.info.id}{else}0{/if}{/capture}
    {capture name="foreach_id" assign="foreach_id"}{if $page.info.id}{$page.info.id}{else}0{/if}{/capture}
    {foreach from=$list item="page" name=$foreach_name}
    
    {if $page.info.type == 'NORMAL' && $page.info.is_published == 'TRUE'}<li {if $smarty.foreach.$foreach_name.last}class="last"{elseif $smarty.foreach.$foreach_name.first}class="first"{else}class="middle"{/if}>
      
      <a id="item_{$page.info.webid}" class="option" href="{$page.info.url}">		 
        <img border="0" src="{$domain}Resources/Icons/page.gif" />{$page.info.title}</a>
      
      {if !empty($page.children)}
      
      <ul class="site-map" id="{$foreach_name}_{$smarty.foreach.$foreach_name.iteration}">
        {fun name="menurecursion" list=$page.children}
      </ul>
      
      {/if}
      
    </li>{/if}
    {/foreach}
  {/defun}
</ul>
