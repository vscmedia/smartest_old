<div id="work-area">
  <h3>Placeholders</h3>
  <table border="0" cellpadding="2" cellspacing="0">
{foreach from=$placeholders item="placeholder"}
    <tr>
      <td><img src="{$domain}Resources/Icons/published_placeholder.gif" /></td>
      <td>{$placeholder.name}</td>
      <td><a href="{$domain}{$section}/editPlaceholder?placeholder_id={$placeholder.id}">edit</a></td>
    </tr>
{/foreach}
  </table>
</div>

<div id="actions-area">

</div>