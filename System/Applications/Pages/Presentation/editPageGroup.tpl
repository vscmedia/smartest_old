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
  <div class="edit-form-layout">
    <div class="edit-form-row">
      <div class="form-section-label">Label</div>
      <p class="editable" id="pagegroup-label">{$pagegroup.label}</p>
      <script type="text/javascript">
      new Ajax.InPlaceEditor('pagegroup-label', sm_domain+'ajax:websitemanager/setPageGroupLabelFromInPlaceEditField', {ldelim}
        callback: function(form, value) {ldelim}
          return 'pagegroup_id={$pagegroup.id}&new_label='+encodeURIComponent(value);
        {rdelim},
        highlightColor: '#ffffff',
        hoverClassName: 'editable-hover',
        savingClassName: 'editable-saving'
      {rdelim});
      </script>
    </div>
    
    <table width="100%" border="0" cellpadding="0" cellspacing="5" style="border:1px solid #ccc">
      <tr>
        <td align="center">
          <div style="text-align:left">Pages that <strong>aren't</strong> in this group</div>

  		    <select name="available_items[]"  id="available_items" size="2" multiple style="width:270px; height:300px;"  onclick="setMode('add')">

{foreach from=$non_members key="key" item="item"}
  		      <option value="{$item.id}" >{$item.name}</option>
{/foreach}

  		    </select>

  		  </td>
      
        <td valign="middle" style="width:40px">
    		  <input type="button" value="&gt;&gt;" id="add_button" disabled="disabled" onclick="executeTransfer();" /><br /><br />
          <input type="button" value="&lt;&lt;" id="remove_button" disabled="disabled" onclick="executeTransfer();" />
        </td>
      
        <td align="center">
          <div style="text-align:left">Pages that <strong>are</strong> in this group</div>
   	      <select name="used_items[]"  id='used_items' size="2" multiple style="width:270px; height:300px" onclick="setMode('remove')" >	
{foreach from=$members key="key" item="item"}
  	      	<option value="{$item.id}" >{$item.name}</option>
{/foreach}
          </select>
  	    </td>
      </tr>
    </table>
    
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