{if is_numeric($template.id)}
  <ul class="tabset">
    <li{if $method == "editTemplate"} class="current"{/if}><a href="{$domain}{$section}/editTemplate?template={$template.id}">Edit template</a></li>
    <li{if $method == "templateInfo"} class="current"{/if}><a href="{$domain}{$section}/templateInfo?template={$template.id}">Template info</a></li>
  </ul>
{/if}