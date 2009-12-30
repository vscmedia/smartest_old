<ul class="tabset">
    <li{if $method == "editModel"} class="current"{/if}><a href="{$domain}{$section}/editModel?class_id={$model.id}">Model info</a></li>
    <li{if $method == "getItemClassProperties"} class="current"{/if}><a href="{$domain}{$section}/getItemClassProperties?class_id={$model.id}">Model properties</a></li>
</ul>