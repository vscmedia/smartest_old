<div class="smartest_preview_top_bar" id="sm_previewBar">
    Smartest Pre-Render Overhead: <?sm:$overhead_time:?>ms |
    Page Build Time: <?sm:$build_time:?>ms |
    Total time taken: <?sm:$total_time:?> |
    <a href="#" onclick="document.getElementById('sm_previewBar').style.display = 'none'; return false;">Hide</a>
    <?sm:if $hide_liberate_link:?> | <a href="<?sm:$domain:?>smartest/pages">Back to site pages</a><?sm:/if:?>
    <?sm:if !$hide_liberate_link:?> | <a href="<?sm:$liberate_link_url:?>" target="_top">Preview in full screen</a><?sm:else:?> | <a href="<?sm:$preview_link_url:?>">Back to Smartest preview</a><?sm:/if:?>
    <?sm:if $show_item_edit_link:?> | <a href="<?sm:$domain:?>datamanager/openItem?item_id=<?sm:$item_id:?>&amp;from=preview&amp;page_webid=<?sm:$page_webid:?>" target="_top">Edit <?sm:$model_name:?></a><?sm:/if:?>
    | <a id="sm-edit-button-toggle" href="#toggle-edit-buttons">Hide edit buttons</a>
</div>

<script type="text/javascript">

  var editButtonsVisible = true;
  
  var hideEditButtons = function(){
      
      var elements = document.getElementsByClassName('sm-edit-button');

      for (var i = 0; i < elements.length; i++) {
          elements[i].style.display = 'none';
      }
      
      editButtonsVisible = false;
      
  }
  
  var showEditButtons = function(){

        var elements = document.getElementsByClassName('sm-edit-button');

        for (var i = 0; i < elements.length; i++) {
            elements[i].style.display = 'inline';
        }
        
        editButtonsVisible = true;

    }
    
    document.getElementById('sm-edit-button-toggle').addEventListener('click', function(event){
        
        event.preventDefault();
        
        if(editButtonsVisible){
            hideEditButtons();
            document.getElementById('sm-edit-button-toggle').innerHTML = 'Show edit buttons';
        }else{
            showEditButtons();
            document.getElementById('sm-edit-button-toggle').innerHTML = 'Hide edit buttons';
        }
        
    }, false);
  
</script>