<?php /* Smarty version 2.6.18, created on 2007-12-04 12:29:14
         compiled from /var/www/html/System/Applications/Items/Presentation/getItemClassProperties.tpl */ ?>
<script language="javascript" type="text/javascript">

var selectedPage = null;
var selectedPageName = null;
var lastRow;
var lastRowColor;
var treeNodes = new Array();
var domain = '<?php echo $this->_tpl_vars['domain']; ?>
';

<?php echo '
function setSelectedItem(item_id,pageName, rowColor){
	
	var row=\'item_\'+item_id;
	var editForm = document.getElementById(\'pageViewForm\');
	
	rowColor=\'#\'+rowColor;
	selectedPage = item_id;
	selectedPageName = pageName;
	
	document.getElementById(\'item-specific-actions\').style.display = \'block\';
	
	if(lastRow){
		// document.getElementById(lastRow).style.backgroundColor=lastRowColor;
	}
	
	// document.getElementById(row).style.backgroundColor=\'#99F\';
	
	lastRow = row;
	lastRowColor = rowColor;
	editForm.itemproperty_id.value = item_id;
}
function workWithItem(pageAction){
	
	var editForm = document.getElementById(\'pageViewForm\');
	
	if(editForm){
		
		editForm.action=pageAction;
		editForm.submit();
		
	}
}

function viewPage(){

	var pageURL = \'{$domain}{$section}/editItemProperty?property_id=\'+selectedPage;
	window.open(pageURL);
	
}

'; ?>


</script>
<div id="work-area">

<h3><a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
">Data Manager</a> &gt; <a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
">Model Structure</a>  &gt;  <?php echo $this->_tpl_vars['itemclass']['name']; ?>
</h3>
<a name="top"></a>
<div class="text" style="margin-bottom:10px">Click a property once and choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
<input type="hidden" name="class_id" value="<?php echo $this->_tpl_vars['itemclass']['id']; ?>
" />
<input type="hidden" name="itemproperty_id" value="" />
</form>

<ul class="tree-parent-node-open" id="tree-root">
  <?php if (!function_exists('smarty_fun_menurecursion')) { function smarty_fun_menurecursion(&$smarty, $params) { $_fun_tpl_vars = $smarty->_tpl_vars; $smarty->assign($params);  ?>
       <?php $_from = $smarty->_tpl_vars['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $smarty->_tpl_vars['element']):
?>
    <li >
       <a id="item_<?php echo $smarty->_tpl_vars['element']['id']; ?>
" class="option" href="javascript:nothing()" onclick="setSelectedItem('<?php echo $smarty->_tpl_vars['element']['id']; ?>
','<?php echo $smarty->_tpl_vars['element']['itemproperty_varname']; ?>
', 'fff');" ondblclick="window.location='<?php echo $smarty->_tpl_vars['domain']; ?>
<?php echo $smarty->_tpl_vars['section']; ?>
/editItemProperty?class_id=<?php echo $smarty->_tpl_vars['itemclass']['itemclass_id']; ?>
&amp;itemproperty_id=<?php echo $smarty->_tpl_vars['element']['itemproperty_id']; ?>
'">		 
        <img border="0" src="<?php echo $smarty->_tpl_vars['domain']; ?>
Resources/Icons/page_code.png" />
        <?php echo $smarty->_tpl_vars['element']['varname']; ?>

      </a>
     
    </li>
    <?php endforeach; endif; unset($_from); ?>
  <?php  $smarty->_tpl_vars = $_fun_tpl_vars; }} smarty_fun_menurecursion($this, array('list'=>$this->_tpl_vars['definition']));  ?>
</ul>

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions">
	<li class="permanent-action"><b>Node Options</b></li>
	
	<li class="permanent-action"><a href="#" onclick="<?php echo 'if(selectedPage){ workWithItem(\'editItemProperty\'); }'; ?>
" class="right-nav-link"> <img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/layout_edit.png" border="0" alt="">Edit</a></li>
	<li class="permanent-action"><a href="#" onclick="<?php echo 'if(selectedPage && confirm(\'Are you sure you want to delete this page?\')){workWithItem(\'deleteProperty\');}'; ?>
" class="right-nav-link"> <img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_edit.png" border="0" alt="">Delete</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
    <li class="disabled-action"><i>Please Select a Node</i></li>
<li class="permanent-action"><a href="#" onclick="<?php echo 'workWithItem(\'addPropertyToClass\');'; ?>
" class="right-nav-link"> <img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_add.png" border="0" alt="">Add Property</a></li>
</ul>

</div>