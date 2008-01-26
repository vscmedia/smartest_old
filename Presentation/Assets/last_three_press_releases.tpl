<h4>Press Releases</h4>
{repeat from="press_releases_main_list" limit=3}

  {capture name="link_contents" assign="link_contents"}metapage:name=press-release:id={$repeated_item.id}{/capture}
  {capture name="body_text" assign="body_text"}{property name="body_text" context="repeat"}{/capture}

  <p>
    <strong>{$repeated_item.name}</strong><br />
    {property name="date_published" context="repeat"}<br />
    {$body_text|summary:80}
    <br />{link to=$link_contents with="Read More"}
  </p>

{/repeat}