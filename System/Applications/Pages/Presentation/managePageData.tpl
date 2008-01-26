<script language="javascript">
{literal}
function cancelForm(){
window.location={$domain}{$section}/;
}
{/literal}
</script>
<h3>Manage Page Data</h3>
{*{foreach from=$items item="item"}
	{$item.itempropertyvalue_content}<br/>
{/foreach}*}
<!--<a href="#" onclick="javascript:cancelForm()">[Cancel]</a><a href="{$domain}{$section}/"  > [OK]  </a>-->
<table>

{foreach from=$items item="item"}
<tr><td>{include file="$tplfilename" item_name=$item.item_name item_id=$item.item_id property=$item.property_details }
</td><td><a href="#" onclick="window.location='{$domain}datamanager/editItem?item_id={$item.item_id}'">edit</a></td></tr>
{/foreach}

</table>