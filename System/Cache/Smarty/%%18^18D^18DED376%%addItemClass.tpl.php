<?php /* Smarty version 2.6.18, created on 2007-12-04 12:16:08
         compiled from /var/www/html/System/Applications/Items/Presentation/addItemClass.tpl */ ?>
<script src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Javascript/livesearch.js" type="text/javascript"></script> 

<script language="javascript">

<?php echo '
var setPlural = true;

function suggestPluralName(){
	// alert(document.getElementById(\'plural\').value);
	
	
	if(setPlural == true){
		document.getElementById(\'plural\').value = document.getElementById(\'livesearch\').value+"s";
	}
}

function turnOffAutoPlural(){
	setPlural = false;
}

'; ?>

</script>

<div id="work-area">

<h3><a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
">Data Manager</a> &gt; Build a New Model</h3>

<div class="instruction">Please enter the name of your new model.</div>

<form name="searchform" onsubmit="return liveSearchSubmit()" method="post" action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/insertItemClass">
<input type="hidden" name="stage" value="2" />
    
<div class="edit-form-row">
  <div class="form-section-label">Model Name:</div>
  <input id="livesearch" onkeyup="suggestPluralName()" type="text" name="itemclass_name" style="width:200px" />
  <div class="form-section-label">Model Plural Name:</div>
  <input id="plural" onkeyup="turnOffAutoPlural()" type="text" name="itemclass_plural_name" style="width:200px" />     
</div>
    

    
<div class="edit-form-row">
    <div class="buttons-bar"><input type="submit" value="Next &gt;&gt;" /></div>
</div>

</form>

</div>