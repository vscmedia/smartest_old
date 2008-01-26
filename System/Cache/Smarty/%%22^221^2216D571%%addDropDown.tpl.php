<?php /* Smarty version 2.6.18, created on 2007-12-02 12:52:35
         compiled from /var/www/html/System/Applications/Dropdowns/Presentation/addDropDown.tpl */ ?>
<script language="javascript" type="text/javascript">

<?php echo '
  function check(){
    var editForm = document.getElementById(\'pageViewForm\');
    if(editForm.drop_down.value==\'\'){
      alert (\'please enter the dropdown Label\');
      editForm.drop_down.focus();
      return false;
    }else{
      return true;
    }
  }
  
'; ?>


</script>

<div id="work-area">

<h3><a href="<?php echo $this->_tpl_vars['domain']; ?>
datamanager">Data Manager</a> &gt; <a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
">DropDowns</a> &gt; Add Drop Down</h3>
<a name="top"></a>

<div class="instruction">Your data is collected into functionally distinct types called Drop Downs. Please choose one to continue.</div>

<table border="0" cellspacing="0" cellpadding="0" style="width:850px">
  <tr>
    <td valign="top" style="width:550px">
			<table  border="0" style="width:550px">
<form id="pageViewForm" method="post" action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/insertDropDown"  onsubmit="return check();">
<!--<input type="hidden" name="set_id" value="<?php echo $this->_tpl_vars['content']['newset']; ?>
" />-->
				<tr>
					<td width="250">Dropdown Label: <input type="text" name="dropdown_label" id="drop_down" value=""></td>				
				
					<td width="250" ><input type="submit" value="Add"></td>	
									
				</tr>		
				
</form>
			</table>

		</td>
		<td valign="top" style="width:250px">		
		</td>		
		</tr>
</table>

</div>