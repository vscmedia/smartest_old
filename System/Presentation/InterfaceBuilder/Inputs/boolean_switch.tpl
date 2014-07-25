<a href="#toggle-switch" id="{$_input_data.id}-link">{if $_input_data.value}<img src="{$domain}Resources/System/Images/bool-switch-on.png" id="{$_input_data.id}-img" alt="On" />{else}<img src="{$domain}Resources/System/Images/bool-switch-off.png" id="{$_input_data.id}-img" alt="Off" />{/if}</a>
<input type="hidden" name="{$_input_data.name}" value="{if $_input_data.value}TRUE{else}FALSE{/if}" id="{$_input_data.id}-input" />

<script type="text/javascript">

  $('{$_input_data.id}-link').observe('click', function(e){ldelim}
    e.stop();
    if($('{$_input_data.id}-input').value == 'TRUE'){ldelim}
      $('{$_input_data.id}-input').value = 'FALSE';
      $('{$_input_data.id}-img').src = '{$domain}Resources/System/Images/bool-switch-off.png';
    {rdelim}else{ldelim}
      $('{$_input_data.id}-input').value = 'TRUE';
      $('{$_input_data.id}-img').src = '{$domain}Resources/System/Images/bool-switch-on.png';
    {rdelim};
  {rdelim});
</script>