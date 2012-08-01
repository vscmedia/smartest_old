  <h3>Create a New Page</h3>
  
  <div class="instruction-text">Step 3 of 3: Confirm the details of your new page</div>
  
  <form action="{$domain}{$section}/insertPage" method="post">
    
    <table cellspacing="1" cellpadding="2" border="0" style="width:100%;background-color:#ccc;margin-top:10px">
      <tr>
        <td style="width:150px;background-color:#fff" valign="top">Title:</td>
        <td style="background-color:#fff" valign="top">{$newPage.title}</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">URL:</td>
        <td style="background-color:#fff" valign="top">{$domain}{$new_page_url}</td>
      </tr>
      {if is_numeric($newPage.dataset_id) && $newPage.type == "ITEMCLASS"}
      <tr>
        <td style="background-color:#fff" valign="top">Represents model:</td>
        <td style="background-color:#fff" valign="top">{$new_page_model.plural_name}</td>
      </tr>
      {/if}
      {if is_numeric($newPage.dataset_id) && $newPage.type == "TAG"}
      <tr>
        <td style="background-color:#fff" valign="top">Retrieves tag:</td>
        <td style="background-color:#fff" valign="top">{$newPage.tag_label}</td>
      </tr>
      {/if}
      <tr>
        <td style="background-color:#fff" valign="top">Cache as HTML:</td>
        <td style="background-color:#fff" valign="top">{$newPage.cache_as_html}</td>
      </tr>
      {if $newPage.cache_as_html=='TRUE'}
      <tr>
        <td style="background-color:#fff" valign="top">Cache Interval:</td>
        <td style="background-color:#fff" valign="top">{$newPage.cache_interval}</td>
      </tr>
      {/if}
      <tr>
        <td style="background-color:#fff" valign="top">Layout Preset:</td>
        <td style="background-color:#fff" valign="top">{if $newPage.preset}{$newPage.preset_label}{else}<i>NONE</i>{/if}</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">Main Template:</td>
        <td style="background-color:#fff" valign="top">{$newPage.draft_template}</td>
      </tr>
  	{if $newPage.type == 'NORMAL'}
      <tr>
        <td style="background-color:#fff" valign="top">Description:</td>
        <td style="background-color:#fff" valign="top">{$newPage.description}</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">Search Keywords:</td>
        <td style="background-color:#fff" valign="top">{$newPage.search_field}</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">Meta Description:</td>
        <td style="background-color:#fff" valign="top">{$newPage.meta_description}</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">Meta Keywords:</td>
        <td style="background-color:#fff" valign="top">{$newPage.keywords}</td>
      </tr>
    {/if}
    </table>
    
    <p>After the page has been built, take me:
      <select name="destination">
        
        <option value="PREVIEW">To preview this page</option>
        <option value="ELEMENTS">To the elements tree for this page</option>
        <option value="SITEMAP">Back to the site map</option>
        <option value="EDIT">To edit this page</option>
        
      </select>
    </p>
  
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="button" value="&lt;&lt; Back" onclick="window.location=sm_domain+sm_section+'/addPage?stage=2'" />
        <input type="submit" value="Finish" />
      </div>
    </div>
    
  </form>