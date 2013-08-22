<script language="javascript">
{literal}

function setMode(mode){

	document.getElementById('transferAction').value=mode;

	if(mode == "add"){
		document.getElementById('add_button').disabled=false;
		document.getElementById('remove_button').disabled=true;
		
	}else if(mode == "remove"){
		document.getElementById('add_button').disabled=true;
		document.getElementById('remove_button').disabled=false;
		formList = document.getElementById('used_items');
	}	
	
}

function executeTransfer(){
	document.transferForm.submit();
}

{/literal}
</script>

<div id="work-area">
  <h3>Edit page group</h3>
  
  {load_interface file="page_group_tabs.tpl"}
  
  <div class="edit-form-layout">
    
    <div class="edit-form-row">
      <div class="form-section-label">Label</div>
      <p class="editable" id="pagegroup-label">{$group.label}</p>
      <script type="text/javascript">
      new Ajax.InPlaceEditor('pagegroup-label', sm_domain+'ajax:websitemanager/setPageGroupLabelFromInPlaceEditField', {ldelim}
        callback: function(form, value) {ldelim}
          return 'pagegroup_id={$group.id}&new_label='+encodeURIComponent(value);
        {rdelim},
        highlightColor: '#ffffff',
        hoverClassName: 'editable-hover',
        savingClassName: 'editable-saving'
      {rdelim});
      </script>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Name</div>
      <p class="editable" id="pagegroup-name">{$group.name}</p>
      <div class="form-hint">Used to access the page group from templates. Numbers, lowercase letters and underscores only</div>
      <script type="text/javascript">
      new Ajax.InPlaceEditor('pagegroup-name', sm_domain+'ajax:websitemanager/setPageGroupNameFromInPlaceEditField', {ldelim}
        callback: function(form, value) {ldelim}
          return 'pagegroup_id={$group.id}&new_name='+encodeURIComponent(value);
        {rdelim},
        highlightColor: '#ffffff',
        hoverClassName: 'editable-hover',
        savingClassName: 'editable-saving'
      {rdelim});
      </script>
    </div>
    
    <form action="{$domain}{$section}/transferPages" method="post" name="transferForm">
    
    <input type="hidden" id="transferAction" name="transferAction" value="" />
    <input type="hidden" name="group_id" value="{$group.id}" />
    
    <table width="100%" border="0" cellpadding="0" cellspacing="5" style="border:1px solid #ccc">
      <tr>
        <td align="center">
          <div style="text-align:left">Pages that <strong>aren't</strong> in this group</div>

  		    <select name="available_pages[]"  id="available_pages" size="2" multiple style="width:270px; height:300px;" onclick="setMode('add')">

{foreach from=$non_members key="key" item="page"}
  		      <option value="{$page.info.id}" >{$page.info.title}</option>
{/foreach}

  		    </select>

  		  </td>
      
        <td valign="middle" style="width:40px">
    		  <input type="button" value="&gt;&gt;" id="add_button" disabled="disabled" onclick="executeTransfer();" /><br /><br />
          <input type="button" value="&lt;&lt;" id="remove_button" disabled="disabled" onclick="executeTransfer();" />
        </td>
      
        <td align="center">
          <div style="text-align:left">Pages that <strong>are</strong> in this group</div>
   	      <select name="used_pages[]"  id='used_pages' size="2" multiple style="width:270px; height:300px" onclick="setMode('remove')" >	
{foreach from=$members key="key" item="lookup"}
  	      	<option value="{$lookup.page.id}" >{$lookup.order_index}. {$lookup.page.title}</option>
{/foreach}
          </select>
  	    </td>
      </tr>
    </table>
    
    </form>
    
  </div>
</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Page groups</b></li>
    <li class="permanent-action"><a href="{$domain}smartest/pagegroup/new" class="right-nav-link"><img src="{$domain}Resources/Icons/add.png" border="0" alt=""> Create another page group</a></li>
    <li class="permanent-action"><a href="{$domain}smartest/pagegroups" class="right-nav-link"><img src="{$domain}Resources/Icons/page.png" border="0" alt=""> Back to page groups</a></li>
    <li class="permanent-action"><a href="{$domain}smartest/pages" class="right-nav-link"><img src="{$domain}Resources/Icons/page.png" border="0" alt=""> Back to pages</a></li>
  </ul>
</div>