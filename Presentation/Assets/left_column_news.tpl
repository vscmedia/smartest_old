<h4>News</h4>

{repeat from="recent_articles" limit=3}

{capture name="link_contents" assign="link_contents"}metapage:name=article:id={$repeated_item.id}{/capture}
{capture name="body_text" assign="body_text"}{property name="article_text" context="repeat"}{/capture}

<p>
  {property name="published_at" context="repeat"}<br />
  {$body_text|summary:80}
  <br />{link to=$link_contents with="Read More"}
</p>

{/repeat}