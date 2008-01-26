    <table cellspacing="0" border="0" cellpadding="0" id="three-column-cheat">
      <tr>
        <td id="left-column" valign="top">
          
          <div class="menu-box">
            <p>
              “Saponin is commercializing the Prairie Carnation™, a proprietary plant species with healthcare, cosmetic, vaccine and pharmaceutical applications”
            </p>
          </div>
          
          <div class="menu-box-bottom">
            {capture name="lower_block_content" assign="lower_block_content"}{field name="lower_block_content"}{/capture}

            {if $this.fields.lower_block_content == 'press_releases'}

            {template name="last_three_press_releases.tpl"}

            {elseif $this.fields.lower_block_content == 'news_articles'}

            {template name="left_column_news.tpl"}

            {elseif $this.fields.lower_block_content == 'presentations'}

            {template name="last_three_presentations.tpl"}

            {else}

            {template name="saponin_custom_search.tpl"}

            {/if}
          </div>
        </td>
    
    <td id="central-column" valign="top" onmouseover="hideAllMenus()">
      
      <div id="central-column-content">
        {if $this.fields.category != 'home'}
          <h3>{$this.page.title}</h3>
          <div id="breadcrumbs">{breadcrumbs}</div>
        {/if}

        {container name="central_column_dynamic_content"}
        
        {template name="saponin_footer.tpl"}

      </div>
    </td>
    
    <td id="right-column" valign="top">
      <div id="right-column-content">
        {placeholder name="right_col_top_image"}
        {placeholder name="right_col_middle_image"}
        {placeholder name="right_col_bottom_image"}
      </div>
    </td>

    
  </tr>
</table>