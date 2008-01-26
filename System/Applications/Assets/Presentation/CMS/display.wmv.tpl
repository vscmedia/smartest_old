<object width="{$render_data.width}" height="{$render_data.height}" classid="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95" id="mediaplayer1">
  <param name="Filename" value="{$domain}Resources/Assets/{$asset_info.url}">
  <param name="AutoStart" value="{$render_data.auto_start}">
  <param name="ShowControls" value="{$render_data.show_controller}">
  <param name="ShowStatusBar" value="{$render_data.show_status_bar}">
  <param name="ShowDisplay" value="{$render_data.show_display}">
  <param name="AutoRewind" value="{$render_data.auto_start}">
  <embed type="application/x-mplayer2" pluginspage="http://www.microsoft.com/Windows/Downloads/Contents/MediaPlayer/" width="{$render_data.width}" height="{$render_data.height}" src="{$domain}Resources/Assets/{$asset_info.url}" filename="{$domain}Resources/Assets/{$asset_info.url}" autostart="{$render_data.auto_start}" showcontrols="{$render_data.show_controller}" showstatusbar="{$render_data.show_status_bar}" showdisplay="{$render_data.show_display}" autorewind="{$render_data.auto_start}"></embed> 
</object>