<?php /* Smarty version 2.6.18, created on 2007-11-25 20:40:09
         compiled from /var/vsc/clients/claritycapital/smartest/System/Applications/Sets/Presentation/editSet.dynamic.tpl */ ?>
<div id="work-area">

<h3><a href="<?php echo $this->_tpl_vars['domain']; ?>
datamanager">Data Manager</a> &gt; <a href="<?php echo $this->_tpl_vars['domain']; ?>
smartest/sets">Data Sets</a> &gt; Conditions</h3>
<a name="top"></a>

<div class="instruction">Create conditions to filter your data into a pre-saved set that can be used anywhere.</div>

  <form id="pageViewForm" method="post" action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/updateDynamicSet">
    
    <input type="hidden" name="set_id" value="<?php echo $this->_tpl_vars['set']['id']; ?>
" />
    
    <table border="0" style="width:100%">
      
      <tr>
        <td style="width:100px">Set Label:</td>
        <td><input type="text"  name="set_name" value="<?php echo $this->_tpl_vars['set']['label']; ?>
" /> (<?php echo $this->_tpl_vars['set']['name']; ?>
)</td>
      </tr>
      
      <tr>
        <td style="width:100px">Sort By Property:</td>
        <td>
          <select name="set_sort_field">
				    <?php $_from = $this->_tpl_vars['properties']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['property']):
?>
				    <option value="<?php echo $this->_tpl_vars['property']['id']; ?>
" <?php if ($this->_tpl_vars['property']['id'] == $this->_tpl_vars['set']['sort_field']): ?> selected<?php endif; ?>><?php echo $this->_tpl_vars['property']['name']; ?>
</option>
            <?php endforeach; endif; unset($_from); ?>
			    </select>
			  </td>
      </tr>
      
      <tr>
        <td style="width:100px">Sort Direction:</td>
        <td>
          <select name="set_sort_direction">
				    <option value="ASC" <?php if ($this->_tpl_vars['set']['sort_direction'] == 'ASC'): ?> selected<?php endif; ?>>Ascending</option>
				    <option value="DESC" <?php if ($this->_tpl_vars['set']['sort_direction'] == 'DESC'): ?> selected<?php endif; ?>>Descending</option>
			    </select>
			  </td>
      </tr>

  <?php if (empty ( $this->_tpl_vars['conditions'] )): ?>			
    <div>There are no conditions for this data set yet</div>
  <?php endif; ?>


    <tr>
      <td colspan="2">
  		  <ul class="options-list" id="rules_list">
  		    <li><h4>Conditions:</h4></li>
  				<?php $_from = $this->_tpl_vars['conditions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['rule']):
?>
  <li id="item_<?php echo $this->_tpl_vars['rule']['itemproperty_id']; ?>
">
					  
  					  <select name="conditions[<?php echo $this->_tpl_vars['rule']['id']; ?>
][property_id]">
  						  <option value="_SMARTEST_ITEM_NAME" <?php if ($this->_tpl_vars['rule']['itemproperty_id'] == '_SMARTEST_ITEM_NAME'): ?> selected<?php endif; ?>><?php echo $this->_tpl_vars['model']['name']; ?>
 Name</option>
  						  <option value="_SMARTEST_ITEM_ID" <?php if ($this->_tpl_vars['rule']['itemproperty_id'] == '_SMARTEST_ITEM_ID'): ?> selected<?php endif; ?>><?php echo $this->_tpl_vars['model']['name']; ?>
 ID</option>
  					    <?php $_from = $this->_tpl_vars['properties']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['property']):
?>
  						  <option value="<?php echo $this->_tpl_vars['property']['id']; ?>
