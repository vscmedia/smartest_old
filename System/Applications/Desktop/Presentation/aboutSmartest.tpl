<div id="work-area">
  <h3>About Smartest</h3>
  
  {literal}<style type="text/css">p{margin:0 0 5px 0;}</style>{/literal}
  
  <div>
    <div style="float:left;width:100px;height:250px">
      <img src="{$domain}Resources/System/Images/info_logo.png" alt="Smartest Logo" />
    </div>
    <p><strong>Version</strong>: {$version} (Revision {$revision})</p>
    <p><strong>Installed</strong>: {if $system_installed_timestamp > 0}{$system_installed_timestamp|date_format:"%A %B %e, %Y, %l:%M%p"}{else}<em>Unknown</em>{/if}</p>
    <p><strong>Platform</strong>: {$platform}</p>
    <p><strong>Server Speed</strong>: {$speed_score} <input type="button" value="Test..." onclick="window.location='{$domain}desktop/testServerSpeed'" /></p>
    <p><strong>Credit</strong>: Developed and written by Marcus Gilroy-Ware. Originally conceived by Marcus Gilroy-Ware and Eddie Tejeda. Thanks to Eddie Tejeda, Chris Brauer, Sereen Joseph, Nancy Arnold, Paul Gilroy, Vron Ware, a few brave MA Journalism students at City University London, and early adopters everywhere.</p>
  </div>
  
</div>