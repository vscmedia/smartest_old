<?php /* Smarty version 2.6.18, created on 2008-01-17 12:22:02
         compiled from /var/www/html/System/Applications/MetaData/Presentation/listFields.tpl */ ?>
<div id="work-area">

<h3>Page Fields</h3>

<div class="text" style="margin-bottom:10px">Double click a field to see how and where it's defined.</div>

<form id="pageViewForm" method="get" action="">
<input type="hidden" name="field_id" id="item_id_input" value="" />
</form>

<ul class="options-grid" id="tree-root">
       <?php $_from = $this->_tpl_vars['fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['element']):
?>
    <li >
       <a id="item_<?php echo $this->_tpl_vars['element']['pageproperty_id']; ?>
" class="option" href="#" onclick="setSelectedItem('<?php echo $this->_tpl_vars['element']['pageproperty_id']; ?>
','<?php echo $this->_tpl_vars['element']['pageproperty_name']; ?>
');" ondblclick="workWithItem('viewPageFieldDefinitions')">		 
        <img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_code.png" />
        <?php echo $this->_tpl_vars['element']['pageproperty_name']; ?>

      </a>
     
    </li>
    <?php endforeach; endif; unset($_from); ?>
</ul>

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions" style="display:none">
	<li><b>Selected Field</b></li>
	<li class="permanent-action"><a href="#" onclick="workWithItem('viewPageFieldDefinitions');" class="right-nav-link"> <img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_code.png" border="0" alt="">&nbsp;View definitions</a></li>
	<li class="permanent-action"><a href="#" onclick="workWithItem('clearFieldOnAllPages');" class="right-nav-link"> <img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_code.png" border="0" alt="">&nbsp;Clear on all pages</a></li>
	<li class="permanent-action"><a href="#" onclick="<?php echo 'if(selectedPage && confirm(\'Are you sure you want to delete this property?\')){workWithItem(\'deletePageProperty\');}'; ?>
" class="right-nav-link"> <img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/cross.png" border="0" alt="">&nbsp;Delete</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Fields Options</b></li>
  <li class="permanent-action"><a href="#" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/addPageProperty?site_id=<?php echo $this->_tpl_vars['content']['site_id']; ?>
'"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_add.png" border="0" alt="">&nbsp;Add A New Field</a></li>
</ul>

</div>