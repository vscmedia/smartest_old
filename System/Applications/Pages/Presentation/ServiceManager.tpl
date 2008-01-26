<table>
  <tr>
    <td>Service</td>
    <td>URL</td>
    <td>Status</td>
  </tr>

  {foreach from=$content key=key item=page}  
  <tr>
    <td>{$page.name}</td>
    <td>{$page.class}</td>
    <td>{$page.enabled}</td>
  </tr>
  {/foreach}
</table>

<!--
Service       URL               Status
---------------------------------------
About         about             enabled
            
Contact       contact           enabled
              contact/store     disabled
            
Shop          shop              enabled
              shop/clothing     enabled
              shop/electronics  enabled
              shop/furniture    disabled
              shop/music        enabled 
              shop/movies       disabled

Administrator admintrator       restricted      
-->