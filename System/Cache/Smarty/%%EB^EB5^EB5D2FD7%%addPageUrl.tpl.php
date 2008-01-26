<?php /* Smarty version 2.6.18, created on 2007-12-01 19:45:07
         compiled from /var/www/html/System/Applications/Pages/Presentation/addPageUrl.tpl */ ?>
<script language="javascript"><?php echo '

 function updatePageName(newName){
  	document.getElementById(\'pageName\').innerHTML="Page Details: "+newName;
 }
 
 function hideNotify(){
 	// alert(\'one\');
 	// var hnot = setTimeout("alert(\'two\')",4000); 
	var hnot = setTimeout("document.getElementById(\'notify\').style.display=\'none\'",3500); 
 }
function check(){
var editForm = document.getElementById(\'addUrl\');
if(editForm.page_url.value==\'\'){
alert (\'please enter the url\');
editForm.page_url.focus();
return false;
}
else{
return true;
}
}

'; ?>
</script>

<div id="work-area">

<h3 id="page-name">Add New URL</h3>

<form id="addUrl" name="addUrl" action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/addNewPageUrl" method="post" style="margin:0px">

<input type="hidden" name="page_id" value="<?php echo $this->_tpl_vars['pageInfo']['id']; ?>
">
<input type="hidden" name="page_webid" value="<?php echo $this->_tpl_vars['pageInfo']['webid']; ?>
">

<table id="edit-form-layout" border="0" cellpadding="0" cellspacing="2">

  <tr>
    <td class="text" style="width:100px" >URL: </td>
      <td><div style="display:inline" id="siteDomainField">http://<?php echo $this->_tpl_vars['pageInfo']['site']['domain']; ?>
/</div><input type="text" name="page_url"></td>
  </tr>

</table>
  
  <div class="edit-form-row">
    <div class="buttons-bar">
    	<input type="button" value="Cancel" onclick="cancelForm();" />
    	<input type="submit" name="action" onclick="return check();" value="Add" />
    </div>
  </div>

</form>

</div>