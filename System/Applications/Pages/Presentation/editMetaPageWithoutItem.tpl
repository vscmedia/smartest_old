<div class="instruction" style="margin-top:10px">You may still edit the basic parameters of this page:</div>

<div class="edit-form-row">
  <div class="form-section-label">Title</div>
  <p class="editable" id="page-title">{$page.title}</p>
  <script type="text/javascript">
  new Ajax.InPlaceEditor('page-title', sm_domain+'ajax:websitemanager/setPageValueFromAjaxForm', {ldelim}
    callback: function(form, value) {ldelim}
      return 'page_id={$page.webid}&name=title&value='+encodeURIComponent(value);
    {rdelim},
    highlightColor: '#ffffff',
    hoverClassName: 'editable-hover',
    savingClassName: 'editable-saving'
  {rdelim});
  </script>
</div>
{if $allow_edit_page_name}
<div class="edit-form-row">
  <div class="form-section-label">Short name</div>
  
  <p class="editable" id="page-name">{$page.name}</p>
  <script type="text/javascript">
  new Ajax.InPlaceEditor('page-name', sm_domain+'ajax:websitemanager/setPageValueFromAjaxForm', {ldelim}
    callback: function(form, value) {ldelim}
      return 'page_id={$page.webid}&name=name&value='+encodeURIComponent(value);
    {rdelim},
    highlightColor: '#ffffff',
    hoverClassName: 'editable-hover',
    savingClassName: 'editable-saving'
  {rdelim});
  </script>
</div>
{/if}
<div class="edit-form-row">
  <div class="form-section-label">URL</div>

	<div id="page-urls">

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
  		      {if $pageurl.is_default == 1}<strong>{/if}{if $link_urls.value && $page.is_published == "TRUE" && ($page.type == 'NORMAL' || ($page.type == 'ITEMCLASS' && $item.public == 'TRUE'))}<a href="{$pageUrl}" target="_blank">{$pageUrl|truncate:100:"..."}</a>{else}{$pageUrl|truncate:100:"..."}{/if}{if $pageurl.is_default == 1}</strong> (default){/if}</div></td>
  	    <td style="width:30%">
  		    <input type="button" name="edit" value="Edit" onclick="MODALS.load('{$section}/editPageUrl?url_id={$pageurl.id}&amp;responseTableLinks=false', 'Edit page URL');" />
  		    {if $ishomepage != "true"}<input type="button" name="mkdefault" value="Make Default" onclick="window.location='{$domain}{$section}/setPageDefaultUrl?page_id={$page.webid}&amp;url={$pageurl.id}'"{if $pageurl.is_default == 1 || $pageurl.type == 'SM_PAGEURL_INTERNAL_FORWARD' || $pageurl.type == 'SM_PAGEURL_ITEM_FORWARD'} disabled="disabled"{/if} />{/if}
  		    {if count($page.urls) > 1 || $ishomepage == "true"}<input type="button" name="delete" value="Delete" onclick="if(confirm('Are you sure you want to delete this URL?')) window.location='{$domain}{$section}/deletePageUrl?page_id={$page.webid}&amp;url={$pageurl.id}&amp;ishomepage={$ishomepage};'"/>{/if}</td></tr> 
      {/foreach}

	    {else}

  	  {/if}

  	  <tr style="background-color:#{cycle values="ddd,fff"};height:20px">
          <td>
            <div style="display:inline" id="siteDomainField">
            {if $link_urls.value && $page.is_published == "TRUE"}<a href="http://{$site.domain}{$domain}{$page.fallback_url}" target="_blank">http://{$site.domain}{$domain}{$page.fallback_url|truncate:50:"..."}</a>{else}http://{$site.domain}{$domain}{$page.fallback_url|truncate:100:"..."}{/if}</div></td>
    	    <td style="width:30%"></td></tr>

  	</table>

	</div>
	<a href="{$domain}{$section}/addPageUrl?page_id={$page.webid}">{if count($page.urls) || $ishomepage == "true"}Add another url{else}Give this page a nicer URL{/if}</a><br />
	<img src="{$domain}Resources/Images/spacer.gif" width="1" height="10" />
