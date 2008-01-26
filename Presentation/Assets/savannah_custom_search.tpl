<h4>Custom Search</h4>

{* <ul class="custom-search-links">
  {foreach from=$this.tags item="tag"}
  {capture name="tag_link" assign="tag_link"}tag:{$tag.name}{/capture}
  <li>{link to=$tag_link with=$tag.label}</li>
  {/foreach}
</ul> *}

  <ul class="custom-search-links">
      <li><a href="{$domain}tags/analyst.html">Analyst</a></li>
      <li><a href="{$domain}tags/brokerage-house.html">Brokerage House</a></li>
      <li><a href="{$domain}tags/family-office.html">Family Office</a></li>
      <li><a href="{$domain}tags/government.html">Government</a></li>
      <li><a href="{$domain}tags/individual-investor.html">Individual Investor</a></li>
      <li><a href="{$domain}tags/institutional-investor.html">Institutional Investor</a></li>
      <li><a href="{$domain}tags/journalists.html">Journalists</a></li>
      <li><a href="{$domain}tags/mining-house.html">Mining House</a></li>
      <li><a href="{$domain}tags/ngos.html">NGOs</a></li>
  </ul>