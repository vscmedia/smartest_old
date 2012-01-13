<div id="work-area">
  
  <script type="text/javascript">
    Smartest.AjaxModalViewer.variables.asset_id = '{$asset.id}';
    Smartest.AjaxModalViewer.variables.author_name = '{$_user.fullname}';
    Smartest.AjaxModalScroller.scrollTo('bottom');
  </script>
  
  <div id="comment-stream">
    {load_interface file="asset_comments.tpl"}
  </div>
  
  <div>
    <div class="instruction">Leave a note about this file:</div>
  
    <form action="{$domain}ajax:{$section}/submitAssetComment" method="post" id="asset-comment-form">
      <input type="hidden" name="asset_id" value="{$asset.id}" />
      <textarea name="comment_content" style="width:500px;height:50px" id="comment-content"> </textarea><br />
      <input type="button" value="Save" id="comment-submit-button" />
    </form>
  </div>
  
  <script type="text/javascript">
  // <![CDATA[
    {literal}
    $('comment-submit-button').observe('click', function(){
      if(($('comment-content').value.length) > 1){
        $('asset-comment-form').request({
          onComplete: function(){
            
            var commentContent = $F('comment-content');
            
            // Update the Scroller (may take a while)
            new Ajax.Updater('comment-stream', sm_domain+'ajax:assets/assetComments', {
              parameters: {'asset_id': Smartest.AjaxModalViewer.variables.asset_id},
              evalScripts: true,
              onComplete: function(){
                Smartest.AjaxModalScroller.scrollTo('bottom');
              }
            });
            
            // Remove the comment from the form now that it's been posted
            $('comment-content').value = ' ';
            
            // If this is the first comment, hide the "none yet" li
            if($('none-yet') && $('none-yet').visible()){$('none-yet').hide();}
            
            // Insert a temporary dummy to help the scroller
            var cli = new Element('li', {style: 'padding:5px', id: 'new-comment'});
            var ca = new Element('b').update(Smartest.AjaxModalViewer.variables.author_name);
            var date = new Element('img', {src: sm_domain+'Resources/System/Images/ajax-loader.gif'});
            var cip = new Element('p');
            var ccp = new Element('p').update(commentContent);
            var clp = new Element('p', {class: 'small', style: 'font-size:10px'}).update(' ');
            var dl = new Element('a', {href: '#'}).update('Delete');
            cip.appendChild(ca);
            cip.appendChild(date);
            cli.appendChild(cip);
            cli.appendChild(ccp);
            clp.appendChild(dl);
            cli.appendChild(clp);
            $('comments-list').appendChild(cli);
            
            // Readjust the scroller
            Smartest.AjaxModalScroller = new Control.ScrollBar('modal-updater', 'modal-scrollbar-track');
            Smartest.AjaxModalScroller.scrollTo('bottom');

          }
        });
      }
    });
    {/literal}
    // ]]>
  </script>
  
</div>
