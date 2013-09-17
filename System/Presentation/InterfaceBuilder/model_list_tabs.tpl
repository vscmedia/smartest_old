<ul class="tabset">
    <li{if $method == "getItemClassMembers"} class="current"{/if}><a href="{$domain}datamanager/getItemClassMembers?class_id={$model.id}">All {$model.plural_name|lower}</a></li>
    <li{if $method == "getItemClassSets"} class="current"{/if}><a href="{$domain}sets/getItemClassSets?class_id={$model.id}">Sets of {$model.plural_name|lower}</a></li>
    <li{if $method == "getItemClassComments"} class="current"{/if}><a href="{$domain}datamanager/getItemClassComments?class_id={$model.id}">Comments</a></li>
    <li{if $method == "editModel"} class="current"{/if}><a href="{$domain}{$section}/editModel?class_id={$model.id}">{if $can_edit_properties}Edit model{else}Model attributes{/if}</a></li>
    {if $can_edit_properties}<li{if $method == "getItemClassProperties"} class="current"{/if}><a href="{$domain}{$section}/getItemClassProperties?class_id={$model.id}">Model properties</a></li>{/if}
    {if $can_edit_properties}<li{if $method == "editItemClassPropertyOrder"} class="current"{/if}><a href="{$domain}{$section}/editItemClassPropertyOrder?class_id={$model.id}">Edit property order</a></li>{/if}
</ul>