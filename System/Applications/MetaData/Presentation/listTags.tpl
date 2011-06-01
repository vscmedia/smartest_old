<div id="work-area">
<h3>Tags</h3>

<div class="instruction">Tags exist across all your sites. Some pags may not make sense for certain sites, but they can be ignored.</div>

{foreach from=$tags item="tag" key="key"}
<a class="tag" href="{$domain}smartest/tagged/{$tag.name}">{$tag.label}</a>
{/foreach}
</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Tags Options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addTag'"><img src="{$domain}Resources/Icons/tag_blue.png" />Add Tag</a></li>    
  </ul>
</div>