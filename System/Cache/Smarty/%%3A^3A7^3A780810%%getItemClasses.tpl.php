<?php /* Smarty version 2.6.18, created on 2007-11-25 20:53:12
         compiled from /var/vsc/clients/claritycapital/smartest/System/Applications/Items/Presentation/getItemClasses.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'dud_link', '/var/vsc/clients/claritycapital/smartest/System/Applications/Items/Presentation/getItemClasses.tpl', 43, false),)), $this); ?>
<script language="javascript" type="text/javascript">

var selectedPage = null;
var selectedPageName = null;
var lastRow;
var lastRowColor;
var domain = '<?php echo $this->_tpl_vars['domain']; ?>
';
var section = '<?php echo $this->_tpl_vars['section']; ?>
';

// alert(document.getElementById('pageViewForm'));
<?php echo '

function viewmodel(){
	var editForm = document.getElementById(\'pageViewForm\');
	var schema = editForm.schema_id.value;
	var pageURL = domain+\'modeltemplates/schemaDefinition?schema_id=\'+schema;
	window.location=pageURL;
}

function setView(viewName, list_id){
	if(viewName == "grid"){
		document.getElementById(list_id).className="options-grid";
	}else if(viewName == "list"){
		document.getElementById(list_id).className="options-list";
	}
}

'; ?>

</script>

<div id="work-area">

<h3><a href="<?php echo $this->_tpl_vars['domain']; ?>
smartest/data">Data Manager</a> &gt; Models</h3>
<a name="top"></a>

<div class="instruction">Your data is collected into functionally distinct types called models. Please choose one to continue.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="class_id" id="item_id_input" value="" />
</form>

<div id="options-view-chooser">
View: <a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="setView('list', 'options_grid')">List</a> /
<a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="setView('grid', 'options_grid')">Icon</a>
</div>

<ul class="options-grid" id="options_grid">
<?php $_from = $this->_tpl_vars['models']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['itemClass']):
?>
  <li ondblclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/getItemClassMembers?class_id=<?php echo $this->_tpl_vars['itemClass']['id']; ?>
'">
    <a id="item_<?php echo $this->_tpl_vars['itemClass']['id']; ?>
" class="option" href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="setSelectedItem('<?php echo $this->_tpl_vars['itemClass']['id']; ?>
');">
      <img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/model.png">
      <?php echo $this->_tpl_vars['itemClass']['plural_name']; ?>
</a>
	<?php if ($this->_tpl_vars['itemClass']['number_properties'] < 1): ?><?php endif; ?></li>
<?php endforeach; endif; unset($_from); ?>
</ul>

</div>


<div id="actions-area">

<ul class="actions-list" id="item-specific-actions" style="display:none">
  <li><b>Selected Model</b></li>
  <li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="workWithItem('getItemClassMembers');"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_code.png"> Browse items</a></li>
  <li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="workWithItem('addItem');"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package_add.png"> Add a new member item</a></li>
  <li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="workWithItem('addSet');"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package_add.png"> Build a new set</a></li>
  <li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="workWithItem('editModel');"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_edit.png"> Edit Model</a></li>
  <li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="workWithItem('getItemClassProperties');"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_edit.png"> Edit Model Properties</a></li>
  <li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="<?php echo 'if(selectedPage && confirm(\'Are you sure you want to delete this page?\')){workWithItem(\'deleteItemClass\');}'; ?>
"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package_delete.png"> Delete This Model</a></li>
    </ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Model Options</b></li>
  <li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/addItemClass'"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package_add.png"> Build a New Model</a></li>
  <li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
smartest/sets'"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package_add.png"> View Sets From Your Data</a></li>
  <li class="permanent-action"><a href="<?php echo $this->_tpl_vars['domain']; ?>
sets/getDataExports"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package_add.png"> View XML Feeds</a></li>
  <li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
smartest/schemas'"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package_add.png"> View XML Schemas</a></li>

</ul>

</div>




