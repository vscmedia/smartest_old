<?php /* Smarty version 2.6.18, created on 2007-11-25 21:09:58
         compiled from /var/vsc/clients/claritycapital/smartest/System/Applications/Pages/Presentation/sitePages.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '/var/vsc/clients/claritycapital/smartest/System/Applications/Pages/Presentation/sitePages.tpl', 63, false),array('function', 'dud_link', '/var/vsc/clients/claritycapital/smartest/System/Applications/Pages/Presentation/sitePages.tpl', 97, false),)), $this); ?>
<script language="javascript" type="text/javascript">

var selectedPage = null;
var selectedPageName = null;
var lastRow;
var lastRowColor;
var treeNodes = new Array();

<?php echo '

function viewPage(){

	var pageURL = domain+\'website/renderPageFromId?page_id=\'+selectedPage;
	window.open(pageURL);

}

'; ?>

</script>

<div id="work-area">

<h3>Website Manager</h3>
<a name="top"></a>
<div class="instruction">Double click a page to edit or click once and choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="page_id" id="item_id_input" value="" />
</form>


<ul class="tree-parent-node-open" id="tree-root">
  <?php if (!function_exists('smarty_fun_menurecursion')) { function smarty_fun_menurecursion(&$smarty, $params) { $_fun_tpl_vars = $smarty->_tpl_vars; $smarty->assign($params);  ?>
    
    <?php ob_start(); ?>list_<?php if ($smarty->_tpl_vars['page']['info']['id']): ?><?php echo $smarty->_tpl_vars['page']['info']['id']; ?>
<?php else: ?>0<?php endif; ?><?php $smarty->_smarty_vars['capture']['foreach_name'] = ob_get_contents();  $smarty->assign('foreach_name', ob_get_contents());ob_end_clean(); ?>
    <?php ob_start(); ?><?php if ($smarty->_tpl_vars['page']['info']['id']): ?><?php echo $smarty->_tpl_vars['page']['info']['id']; ?>
<?php else: ?>0<?php endif; ?><?php $smarty->_smarty_vars['capture']['foreach_id'] = ob_get_contents();  $smarty->assign('foreach_id', ob_get_contents());ob_end_clean(); ?>
    <?php $_from = $smarty->_tpl_vars['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$smarty->_foreach[$smarty->_tpl_vars['foreach_name']] = array('total' => count($_from), 'iteration' => 0);
if ($smarty->_foreach[$smarty->_tpl_vars['foreach_name']]['total'] > 0):
    foreach ($_from as $smarty->_tpl_vars['page']):
        $smarty->_foreach[$smarty->_tpl_vars['foreach_name']]['iteration']++;
?>
    
    <li <?php if (($smarty->_foreach[$smarty->_tpl_vars['foreach_name']]['iteration'] == $smarty->_foreach[$smarty->_tpl_vars['foreach_name']]['total'])): ?>class="last"<?php elseif (($smarty->_foreach[$smarty->_tpl_vars['foreach_name']]['iteration'] <= 1)): ?>class="first"<?php else: ?>class="middle"<?php endif; ?>>
      
      <?php if (! empty ( $smarty->_tpl_vars['page']['child_items'] ) || ! empty ( $smarty->_tpl_vars['page']['children'] )): ?>
      <a href="javascript:toggleParentNodeFromOpenState('<?php echo $smarty->_tpl_vars['foreach_id']; ?>
_<?php echo $smarty->_foreach[$smarty->_tpl_vars['foreach_name']]['iteration']; ?>
')"><img src="<?php echo $smarty->_tpl_vars['domain']; ?>
Resources/Images/open.gif" alt="" border="0" id="toggle_<?php echo $smarty->_tpl_vars['foreach_id']; ?>
_<?php echo $smarty->_foreach[$smarty->_tpl_vars['foreach_name']]['iteration']; ?>
" /></a>
      <?php else: ?>
      <img src="<?php echo $smarty->_tpl_vars['domain']; ?>
Resources/Images/blank.gif" alt="" border="0" />
      <?php endif; ?>
      
      <a id="item_<?php echo $smarty->_tpl_vars['page']['info']['webid']; ?>
" class="option" href="javascript:nothing()" onclick="setSelectedItem('<?php echo $smarty->_tpl_vars['page']['info']['webid']; ?>
', '<?php echo ((is_array($_tmp=$smarty->_tpl_vars['page']['info']['title'])) ? $smarty->_run_mod_handler('escape', true, $_tmp, 'quotes') : smarty_modifier_escape($_tmp, 'quotes')); ?>
', '<?php if ($smarty->_tpl_vars['page']['info']['type'] == 'ITEMCLASS'): ?>meta-page<?php else: ?>static-page<?php endif; ?>');" ondblclick="window.location='<?php echo $smarty->_tpl_vars['domain']; ?>
<?php echo $smarty->_tpl_vars['section']; ?>
/openPage?page_id=<?php echo $smarty->_tpl_vars['page']['info']['webid']; ?>
&amp;site_id=<?php echo $smarty->_tpl_vars['content']['data'][0]['info']['site_id']; ?>
'">		 
        <?php if ($smarty->_tpl_vars['page']['info']['type'] == 'ITEMCLASS'): ?><img border="0" src="<?php echo $smarty->_tpl_vars['domain']; ?>
Resources/Icons/page_gear.png" /><?php else: ?><img border="0" src="<?php echo $smarty->_tpl_vars['domain']; ?>
Resources/Icons/page.png" /><?php endif; ?>
        <?php echo $smarty->_tpl_vars['page']['info']['title']; ?>
 <?php if ($smarty->_tpl_vars['page']['info']['is_published'] == 'TRUE'): ?>(published)
        <?php else: ?>(not published)<?php endif; ?>
      </a>
      <?php if (! empty ( $smarty->_tpl_vars['page']['children'] )): ?>
                <ul class="tree-parent-node-open" id="<?php echo $smarty->_tpl_vars['foreach_name']; ?>
_<?php echo $smarty->_foreach[$smarty->_tpl_vars['foreach_name']]['iteration']; ?>
">
                 
              <?php smarty_fun_menurecursion($smarty, array('list'=>$smarty->_tpl_vars['page']['children']));  ?>
      </ul>
      <?php endif; ?>
      
    </li>
    <?php endforeach; endif; unset($_from); ?>
  <?php  $smarty->_tpl_vars = $_fun_tpl_vars; }} smarty_fun_menurecursion($this, array('list'=>$this->_tpl_vars['tree']));  ?>
</ul>

</div>

<div id="actions-area">

<ul class="actions-list" id="static-page-specific-actions" style="display:none">

	<li><b>Static Page Options</b></li>
	<li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="<?php echo 'if(selectedPage){ workWithItem(\'openPage\'); }'; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_edit.png" border="0" alt=""> Edit This Page</a></li>
	<li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="workWithItem('addPage');" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_add.png" border="0" alt=""> Add a New Page</a></li>
	<li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="<?php echo 'if(selectedPage && confirm(\'Are you sure you want to delete this page?\')){workWithItem(\'deletePage\');}'; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_delete.png" border="0" alt=""> Delete Page</a></li>
	
</ul>

<ul class="actions-list" id="meta-page-specific-actions" style="display:none">

	<li><b>Meta Page Options</b></li>
	<li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="<?php echo 'if(selectedPage){ workWithItem(\'openPage\'); }'; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_edit.png" border="0" alt=""> Edit This Page</a></li>
	<li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="workWithItem('addPage');" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_add.png" border="0" alt=""> Add a New Page</a></li>
	<li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="<?php echo 'if(selectedPage && confirm(\'Are you sure you want to delete this page?\')){workWithItem(\'deletePage\');}'; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_delete.png" border="0" alt=""> Delete Page</a></li>
    	
</ul>

<ul class="actions-list" id="set-member-specific-actions" style="display:none">

	<li><b>Item Options</b></li>
	<li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="<?php echo 'if(selectedPage){ window.location=sm_domain+\'datamanager/editItem?item_id=\'+selectedPage); }'; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_edit.png" border="0" alt=""> Edit This Item</a></li>
	<li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="workWithItem('addPage');" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_add.png" border="0" alt=""> Add a New Page</a></li>
	
</ul>

<ul class="actions-list" id="non-specific-actions">
    <li><b>Site Options</b></li>
        <li class="permanent-action"><a href="<?php echo $this->_tpl_vars['domain']; ?>
websitemanager/clearPagesCache" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_delete.png" border="0" alt=""> Clear Cached Pages</a></li>
    <li class="permanent-action"><a href="<?php echo $this->_tpl_vars['domain']; ?>
desktop/closeCurrentSite" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/tick.png" border="0" alt=""> Finish Working With This Site</a></li>
</ul>

</div>