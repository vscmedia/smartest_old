{if empty($this.search_results)}
  
  <p>Your search for "{$this.page.query}" did not return any results</p>
  
{else}

{foreach from=$this.search_results item="object"}

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

{/if}