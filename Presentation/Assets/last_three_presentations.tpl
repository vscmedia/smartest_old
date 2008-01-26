<h4>Presentations</h4>

{repeat from="recent_presentations" limit=3}
  
  {capture name="download_asset_id" assign="download_asset_id"}{property name="presentation_file" context="repeat"}{/capture}
  {capture name="link_contents" assign="link_contents"}download:id={$download_asset_id}{/capture}

  <p>
    <strong>{$repeated_item.name}</strong><br />
    {property name="presentation_date" context="repeat"}<br />
    {property name="short_description" context="repeat"}
    <br />{link to=$link_contents with="Download"}
  </p>

{/repeat}