<ul class="tabset">
    <li{if $method == "getItemClassMembers"} class="current"{/if}><a href="{$domain}datamanager/getItemClassMembers?class_id={$model.id}">All {$model.plural_name|lower}</a></li>
    <li{if $method == "getItemClassSets"} class="current"{/if}><a href="{$domain}sets/getItemClassSets?class_id={$model.id}">Sets of {$model.plural_name|lower}</a></li>
    <li{if $method == "getItemClassComments"} class="current"{/if}><a href="{$domain}datamanager/getItemClassComments?class_id={$model.id}">Comments</a></li>
</ul>