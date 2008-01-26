<div id="items-list">

{repeat from="press_releases_main_list"}

{capture name="link_contents" assign="link_contents"}metapage:name=press-release:id={$repeated_item.id}{/capture}

<div style="margin:0 0 15px 0">
  
  <h4>{$repeated_item.name}</h4>
  
  <div class="list-item">
    <div style="font-size:11px">{property name="date_published" context="repeat"}</div>
    {capture name="body_text" assign="body_text"}{property name="body_text" context="repeat"}{/capture}
    <p>{$body_text|summary}</p>
    {link to=$link_contents with="Read More"}
  </div>
  
</div>

{/repeat}

</div>