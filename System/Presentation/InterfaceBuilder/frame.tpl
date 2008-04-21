<div id="user-message-container-outer">
  <div id="user-message-container-inner">
    {foreach from=$sm_messages item="message" name="messages"}
    {capture name="z_index" assign="z_index"}{math equation="x + y" x=$smarty.foreach.messages.iteration y=99}{/capture}
    {capture name="top" assign="top"}{math equation="x*10 + y" x=$smarty.foreach.messages.iteration y=70}{/capture}
    {capture name="left" assign="left"}{math equation="x*10 + y" x=$smarty.foreach.messages.iteration y=190}{/capture}
    <div class="user-message" id="user_message_{$smarty.foreach.messages.iteration}" style="z-index:{$z_index};top:{$top}px;left:{$left}px">
      <input type="button" value="OK" class="user-message-dismiss" onclick="hideUserMessage('user_message_{$smarty.foreach.messages.iteration}');" />
      {if $message->getType() == 2}
      <img src="{$domain}Resources/System/Images/msg_icon_success.png" alt="icon" />
      {elseif $message->getType() == 4}
      <img src="{$domain}Resources/System/Images/msg_icon_warning.png" alt="icon" />
      {elseif $message->getType() == 8}
      <img src="{$domain}Resources/System/Images/msg_icon_error.png" alt="icon" />
      {elseif $message->getType() == 16}
      <img src="{$domain}Resources/System/Images/msg_icon_permission.png" alt="icon" />
      {else}
      <img src="{$domain}Resources/System/Images/msg_icon_info.png" alt="icon" />
      {/if}
      <div class="user-message-text">{$message->getMessage()}</div>
    </div>
    {/foreach}
  </div>
</div>

{include file=$sm_navigation}

{include file=$sm_interface}
