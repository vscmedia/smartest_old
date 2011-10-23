{if $status == 2}
  <img src="{$domain}Resources/Icons/tick.png" alt="" /> Regularized property successfully ({$num_changed_values} values affected).
{elseif $status == 1}
  <img src="{$domain}Resources/Icons/information.png" alt="" /> No changes were needed.
{else}
  <img src="{$domain}Resources/Icons/cross.png" alt="" /> Failed to regularize property{if $status_message}: {$status_message}{else}.{/if}
{/if}