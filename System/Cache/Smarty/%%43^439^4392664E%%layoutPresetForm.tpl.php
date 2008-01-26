<?php /* Smarty version 2.6.18, created on 2007-12-02 12:35:59
         compiled from /var/www/html/System/Applications/Pages/Presentation/layoutPresetForm.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'lower', '/var/www/html/System/Applications/Pages/Presentation/layoutPresetForm.tpl', 59, false),)), $this); ?>
<script language="javascript"><?php echo '

function check(){
var editForm = document.getElementById(\'createLayoutPreset\');
var element = document.getElementsByName(\'asset[]\');
var flag = \'false\';
for(i=0;i<element.length;i++){
    if(element[i].checked){
        flag= \'true\';
    }
  }
if(editForm.layoutpresetname.value==\'\'){
alert (\'please enter the presetname\');
alert (element);
editForm.layoutpresetname.focus();
return false;
}
elseif(flag==false){
  alert("Please check at least one box!");
  return false;
}
else 
return true;

}

'; ?>
</script>

<div id="work-area">

<h3>Create a Page Preset</h3>

<a name="top"></a>

<div class="instruction">Click the checkbox corresponding to each asset to add it to preset</div>

<form id="createLayoutPreset" action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/createLayoutPreset" method="post" style="margin:0px">
  
<input type="hidden" name="page_id" value="<?php echo $this->_tpl_vars['page']['id']; ?>
" />

<div class="instruction">Enter a name for this preset: <input type="text" name="preset_name" value="Untitled Preset" /></div>

<div style="margin-bottom:10px"><input type="checkbox" name="preset_shared" value="true" id="preset_shared" /><label for="preset_shared">Make this preset available to all sites</label></div>

<table width="100%" cellpadding="0" cellspacing="2" style="width:100%" border="0">

<?php if (! empty ( $this->_tpl_vars['elements'] )): ?>

<?php $_from = $this->_tpl_vars['elements']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['element']):
?>
  
<?php if ($this->_tpl_vars['element']['info']['exists'] == 'true'): ?>
  <tr>
    <td style="width:20px">
      <input type="checkbox" name="<?php echo $this->_tpl_vars['element']['info']['type']; ?>
[]" value="<?php echo $this->_tpl_vars['element']['info']['assetclass_id']; ?>
" id="element_<?php echo $this->_tpl_vars['key']; ?>
" <?php if (in_array ( $this->_tpl_vars['element']['info']['defined'] , array ( 'PUBLISHED' , 'DRAFT' ) )): ?>checked="checked"<?php else: ?>disabled="disabled"<?php endif; ?> />
    </td>
    
    <td>
<?php if ($this->_tpl_vars['element']['info']['defined'] == 'PUBLISHED'): ?>
		  <img border="0" style="width:16px;height:16px;" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/published_<?php echo ((is_array($_tmp=$this->_tpl_vars['element']['info']['type'])) ? $this->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)); ?>
.gif" />
<?php elseif ($this->_tpl_vars['element']['info']['defined'] == 'DRAFT'): ?>
      <img border="0" style="width:16px;height:16px;" title="This <?php echo $this->_tpl_vars['element']['info']['type']; ?>
 is only defined in the draft version of the page" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/draftonly_<?php echo ((is_array($_tmp=$this->_tpl_vars['element']['info']['type'])) ? $this->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)); ?>
.gif" />
<?php else: ?>
		  <img border="0" style="width:16px;height:16px;" title="This <?php echo $this->_tpl_vars['element']['info']['type']; ?>
 is only defined in the draft version of the page" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/undefined_<?php echo ((is_array($_tmp=$this->_tpl_vars['element']['info']['type'])) ? $this->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)); ?>
.gif" />
<?php endif; ?>
		  <label for="element_<?php echo $this->_tpl_vars['key']; ?>
"><strong><?php echo $this->_tpl_vars['element']['info']['assetclass_name']; ?>
</strong></label>
	  </td>
	</tr>
<?php endif; ?>

<?php endforeach; endif; unset($_from); ?>

<?php endif; ?>

</table>

<div class="edit-form-row">
  <div class="buttons-bar">
    <input type="button" value="Cancel" onclick="cancelForm();" />
    <input type="submit" name="action" onclick= "return check();" value="Save" />
  </div>
</div>

</form>

</div>