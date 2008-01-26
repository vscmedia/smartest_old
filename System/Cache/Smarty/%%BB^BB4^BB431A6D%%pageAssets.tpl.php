<?php /* Smarty version 2.6.18, created on 2007-12-04 22:53:44
         compiled from /var/www/html/System/Applications/Pages/Presentation/pageAssets.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'load_interface', '/var/www/html/System/Applications/Pages/Presentation/pageAssets.tpl', 33, false),array('function', 'dud_link', '/var/www/html/System/Applications/Pages/Presentation/pageAssets.tpl', 79, false),)), $this); ?>
<script language="javascript" type="text/javascript">

var selectedPage = null;
var selectedPageName = null;
var lastRow;
var lastRowColor;
var treeNodes = new Array();
var pageWebId = '<?php echo $this->_tpl_vars['page']['webid']; ?>
';

<?php echo '

function viewDraftPage(){

	var pageURL = sm_domain+\'website/renderEditableDraftPage?page_id=\'+pageWebId;
	window.location=pageURL;

}

function viewLivePage(){

	var pageURL = sm_domain+\'website/renderPageFromId?page_id=\'+pageWebId;
	window.open(pageURL);

}

'; ?>

</script>

<div id="work-area">

<?php if ($this->_tpl_vars['allow_edit']): ?>

<?php echo smarty_function_load_interface(array('file' => "editPage.tabs.tpl"), $this);?>


<?php echo smarty_function_load_interface(array('file' => $this->_tpl_vars['sub_template']), $this);?>


<?php else: ?>

<h3>Page Structure</h3>

<?php endif; ?>

</div>

<div id="actions-area">

<!--Navigation Bar-->

<ul class="invisible-actions-list" id="placeholder-specific-actions" style="display:none">
  <li><b>Placeholder Options</b></li>
  <li class="permanent-action"><a href="#" onclick="workWithItem('definePlaceholder');" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/layout_edit.png" border="0" alt=""> Define Placeholder</a></li>
  <li class="permanent-action"><a href="#" onclick="workWithItem('undefinePlaceholder');" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/layout_edit.png" border="0" alt=""> Clear Placeholder</a></li>
</ul>

<ul class="invisible-actions-list" id="container-specific-actions" style="display:none">
  <li><b>Container Options</b></li>
  <li class="permanent-action"><a href="#" onclick="workWithItem('defineContainer');" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/layout_edit.png" border="0" alt=""> Define Container</a></li>
</ul>

<ul class="invisible-actions-list" id="list-specific-actions" style="display:none">
  <li><b>List Options</b></li>
  <li class="permanent-action"><a href="#" onclick="workWithItem('defineList');" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/layout_edit.png" border="0" alt=""> Define List Parameters</a></li>
</ul>

<ul class="invisible-actions-list" id="field-specific-actions" style="display:none">
  <li><b>Field Options</b></li>
  <li class="permanent-action"><a href="#" onclick="workWithItem('editField');" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/layout_edit.png" border="0" alt=""> Define This Field</a></li>
  <li class="permanent-action">
    <a href="#" onclick="if(confirm('Are you sure you want to set the draft value as live?')) workWithItem('setLiveProperty')" class="right-nav-link">
      <img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_lightning.png" border="0" alt=""> Publish this field</a></li>
  <li class="permanent-action">
    <a href="#" onclick="if(confirm('Are you sure you want to undefine this field?')) workWithItem('undefinePageProperty')" class="right-nav-link">
      <img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_delete.png" border="0" alt=""> Undefine this field</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Page Options</b></li>
  <?php if ($this->_tpl_vars['template']['filename']): ?><li class="permanent-action"><a href="<?php echo $this->_tpl_vars['domain']; ?>
templates/editTemplate?type=SM_PAGE_MASTER_TEMPLATE&amp;template_name=<?php echo $this->_tpl_vars['templateMenuField']; ?>
" value="Edit"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_edit.png" border="0" alt="" /> Edit Page Template</a></li><?php endif; ?>
  <?php if ($this->_tpl_vars['version'] == 'draft'): ?><li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/publishPageConfirm?page_id=<?php echo $this->_tpl_vars['page']['webid']; ?>
'" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_lightning.png" border="0" alt=""> Publish this page</a></li><?php endif; ?>
      <li class="permanent-action"><a href="#" onclick="viewLivePage();" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_go.png" border="0" alt=""> Go to this page</a></li>

  <li class="permanent-action"><a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/layoutPresetForm?page_id=<?php echo $this->_tpl_vars['page']['webid']; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_edit.png" border="0" alt=""> Create preset from this page</a></li>

  <?php if ($this->_tpl_vars['draftAsset']['asset_id'] && $this->_tpl_vars['draftAsset']['asset_id'] != $this->_tpl_vars['liveAsset']['asset_id']): ?><li class="permanent-action"><a href="#" onclick="<?php echo 'if(confirm(\'Are you sure you want to publish your changes right now?\')){workWithItem(\'setLiveAsset\');}'; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_delete.png" border="0" alt=""> Publish This Asset Class</a><?php endif; ?>
  <li class="permanent-action"><a href="<?php echo $this->_tpl_vars['domain']; ?>
smartest/assets/types" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_add.png" border="0" alt=""> Browse Assets Library</a></li>
  <li class="permanent-action"><a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/closeCurrentPage" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/tick.png" border="0" alt=""> Finish working with this page</a></li>
  <?php if ($this->_tpl_vars['allow_release']): ?><li class="permanent-action"><a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/releasePage?page_id=<?php echo $this->_tpl_vars['page']['webid']; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/tick.png" border="0" alt=""> Release this page</a></li><?php endif; ?>
</ul>

</div>