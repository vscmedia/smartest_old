<script type="text/javascript">
// <![CDATA[
  {literal}
  Smartest.AjaxModalViewer.variables.deleteAssetComment = function(commentId){
    if(confirm('Really delete this note?')){
      var commentDomId = 'comment-'+commentId;
      $(commentDomId).fade({duration: 0.5});
      setTimeout(function(){
        if(!$('none-yet') && $$('#comments-list li').length == 1){
            var div = new Element('div', {class: 'instruction'}).update('No notes yet');
            var li = new Element('li', {style: 'padding:5px;', id: 'none-yet'});
            li.appendChild(div);
            $('comments-list').appendChild(li);
          }
        }, 510);
      new Ajax.Request(sm_domain+'ajax:assets/removeAssetComment?comment_id='+commentId, {
        onSuccess: function(){
          new Ajax.Updater('comment-stream', sm_domain+'ajax:assets/assetComments', {
            parameters: {'asset_id': Smartest.AjaxModalViewer.variables.asset_id},
            evalScripts: true,
            onComplete: function(){
              Smartest.AjaxModalScroller = new Control.ScrollBar('modal-updater', 'modal-scrollbar-track');
              Smartest.AjaxModalScroller.scrollTo('bottom');
            }
          });
          $('comment-content').value = ' ';
        }
      });
    }
  }
  {/literal}
  // ]]>
</script>

    <ul style="padding:0px;margin:0px;list-style-type:none" id="comments-list">
{foreach from=$comments item="comment"}
      <li style="padding:5px;" id="comment-{$comment.id}">
        <p><b>{$comment.user.full_name}</b>, {$comment.posted_at}</p>
        <p>{$comment.content}</p>
        <p class="small" style="font-size:10px"><a href="#delete-comment-{$comment.id}" id="delete-comment-link-{$comment.id}">Delete</a></p>
      </li>
{foreachelse}
      <li style="padding:5px;" id="none-yet"><div class="instruction">No notes yet</div></li>
{/foreach}
    </ul>
    
<script type="text/javascript">
  {foreach from=$comments item="comment"}
  $('delete-comment-link-{$comment.id}').observe('click', function(e){ldelim}Smartest.AjaxModalViewer.variables.deleteAssetComment('{$comment.id}');e.stop();{rdelim});
  {/foreach}
</script>