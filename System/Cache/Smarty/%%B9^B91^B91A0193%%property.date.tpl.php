<?php /* Smarty version 2.6.18, created on 2007-11-28 06:28:57
         compiled from /var/www/html/System/Applications/Items/Presentation/Fields/property.date.tpl */ ?>
<div class="form-section-label"><?php if ($this->_tpl_vars['property']['required'] == 'TRUE'): ?><strong><?php endif; ?><?php echo $this->_tpl_vars['property']['name']; ?>
 (<?php echo $this->_tpl_vars['property']['varname']; ?>
)<?php if ($this->_tpl_vars['property']['required'] == 'TRUE'): ?></strong> *<?php endif; ?></div>
<select name="item[<?php echo $this->_tpl_vars['property']['id']; ?>
][M]">
	<option value="00"<?php if ($this->_tpl_vars['value']['M'] == '00'): ?> selected="selected"<?php endif; ?>>-- Month --</option>
	<option value="01"<?php if ($this->_tpl_vars['value']['M'] == '01'): ?> selected="selected"<?php endif; ?>>January</option>
	<option value="02"<?php if ($this->_tpl_vars['value']['M'] == '02'): ?> selected="selected"<?php endif; ?>>February</option>
	<option value="03"<?php if ($this->_tpl_vars['value']['M'] == '03'): ?> selected="selected"<?php endif; ?>>March</option>
	<option value="04"<?php if ($this->_tpl_vars['value']['M'] == '04'): ?> selected="selected"<?php endif; ?>>April</option>
	<option value="05"<?php if ($this->_tpl_vars['value']['M'] == '05'): ?> selected="selected"<?php endif; ?>>May</option>
	<option value="06"<?php if ($this->_tpl_vars['value']['M'] == '06'): ?> selected="selected"<?php endif; ?>>June</option>
	<option value="07"<?php if ($this->_tpl_vars['value']['M'] == '07'): ?> selected="selected"<?php endif; ?>>July</option>
	<option value="08"<?php if ($this->_tpl_vars['value']['M'] == '08'): ?> selected="selected"<?php endif; ?>>August</option>
	<option value="09"<?php if ($this->_tpl_vars['value']['M'] == '09'): ?> selected="selected"<?php endif; ?>>September</option>
	<option value="10"<?php if ($this->_tpl_vars['value']['M'] == '10'): ?> selected="selected"<?php endif; ?>>October</option>
	<option value="11"<?php if ($this->_tpl_vars['value']['M'] == '11'): ?> selected="selected"<?php endif; ?>>November</option>
	<option value="12"<?php if ($this->_tpl_vars['value']['M'] == '12'): ?> selected="selected"<?php endif; ?>>December</option>
