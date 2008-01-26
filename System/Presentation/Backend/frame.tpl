{* if count($sm_messages) *}
<div id="user-message-container">
  {foreach from=$sm_messages item="message" name="messages"}
  <div class="user-message" id="user_message_{$smarty.foreach.messages.iteration}">
    <input type="button" value="OK" class="user-message-dismiss" onclick="hideUserMessage('user_message_{$smarty.foreach.messages.iteration}');" />
    {$message->getMessage()}
  </div>
  {/foreach}
</div>
{* /if *}

{include file=$sm_navigation}

{include file=$sm_interface}
