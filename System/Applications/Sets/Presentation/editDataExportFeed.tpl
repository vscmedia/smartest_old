<div id="work-area">

<h3>Data Exports &gt; editDataExportFeed</h3>
<a name="top"></a>


<form id="pageViewForm" method="post" action="updateDataExportFeed">
    <input type="hidden" name="export_id" value="{$content.export_id}"/>
  <table cellpadding="1" cellspacing="1">
	<tr><td> Choose A Set</td>
	<td>
	<select name="set">
	{foreach from=$sets key=key item=set}
	<option value="{$set.set_id}">{$set.set_name}</option>
	{/foreach}
	</select>
	</td>
	</tr>
	<tr><td> Choose A Pairing</td>
	<td>
	<select  name="pair">
	{foreach from=$pairing key=key item=pair}
	<option value="{$pair.dataexport_pairing_id}">{$pair.dataexport_name}</option>
	{/foreach}
	</select>
	</td>
	</tr>
	<tr><td colspan="2" align="right"><input type="submit" value="Edit"></td></tr>
  </table>
</form>

</div>