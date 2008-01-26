{if empty($this.tagged_objects)}
  
  <p>There are no items tagged with "{$this.page.tag.label}"</p>
  
{else}

{foreach from=$this.tagged_objects item="object"}

<div style="margin:0 0 10px 0">
  
  <h4>{$object.title}</h4>
  
  <div class="list-item">
    <p>
      {$object.date|date_format:"%A %B %e, %Y"}<br />
      {$object.description}<br />
      {link to=$object.url with="Read More"}
    </p>
  </div>
  
  
  
</div>

{/foreach}

<p>{if $this.page.is_tag_page}<img src="{$domain}Resources/Icons/rss.png" />&nbsp;<a href="{$domain}tags/{$this.page.tag.name}/feed">Feed for tag "{$this.page.tag.label}"</a>{/if}</p>

{/if}