</select><br />
<select name="item[<?php echo $this->_tpl_vars['property']['id']; ?>
][D]">
	<option value="00"<?php if ($this->_tpl_vars['value']['D'] == '00'): ?> selected="selected"<?php endif; ?>>-- Day --</option>
	<option value="01"<?php if ($this->_tpl_vars['value']['D'] == '01'): ?> selected="selected"<?php endif; ?>>1st</option>
	<option value="02"<?php if ($this->_tpl_vars['value']['D'] == '02'): ?> selected="selected"<?php endif; ?>>2nd</option>
	<option value="03"<?php if ($this->_tpl_vars['value']['D'] == '03'): ?> selected="selected"<?php endif; ?>>3rd</option>
	<option value="04"<?php if ($this->_tpl_vars['value']['D'] == '04'): ?> selected="selected"<?php endif; ?>>4th</option>
	<option value="05"<?php if ($this->_tpl_vars['value']['D'] == '05'): ?> selected="selected"<?php endif; ?>>5th</option>
	<option value="06"<?php if ($this->_tpl_vars['value']['D'] == '06'): ?> selected="selected"<?php endif; ?>>6th</option>
	<option value="07"<?php if ($this->_tpl_vars['value']['D'] == '07'): ?> selected="selected"<?php endif; ?>>7th</option>
	<option value="08"<?php if ($this->_tpl_vars['value']['D'] == '08'): ?> selected="selected"<?php endif; ?>>8th</option>
	<option value="09"<?php if ($this->_tpl_vars['value']['D'] == '09'): ?> selected="selected"<?php endif; ?>>9th</option>
	<option value="10"<?php if ($this->_tpl_vars['value']['D'] == '10'): ?> selected="selected"<?php endif; ?>>10th</option>
	<option value="11"<?php if ($this->_tpl_vars['value']['D'] == '11'): ?> selected="selected"<?php endif; ?>>11th</option>
	<option value="12"<?php if ($this->_tpl_vars['value']['D'] == '12'): ?> selected="selected"<?php endif; ?>>12th</option>
	<option value="13"<?php if ($this->_tpl_vars['value']['D'] == '13'): ?> selected="selected"<?php endif; ?>>13th</option>
	<option value="14"<?php if ($this->_tpl_vars['value']['D'] == '14'): ?> selected="selected"<?php endif; ?>>14th</option>
	<option value="15"<?php if ($this->_tpl_vars['value']['D'] == '15'): ?> selected="selected"<?php endif; ?>>15th</option>
	<option value="16"<?php if ($this->_tpl_vars['value']['D'] == '16'): ?> selected="selected"<?php endif; ?>>16th</option>
	<option value="17"<?php if ($this->_tpl_vars['value']['D'] == '17'): ?> selected="selected"<?php endif; ?>>17th</option>
	<option value="18"<?php if ($this->_tpl_vars['value']['D'] == '18'): ?> selected="selected"<?php endif; ?>>18th</option>
	<option value="19"<?php if ($this->_tpl_vars['value']['D'] == '19'): ?> selected="selected"<?php endif; ?>>19th</option>
	<option value="20"<?php if ($this->_tpl_vars['value']['D'] == '20'): ?> selected="selected"<?php endif; ?>>20th</option>
	<option value="21"<?php if ($this->_tpl_vars['value']['D'] == '21'): ?> selected="selected"<?php endif; ?>>21st</option>
	<option value="22"<?php if ($this->_tpl_vars['value']['D'] == '22'): ?> selected="selected"<?php endif; ?>>22nd</option>
	<option value="23"<?php if ($this->_tpl_vars['value']['D'] == '23'): ?> selected="selected"<?php endif; ?>>23rd</option>
	<option value="24"<?php if ($this->_tpl_vars['value']['D'] == '24'): ?> selected="selected"<?php endif; ?>>24th</option>
	<option value="25"<?php if ($this->_tpl_vars['value']['D'] == '25'): ?> selected="selected"<?php endif; ?>>25th</option>
	<option value="26"<?php if ($this->_tpl_vars['value']['D'] == '26'): ?> selected="selected"<?php endif; ?>>26th</option>
	<option value="27"<?php if ($this->_tpl_vars['value']['D'] == '27'): ?> selected="selected"<?php endif; ?>>27th</option>
	<option value="28"<?php if ($this->_tpl_vars['value']['D'] == '28'): ?> selected="selected"<?php endif; ?>>28th</option>
	<option value="29"<?php if ($this->_tpl_vars['value']['D'] == '29'): ?> selected="selected"<?php endif; ?>>29th</option>
	<option value="30"<?php if ($this->_tpl_vars['value']['D'] == '30'): ?> selected="selected"<?php endif; ?>>30th</option>
	<option value="31"<?php if ($this->_tpl_vars['value']['D'] == '31'): ?> selected="selected"<?php endif; ?>>31st</option>
</select><br />
Year: <input type="text" name="item[<?php echo $this->_tpl_vars['property']['id']; ?>
][Y]" size="5" maxlength="4" value="<?php if ($this->_tpl_vars['value']['Y']): ?><?php echo $this->_tpl_vars['value']['Y']; ?>
<?php else: ?><?php echo $this->_tpl_vars['default_year']; ?>
<?php endif; ?>" />