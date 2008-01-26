<div id="work-area">
{load_interface file=$formTemplateInclude}
</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getAssetTypeMembers?asset_type={$asset_type.id}'">View all {$asset_type.label} assets</a></li>
  </ul>
</div>