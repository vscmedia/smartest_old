<div class="smartest_preview_top_bar" id="sm_previewBar">
    Smartest Pre-Render Overhead: <?sm:$overhead_time:?>ms |
    Page Build Time: <?sm:$build_time:?>ms |
    Total time taken: <?sm:$total_time:?> |
    <a href="#" onclick="document.getElementById('sm_previewBar').style.display = 'none'; return false;">Hide</a>
    <?sm:if !$hide_liberate_link:?> | <a href="<?sm:$liberate_link_url:?>" target="_top">Preview in full screen</a><?sm:else:?> | <a href="<?sm:$preview_link_url:?>">Back to Smartest preview</a><?sm:/if:?>
    <?sm:if $show_item_edit_link:?> | <a href="<?sm:$domain:?>datamanager/openItem?item_id=<?sm:$item_id:?>" target="_top">Edit <?sm:$model_name:?></a><?sm:/if:?>
</div>