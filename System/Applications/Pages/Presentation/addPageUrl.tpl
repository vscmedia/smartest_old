<script language="javascript">{literal}

 function updatePageName(newName){
  	document.getElementById('pageName').innerHTML="Page Details: "+newName;
 }
 
 function hideNotify(){
 	// alert('one');
 	// var hnot = setTimeout("alert('two')",4000); 
	var hnot = setTimeout("document.getElementById('notify').style.display='none'",3500); 
 }
function check(){
var editForm = document.getElementById('addUrl');
if(editForm.page_url.value==''){
alert ('please enter the url');
editForm.page_url.focus();
return false;
}
else{
return true;
}
}

{/literal}</script>

<div id="work-area">

<h3 id="page-name">Add New URL</h3>

<form id="addUrl" name="addUrl" action="{$domain}{$section}/addNewPageUrl" method="post" style="margin:0px">

<input type="hidden" name="page_id" value="{$pageInfo.id}">
<input type="hidden" name="page_webid" value="{$pageInfo.webid}">

<div id="edit-form-layout">

  <div class="edit-form-row">
    <div class="form-section-label">New Url:</div>
    http://{$pageInfo.site.domain}{$domain}<input type="text" name="page_url" value="" style="width:200px" />
    <input type="checkbox" name="forward_to_default" id="forward_to_default" value="1" /><label for="forward_to_default">Forward to default URL</label>
  </div>

  <div class="edit-form-row">
    <div class="buttons-bar">
    	<input type="button" value="Cancel" onclick="cancelForm();" />
    	<input type="submit" name="action" onclick="return check();" value="Save" />
    </div>
  </div>

</div>

</form>

</div>