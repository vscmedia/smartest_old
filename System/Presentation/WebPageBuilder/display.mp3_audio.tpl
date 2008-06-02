<?sm:capture name="_width" assign="_width":?><?sm:if $render_data.width:?><?sm:$render_data.width:?><?sm:else:?>240<?sm:/if:?><?sm:/capture:?>
<script language="JavaScript" src="<?sm:$domain:?>Resources/System/Javascript/mp3_player/audio-player.js"></script>
<object type="application/x-shockwave-flash" data="<?sm:$domain:?>Resources/System/Assets/mp3_player.swf" id="mp3_asset_<?sm:$asset_info.stringid:?>" height="24" width="<?sm:$_width:?>">
<param name="movie" value="<?sm:$domain:?>Resources/System/Assets/mp3_player.swf">
<param name="FlashVars" value="playerID=1&amp;soundFile=<?sm:$domain:?>Resources/Assets/<?sm:$asset_info.url:?>">
<param name="quality" value="high">
<param name="menu" value="false">
<param name="wmode" value="transparent">
</object>