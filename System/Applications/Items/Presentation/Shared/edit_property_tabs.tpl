<ul class="tabset">
    {if $can_edit_properties}<li{if $method == "editItemClassProperty"} class="current"{/if}><a href="{$domain}{$section}/editItemClassProperty?itemproperty_id={$property.id}{if $request_parameters.class_id}&amp;class_id={$request_parameters.class_id}{/if}">Edit property</a></li>{/if}
    {if $property.is_fk}<li{if $method == "viewItemClassPropertyValueSpread"} class="current"{/if}><a href="{$domain}{$section}/viewItemClassPropertyValueSpread?itemproperty_id={$property.id}{if $request_parameters.class_id}&amp;class_id={$request_parameters.class_id}{/if}">View values spread</a></li>{/if}
</ul>

