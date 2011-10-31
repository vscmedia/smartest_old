<?sm:if $render_data.flash:?>
<?sm:capture name="_width" assign="_width":?><?sm:if $render_data.width:?><?sm:$render_data.width:?><?sm:else:?>290<?sm:/if:?><?sm:/capture:?>
<script type="text/javascript" src="<?sm:$domain:?>Resources/System/Javascript/mp3_player/audio-player.js"></script>

<object type="application/x-shockwave-flash" data="<?sm:$domain:?>Resources/System/Assets/mp3_player.swf" id="mp3_asset_<?sm:$asset_info.stringid:?>" height="24" width="<?sm:$_width:?>">
  <param name="movie" value="<?sm:$domain:?>Resources/System/Assets/mp3_player.swf">
  <param name="FlashVars" value="playerID=1&amp;soundFile=<?sm:$domain:?>Resources/Assets/<?sm:$asset_info.url:?>">
  <param name="quality" value="high">
  <param name="menu" value="false">
  <param name="wmode" value="transparent">
</object>

<?sm:else:?>
<object width="<?sm:if $render_data.width:?><?sm:$render_data.width:?><?sm:else:?>290<?sm:/if:?>" height="16"> 
  <param name="kioskmode" value="true"> 
  <param name="src" value="<?sm:$domain:?>Resources/Assets/<?sm:$asset_info.url:?>"> 
  <param name="autoplay" value="false"> 
  <param name="controller" value="true"> 
  <embed src="<?sm:$domain:?>Resources/Assets/<?sm:$asset_info.url:?>" type="video/quicktime" height="16" width="<?sm:if $render_data.width:?><?sm:$render_data.width:?><?sm:else:?>290<?sm:/if:?>" controller="true" autoplay="false" kioskmode="true"> 
</object>

<?sm:/if:?>