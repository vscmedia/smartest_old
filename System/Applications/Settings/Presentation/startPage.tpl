<div id="work-area">

<h3>Settings</h3>

{*<table border="0" cellspacing="0" cellpadding="0" style="width:850px">
<tr>
<td>
{if $release != false}
<div class="notify-failure">
New version of Smartest is available. Upgrade? 
</div>
{elseif $release == "downgrade"}
<div class="notify-failure">
Cannot downgrade
</div>
{/if}
</td>
</tr>
</table>*}

<ul class="basic-list">
  <li><a href="{$domain}{$section}/systemSettings">System Settings</a></li>
  <li><a href="{$domain}smartest/users">Users &amp; Permissions</a></li>
</ul>

</div>