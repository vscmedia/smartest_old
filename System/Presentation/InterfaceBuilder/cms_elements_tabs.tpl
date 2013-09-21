<ul class="tabset">
  <li{if $method == "sitePages"} class="current"{/if}><a href="{$domain}smartest/pages">Pages</a></li>
  <li{if $method == "pageGroups"} class="current"{/if}><a href="{$domain}smartest/pagegroups">Page groups</a></li>
  <li{if $method == "placeholders"} class="current"{/if}><a href="{$domain}websitemanager/placeholders">Placeholders</a></li>
  <li{if $method == "listFields"} class="current"{/if}><a href="{$domain}smartest/fields">Fields</a></li>
</ul>