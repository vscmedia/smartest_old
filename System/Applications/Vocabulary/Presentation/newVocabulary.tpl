<h3><a href="{$domain}dataManager">Data Manager</a> > <a href="{$domain}{$section}/getItemVocabulary">Vocabularies</a> > New Vocabulary</h3>

<table style="width:750px" border="0" cellspacing="2" cellpadding="1">
	
	<form name="addNewVocabulary" action="{$domain}{$section}/addNewVocabulary" method="post">

	<tr>
			<td style="width:100px" valign="top">Name:</td>
			<td><input type="text" id="vocabulary_name" name="vocabulary_name" />	
	</tr>
	
	<tr>
			<td style="width:100px" valign="top">Namespace:</td>
			<td><input type="text" id="vocabulary_namespace" name="vocabulary_namespace" />	
	</tr>

	<tr>
			<td style="width:100px" valign="top">Prefix:</td>
			<td><input type="text" id="vocabulary_prefix" name="vocabulary_prefix" />	
	</tr>
	
	<tr>
			<td style="width:100px" valign="top">Description:</td>
			<td><input type="text" id="vocabulary_description" name="vocabulary_description" />	
	</tr>
	
	<tr><td style="width:100px" valign="top">Type:</td>
	<td>
	<select name="vocabulary_type">
	<option value="STRING">String (255 Chars max)</option>
	<option value="TEXT">Full Text</option>
	<option value="NUMERIC">Number</option>
	<option value="BOOLEAN">True/False (boolean)</option>
	<option value="NODE">Node</option>
	</select>
	</td>
	</tr>

	<tr>
	<td>
	</td>
	
	<td>
		<input type="submit" value="Add" />
		<input type="button" value="Cancel" onclick="window.location='{$domain}{$section}';" />
	</td>
	</tr>
</table>
</form>	



