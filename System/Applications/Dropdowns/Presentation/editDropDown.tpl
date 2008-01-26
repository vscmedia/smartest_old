<script language="javascript" type="text/javascript">
{literal}

function check(){
	var editForm = document.getElementById('pageViewForm');
	if(editForm.drop_down.value==''){
		alert ('please enter the DropDown Label');
		editForm.drop_down.focus();
		return false;
	}else{
		return true;
	}
}

{/literal}
</script>

<div id="work-area">

<h3><a href="{$domain}datamanager">Data Manager</a> &gt; <a href="{$domain}{$section}">DropDowns</a> &gt; Edit Drop Down </h3>

<a name="top"></a>



<form id="pageViewForm" method="post" action="{$domain}{$section}/updateDropDown" onsubmit="return check();">
  
  <div id="edit-form-layout">
    
    <div class="edit-form-row">
      <div class="form-section-label">Label: </div>
      <input type="text" name="drop_down" id="drop_down" value="{$dropdown_details.dropdown_label}">
    </div>
    
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="submit"  value="Save">
      </div>
    </div>

  </div>
  
  <input type="hidden" name="drop_down_id" value="{$dropdown_details.dropdown_id}" />

</form>

</div>