<?php /* Smarty version 2.6.18, created on 2007-12-06 07:57:39
         compiled from /var/www/html/System/Applications/Desktop/Presentation/startPage.tpl */ ?>
<div id="work-area">

<?php if ($this->_tpl_vars['display'] == 'desktop'): ?>

<table cellpadding="0" cellspacing="0" width="100%" border="0" style="margin-bottom:10px">
  <tr style="height:60px">
    <td style="width:60px"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Images/SiteLogos/<?php echo $this->_tpl_vars['site']['logo_image_file']; ?>
" /></td>
    <td valign="top"><h3 style="margin:0px;margin-left:8px">Welcome to the <?php echo $this->_tpl_vars['site']['name']; ?>
 administration interface</h3></td>
  </tr>
</table>

<div class="instruction">What would you like to do?</div>

<ul class="options-grid-no-scroll">
  <li><a href="<?php echo $this->_tpl_vars['domain']; ?>
smartest/pages" class="option" id="option_1"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package.png" />Modify Presentation</a></li>
  <li><a href="<?php echo $this->_tpl_vars['domain']; ?>
smartest/data" class="option" id="option_2"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package.png" />Manage Data</a></li>
  <li><a href="<?php echo $this->_tpl_vars['domain']; ?>
smartest/assets" class="option" id="option_3"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package.png" />Browse and Upload Multimedia Assets</a></li>
  <li><a href="<?php echo $this->_tpl_vars['domain']; ?>
smartest/todo" class="option" id="option_4"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package.png" />Go to Your Todo List</a></li>
  <li><a href="<?php echo $this->_tpl_vars['domain']; ?>
smartest/templates" class="option" id="option_5"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package.png" />Administer Templates</a></li>
  <li><a href="<?php echo $this->_tpl_vars['domain']; ?>
smartest/users" class="option" id="option_6"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package.png" />Administer User Accounts</a></li>
  <li><a href="<?php echo $this->_tpl_vars['domain']; ?>
smartest/settings" class="option" id="option_7"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package.png" />Modify Settings</a></li>
  </ul>

<?php elseif ($this->_tpl_vars['display'] == 'sites'): ?>

<?php if ($this->_tpl_vars['num_sites'] > 0): ?>

<h3>Welcome to Smartest!</h3>

<div class="instruction">Select a site to work with:</div>

<ul class="basic-list">
<?php $_from = $this->_tpl_vars['sites']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['site']):
?>
<?php if (isset ( $this->_tpl_vars['site']['name'] )): ?>
<li><a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/openSite?site_id=<?php echo $this->_tpl_vars['site']['id']; ?>
"><?php echo $this->_tpl_vars['site']['name']; ?>
 (<?php echo $this->_tpl_vars['site']['domain']; ?>
)</a></li>
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
</ul>

<?php else: ?>

<div class="instruction" style="margin-bottom:10px">You haven't yet been granted access to any sites.</div>

<?php endif; ?>

<?php if ($this->_tpl_vars['show_create_button']): ?><a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/createSite">Create a new site</a><?php endif; ?>

<?php endif; ?>

</div>