<?sm:if $render_data.use_style_tag == 'true' || $render_data.use_style_tag == '1' || $render_data.use_style_tag == 'on' :?><style type="text/css">
  @import url('<?sm:$domain:?>Resources/Stylesheets/<?sm:$asset_info.url:?>');
</style><?sm:else:?><link rel="stylesheet" href="<?sm:$domain:?>Resources/Stylesheets/<?sm:$asset_info.url:?>" /><?sm:/if:?>