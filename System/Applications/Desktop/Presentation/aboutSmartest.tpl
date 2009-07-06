<div id="work-area">
  <h3>About Smartest</h3>
  <strong>Version</strong>: {$version} (Revision {$revision})<br />
  <strong>Installed</strong>: {if $system_installed_timestamp > 0}{$system_installed_timestamp|date_format:"%A %B %e, %Y, %l:%M%p"}{else}<em>Unknown</em>{/if}<br />
  <strong>Platform</strong>: {$platform}<br />
</div>