" <?php if ($this->_tpl_vars['property']['id'] == $this->_tpl_vars['rule']['itemproperty_id']): ?> selected<?php endif; ?>><?php echo $this->_tpl_vars['property']['name']; ?>
</option>
                <?php endforeach; endif; unset($_from); ?>
  					  </select>
					
  					  <select name="conditions[<?php echo $this->_tpl_vars['rule']['id']; ?>
][operator]">
  						  <option value="0" <?php if ($this->_tpl_vars['rule']['operator'] == '0'): ?> selected="selected" <?php endif; ?>>Equals</option>
  						  <option value="1" <?php if ($this->_tpl_vars['rule']['operator'] == '1'): ?> selected="selected" <?php endif; ?>>Does Not Equal</option>
  						  <option value="2" <?php if ($this->_tpl_vars['rule']['operator'] == '2'): ?> selected="selected" <?php endif; ?>>Contains</option>
  						  <option value="3" <?php if ($this->_tpl_vars['rule']['operator'] == '3'): ?> selected="selected" <?php endif; ?>>Does Not Contain</option>
  						  <option value="4" <?php if ($this->_tpl_vars['rule']['operator'] == '4'): ?> selected="selected" <?php endif; ?>>Starts With</option>
  						  <option value="5" <?php if ($this->_tpl_vars['rule']['operator'] == '5'): ?> selected="selected" <?php endif; ?>>Ends With</option>
  						  <option value="6" <?php if ($this->_tpl_vars['rule']['operator'] == '6'): ?> selected="selected" <?php endif; ?>>Greater Than</option>
  						  <option value="7" <?php if ($this->_tpl_vars['rule']['operator'] == '7'): ?> selected="selected" <?php endif; ?>>Less Than</option>
  					  </select>
						
  					  <input type="text" value="<?php echo $this->_tpl_vars['rule']['value']; ?>
" name="conditions[<?php echo $this->_tpl_vars['rule']['id']; ?>
][value]" />
					  
  					    					  <input type="button" value="-" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/removeConditionFromSet?condition_id=<?php echo $this->_tpl_vars['rule']['id']; ?>
'" />

  </li>
  				<?php endforeach; endif; unset($_from); ?>
  <li>
					    
					    <div style="margin-top:15px">Add a new Condition? <input type="checkbox" name="add_new_condition" value="1" /> Yes</div>
					    
  					  <div>
  					    
  					    <select name="new_condition_property_id">
    					    <option value="_SMARTEST_ITEM_NAME"><?php echo $this->_tpl_vars['model']['name']; ?>
 Name</option>
    					    <option value="_SMARTEST_ITEM_ID"><?php echo $this->_tpl_vars['model']['name']; ?>
 ID</option>
                  <?php $_from = $this->_tpl_vars['properties']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['property']):
?>
                  <option value="<?php echo $this->_tpl_vars['property']['id']; ?>
"><?php echo $this->_tpl_vars['property']['name']; ?>
</option>
                  <?php endforeach; endif; unset($_from); ?>
    					  </select>
					
    					  <select name="new_condition_operator">
    					    <option value="0">Equals</option>
    					    <option value="1">Does Not Equal</option>
    					    <option value="2">Contains</option>
    					    <option value="3">Does Not Contain</option>
    					    <option value="4">Starts With</option>
    					    <option value="5">Ends With</option>
    					    <option value="6">Greater Than</option>
    					    <option value="7">Less Than</option>
    				    </select>
  				    
  						  <input type="text" name="new_condition_value" />
  				    
  				    </div>
					
  				    					
  </li>
  			  </ul>
  			</td>
  		</tr>
    </table>
  
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="button" value="cancel" />
        <input type="submit" value="Save Changes" />
      </div>
    </div>
  
  </form>

</div>

<div id="actions-area">
		
		<ul class="actions-list">
		  <li><b>Options</b></li>
			<li class="permanent-action"><a href="#" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/previewSet?set_id=<?php echo $this->_tpl_vars['set']['id']; ?>
'"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package_add.png"> Preview Set</a></li>
			<li class="permanent-action"><a href="#" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/'">         <img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package_add.png"> Browse Data Sets</a></li>
			<li class="permanent-action"><a href="#" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
datamanager/'"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package_add.png"> Data Manager</a></li>
					</ul>
		
</div>

