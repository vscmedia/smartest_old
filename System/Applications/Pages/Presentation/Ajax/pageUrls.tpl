<table width="100%" style="border:1px solid #ccc;padding:2px;" cellpadding="0" cellspacing="0">
  
  {if $ishomepage == "true"}
	<tr style="background-color:#{cycle values="ddd,fff"};height:20px">
	  <td>
		  <div style="display:inline" id="siteDomainField_0">
		    <strong>{if $page.is_published == "TRUE"}<a href="http://{$site.domain}{$domain}" target="_blank">{/if}http://{$site.domain}{$domain}{if $page.is_published == "TRUE"}</a>{/if}</strong> (default)</div></td>
	  <td style="width:30%">&nbsp;</td>
  </tr>
  {/if}
  
  {if count($page.urls)}
  
  {foreach from=$page.urls item=pageurl}
    {capture name="pageUrl" assign="pageUrl"}http://{$site.domain}{$domain}{$pageurl.url}{/capture}
  <tr style="background-color:#{cycle values="ddd,fff"};height:20px">
    <td>
	    <div style="display:inline" id="siteDomainField_{$pageurl.id}">
	      {if $pageurl.is_default == 1}<strong>{/if}{if $link_urls && $page.is_published == "TRUE" && ($page.type == 'NORMAL' || $page._php_class == "SmartestPage" || ($page.type == 'ITEMCLASS' && $item.public == 'TRUE'))}<a href="{$pageUrl}" target="_blank">{$pageUrl|truncate:100:"..."}</a>{else}{$pageUrl|truncate:100:"..."}{/if}{if $pageurl.is_default == 1}</strong> (default){/if}</div></td>
    <td style="width:30%">
	    <input type="button" name="edit" value="Edit" onclick="MODALS.load('{$section}/editPageUrl?url_id={$pageurl.id}', 'Edit page URL');" />
	    {if $ishomepage != "true"}<input type="button" name="mkdefault" value="Make Default" onclick="window.location='{$domain}{$section}/setPageDefaultUrl?page_id={$page.webid}&amp;url={$pageurl.id}'"{if $pageurl.is_default == 1 || $pageurl.type == 'SM_PAGEURL_INTERNAL_FORWARD' || $pageurl.type == 'SM_PAGEURL_ITEM_FORWARD'} disabled="disabled"{/if} />{/if}
	    {if count($page.urls) > 1 || $ishomepage == "true"}<input type="button" name="delete" value="Delete" onclick="if(confirm('Are you sure you want to delete this URL?')) window.location='{$domain}{$section}/deletePageUrl?page_id={$page.webid}&amp;url={$pageurl.id}&amp;ishomepage={$ishomepage};'"/>{/if}</td></tr> 
  {/foreach}
  
  {else}
    
  {/if}
  
  <tr style="background-color:#{cycle values="ddd,fff"};height:20px">
      <td>
        <div style="display:inline" id="siteDomainField">
        {if $link_urls && $page.is_published == "TRUE"}<a href="http://{$site.domain}{$domain}{$page.fallback_url}" target="_blank">http://{$site.domain}{$domain}{$page.fallback_url|truncate:50:"..."}</a>{else}http://{$site.domain}{$domain}{$page.fallback_url|truncate:100:"..."}{/if}</div></td>
	    <td style="width:30%"></td></tr>
  
</table>