</div>
<div class="edit-form-row">
  <div class="form-section-label">Parent page</div>
  <form action="{$domain}ajax:websitemanager/setPageValueFromAjaxForm" method="post" id="parent-changer">
    <input type="hidden" name="page_id" value="{$page.webid}" />
    <input type="hidden" name="name" value="parent" />
    <select name="value">
      {foreach from=$parent_pages item="p_page"}
        {if $p_page.id != $page.id}
        <option value="{$p_page.info.id}"{if $page.parent.id == $p_page.info.id} selected="selected"{/if}>+{section name="dashes" loop=$p_page.treeLevel}-{/section} {$p_page.info.title}</option>
        {/if}
      {/foreach}
    </select>
    <input type="button" value="Save" id="parent-change-button" />
    <img src="{$domain}Resources/System/Images/ajax-loader.gif" style="display:none" alt="" id="parent-change-loader" />
    <script type="text/javascript">
    {literal}
      $('parent-change-button').observe('click', function(){
        $('parent-change-loader').show();
        $('parent-changer').request({
          onComplete: function(){ 
            $('parent-change-loader').hide();
          }});
      });
    {/literal}
    </script>
  </form>
</div>
<div class="edit-form-row">
  <div class="form-section-label">Cache frequency</div>
  <form action="{$domain}ajax:websitemanager/setPageValueFromAjaxForm" method="post" id="cachefreq-changer">
    <input type="hidden" name="page_id" value="{$page.webid}" />
    <input type="hidden" name="name" value="cache_frequency" />
    <select name="value">
      <option value="PERMANENT"{if $page.cache_interval=='PERMANENT'} selected="selected"{/if}>Stay Cached Until Re-Published</option>
  	  <option value="MONTHLY"{if $page.cache_interval=='MONTHLY'} selected="selected"{/if}>Every Month</option>
  	  <option value="DAILY"{if $page.cache_interval=='DAILY'} selected="selected"{/if}>Every Day</option>
  	  <option value="HOURLY"{if $page.cache_interval=='HOURLY'} selected="selected"{/if}>Every Hour</option>
  	  <option value="MINUTE"{if $page.cache_interval=='MINUTE'} selected="selected"{/if}>Every Minute</option>
  	  <option value="SECOND"{if $page.cache_interval=='SECOND'} selected="selected"{/if}>Every Second</option>
    </select>
    <input type="button" value="Save" id="cachefreq-change-button" />
    <img src="{$domain}Resources/System/Images/ajax-loader.gif" style="display:none" alt="" id="cachefreq-change-loader" />
    <script type="text/javascript">
    {literal}
      $('cachefreq-change-button').observe('click', function(){
        $('cachefreq-change-loader').show();
        $('cachefreq-changer').request({
          onComplete: function(){ 
            $('cachefreq-change-loader').hide();
          }});
      });
    {/literal}
    </script>
  </form>
</div>
<div class="edit-form-row">
  <div class="form-section-label">Always use static page title</div>
  <input type="checkbox" name="always_use_static_title" id="always-use-static-title"{if $page.force_static_title} checked="checked"{/if} />
  <label for="always-use-static-title" id="always-use-static-title-label">{if $page.force_static_title=='1'}Un-tick this box to make this meta-page have the title of the item that is being displayed.{else}Tick this box to make sure this meta-page keeps the title above, instead of the item that is being displayed.{/if}</label>
  <script type="text/javascript">
  var pid = '{$page.webid}';
  {literal}
  $('always-use-static-title').observe('click', function(){
    var url = sm_domain+'ajax:websitemanager/setPageValueFromAjaxForm';
    var checked = $('always-use-static-title').checked ? 1 : 0;
    var label = checked ? 'Un-tick this box to make this meta-page have the title of the item that is being displayed.' : 'Tick this box to make sure this meta-page keeps the title above, instead of the item that is being displayed.';
    $('always-use-static-title-label').update(label);
    new Ajax.Request(url, {
      method: 'post',
      parameters: {'page_id': pid, 'name': 'force_static_title', 'value': checked}
    });
    
  });
  {/literal}
  </script>
</div>

{$page.urls._count}