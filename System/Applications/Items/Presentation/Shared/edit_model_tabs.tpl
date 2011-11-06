<ul class="tabset">
    <li{if $method == "editModel"} class="current"{/if}><a href="{$domain}{$section}/editModel?class_id={$model.id}">Model info</a></li>
    {if $can_edit_properties}<li{if $method == "getItemClassProperties"} class="current"{/if}><a href="{$domain}{$section}/getItemClassProperties?class_id={$model.id}">Model properties</a></li>{/if}
    {if $can_edit_properties}<li{if $method == "editItemClassPropertyOrder"} class="current"{/if}><a href="{$domain}{$section}/editItemClassPropertyOrder?class_id={$model.id}">Edit property order</a></li>{/if}
</ul>