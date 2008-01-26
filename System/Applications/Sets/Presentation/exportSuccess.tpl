<div id="work-area">
<h3><a href="{$domain}datamanager">Data Manager</a> &gt; <a href="{$domain}{$section}">Sets</a> &gt;{$set.set_name}  &gt; Export Data Complete</h3>

<div  class="instruction">the export is complete. Your xml feed is available at the following links</div>
<a href="{$domain}xmlexport/exportData?schema={$content.schema_varname}&model={$set.itemclass_varname}&set={$set.set_varname}&dataexport={$content.dataexport_varname}">
{$domain}xmlexport/exportData?schema={$content.schema_varname}&model={$set.itemclass_varname}&set={$set.set_varname}&dataexport={$content.dataexport_varname} </a>
<br>
<a href="{$domain}smartest/{$content.schema_varname}/{$set.itemclass_varname}/{$set.set_varname}/{$content.dataexport_varname}.xml">
{$domain}smartest/{$content.schema_varname}/{$set.itemclass_varname}/{$set.set_varname}/{$content.dataexport_varname}.xml </a>

</div>