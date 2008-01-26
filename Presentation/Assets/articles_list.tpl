<div id="items-list">

{repeat from="recent_articles" limit="10"}

{capture name="link_contents" assign="link_contents"}metapage:name=article:id={$repeated_item.id}{/capture}

<div style="margin:0 0 15px 0">
  
  <h4>{$repeated_item.name}</h4>
  
  <div class="list-item">
    <div style="font-size:11px">{property name="published_at" context="repeat"}</a>
    {capture name="article_text" assign="article_text"}{property name="article_text" context="repeat"}{/capture}
    <p>{$article_text|summary}</p>
    {link to=$link_contents with="Read More"}
  </div>
  
</div>

{/repeat}

</div>