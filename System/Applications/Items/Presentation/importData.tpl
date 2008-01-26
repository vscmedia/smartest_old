<h3><a href="{$domain}{$section}">Data Manager</a> >  <a href="{$domain}{$section}/getItemClassMembers?class_id={$itemClass.itemclass_id}">{$itemClass.itemclass_plural_name}</a> &gt; Import Data

<form action="{$domain}{$section}/importDataAction" method="post" enctype="multipart/form-data" >
<input type="hidden" name="class_id" value="{$itemClass.itemclass_id}">
<table style="width:750px" border="0" cellspacing="2" cellpadding="1">
	<tr>
		<td >File:</td>
		<td><input type="file" name="file" /></td>
	</tr>
<tr>
		<td colspan="2"><input type="checkbox" name="indicator" checked="true" >Please check this box to indicate that the first row of the csv file will be the field names</td>
		
	</tr>
<tr>
		<td colspan="2"><input type="submit" value="Submit" /></td>
		
	</tr>
</table>
</form>

