<div id="work-area">
    
    {load_interface file="edit_tabs.tpl"}
    
    <h3>Comments</h3>
    
    <form action="{$domain}{$section}/itemComments" id="comment_status_filter">
      
      <input type="hidden" name="item_id" value="{$item.id}" />
      
      <div class="special-box">
        Show:
        <select name="show" onchange="$('comment_status_filter').submit()">
          <option value="SM_COMMENTSTATUS_APPROVED"{if $show=='SM_COMMENTSTATUS_APPROVED'} selected="selected"{/if}>Approved comments</option>
          <option value="SM_COMMENTSTATUS_PENDING"{if $show=='SM_COMMENTSTATUS_PENDING'} selected="selected"{/if}>Comments awaiting moderation</option>
          <option value="SM_COMMENTSTATUS_REJECTED"{if $show=='SM_COMMENTSTATUS_REJECTED'} selected="selected"{/if}>Rejected comments</option>
        </select>&nbsp;&nbsp;({$num_comments} comment{if $num_comments != 1}s{/if} with this status)
      </div>
    
    </form>
    
{foreach from=$comments item="comment"}
    
    <div style="background-color:#{cycle values="fff,ddd"};padding:5px">
      <strong>Author name</strong>: {$comment.author_name}<br />
      <strong>Author website</strong>: {$comment.author_website}<br />
      <strong>Content</strong>: {$comment.content}<br />
      {if $comment.status != 'SM_COMMENTSTATUS_APPROVED'}<a href="{$domain}{$section}/moderateComment?item_id={$item.id}&amp;comment_id={$comment.id}&amp;action=APPROVE&amp;from=item_list&amp;fromStatus={$show}">Approve this comment</a> | {/if}
      {if $comment.status != 'SM_COMMENTSTATUS_PENDING'}<a href="{$domain}{$section}/moderateComment?item_id={$item.id}&amp;comment_id={$comment.id}&amp;action=MAKEPENDING&amp;from=item_list&amp;fromStatus={$show}">Make this comment 'pending'</a> | {/if}
      {if $comment.status != 'SM_COMMENTSTATUS_REJECTED'}<a href="{$domain}{$section}/moderateComment?item_id={$item.id}&amp;comment_id={$comment.id}&amp;action=REJECT&amp;from=item_list&amp;fromStatus={$show}">Reject this comment</a>{/if}
    </div>
    
{/foreach}
    
</div>

<div id="actions-area"></div>