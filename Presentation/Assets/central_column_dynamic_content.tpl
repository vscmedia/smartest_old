<table cellspacing="13" border="0" cellpadding="0">
  <tr>
    
    <td id="left-column" valign="top">
      {* First Box *}
      
      <div class="menu-box">
        
        {capture name="upper_block_content" assign="upper_block_content"}{field name="upper_block_content"}{/capture}
        
        {if $this.fields.upper_block_content == 'custom_search'}
        
        {template name="savannah_custom_search.tpl"}
        
        {elseif $this.fields.upper_block_content == 'news_articles'}
        
        {template name="left_column_news.tpl"}
        
        {elseif $this.fields.upper_block_content == 'presentations'}
        
        {template name="last_three_presentations.tpl"}
        
        {else}
        
        {template name="last_three_press_releases.tpl"}
        
        {/if}
      </div>
      
      {* Second Box *}
      
      <div class="menu-box-bottom">
        
        {capture name="lower_block_content" assign="lower_block_content"}{field name="lower_block_content"}{/capture}
        
        {if $this.fields.lower_block_content == 'press_releases'}
        
        {template name="last_three_press_releases.tpl"}
        
        {elseif $this.fields.lower_block_content == 'custom_search'}
        
        {template name="custom_search.tpl"}
        
        {elseif $this.fields.lower_block_content == 'presentations'}
        
        {template name="last_three_presentations.tpl"}
        
        {else}
        
        {template name="savannah_custom_search.tpl"}
        
        {/if}
      </div>
    </td>
    
    <td id="central-column" valign="top" onmouseover="hideAllMenus()">
      <div id="central-column-content">
        
        {if $this.fields.category != 'home'}
          <h3>{$this.page.title}</h3>
          <div id="breadcrumbs">You are in: {breadcrumbs}</div>
        {/if}
        
        {container name="central_column_dynamic_content"}
        
        <div id="footer-container">
        
          <div id="footer">
          
            {link to="page:site-map" with="Site Map" class="footer-nav-link"} <span style="color:#000">|</span>
            {link to="page:contact-us" with="Contact Us" class="footer-nav-link"} <span style="color:#000">|</span>
            {link to="page:aim-compliance" with="AIM Compliance" class="footer-nav-link"} <span style="color:#000">|</span>
            {link to="page:terms-conditions" with="Terms &amp; Conditions" class="footer-nav-link"}
          
            <table id="clarity-parent" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td valign="middle">Savannah Diamonds is a</td>
                <td><a href="http://www.claritycapital.com/" target="_blank"><img src="{$domain}Resources/Images/clarity_logo_small.gif" alt="Clarity Capital" /></a></td>
                <td valign="middle">company.</td>
              </tr>
            </table>
          
            <div id="copyright">All Content Copyright &copy; Savannah Diamonds Limited 2007<br />
              Website by <a href="http://www.smoothmedia.com/" target="_blank">Smoothmedia</a> Inc.
            </div>
          
          </div>
        
        </div>
        
      </div>
    </td>
    
    <td id="right-column" valign="top">
      <div id="right-column-content">
        {placeholder name="right_col_top_image"}
        {placeholder name="right_col_bottom_image"}
        {placeholder name="right_col_extra_image"}
      </div>
    </td>
    
  </tr>
</table>