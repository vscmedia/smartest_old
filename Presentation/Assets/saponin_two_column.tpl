    <table cellspacing="0" border="0" cellpadding="0" id="three-column-cheat">
      <tr>
        <td id="left-column" valign="top">
          
          <div class="menu-box">
            <h4>Custom Search</h4>
            <ul class="custom-search-links">
              <li><a href="{$domain}tags/institutional-investor.html">Institutional Investor</a></li>
              <li><a href="{$domain}tags/individual-investor.html">Individual Investor</a></li>
              <li><a href="{$domain}tags/family-office.html">Family Office</a></li>
              <li><a href="{$domain}tags/journalists.html">Journalist</a></li>
              <li><a href="{$domain}tags/government.html">Government</a></li>
              <li><a href="{$domain}tags/analyst.html">Analyst</a></li>
              <li><a href="{$domain}tags/brokerage-house.html">Brokerage House</a></li>
              <li><a href="{$domain}tags/ngos.html">NGO</a></li>
              <li><a href="{$domain}tags/pharmaceutical.html">Pharmaceutical</a></li>
              <li><a href="{$domain}tags/cosmetics.html">Cosmetics</a></li>
              <li><a href="{$domain}tags/health-care.html">Health Care</a></li>
              <li><a href="{$domain}tags/animal-health-care.html">Animal Health Care</a></li>
              <li><a href="{$domain}tags/scientist.html">Scientist</a></li>
              <li><a href="{$domain}tags/purchasing-agent.html">Purchasing Agent</a></li>
            </ul>
          </div>
          
          <div class="menu-box-bottom">
                        {capture name="lower_block_content" assign="lower_block_content"}{field name="lower_block_content"}{/capture}

            {if $this.fields.lower_block_content == 'custom_search'}

            {template name="saponin_custom_search.tpl"}

            {elseif $this.fields.lower_block_content == 'news_articles'}

            {template name="left_column_news.tpl"}

            {elseif $this.fields.lower_block_content == 'presentations'}

            {template name="last_three_presentations.tpl"}

            {else}

            {template name="last_three_press_releases.tpl"}

            {/if}
          </div>
        </td>
    
    <td id="central-column" valign="top" onmouseover="hideAllMenus()">
      
      <div id="central-column-content">
        {if $this.fields.category != 'home'}
          {if $this.principal_item.name}<h3>{$this.principal_item.name}</h3>{else}<h3>{$this.page.title}</h3>{/if}
          <div id="breadcrumbs">{breadcrumbs}</div>
        {/if}

        {container name="central_column_dynamic_content"}
        {template name="saponin_footer.tpl"}        

      </div>
    </td>
    
  </tr>
</table>