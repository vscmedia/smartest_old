<div id="work-area">
  <h3>About Smartest</h3>
  
  {literal}<style type="text/css">p{margin:0 0 5px 0;}</style>{/literal}
  
  <div>
    <div style="float:left;width:106px;height:500px">
      <img src="{$domain}Resources/System/Images/info_logo.png" alt="Smartest Logo" />
    </div>
    <p><strong>Version</strong>: Smartest {$version} (Revision {$revision})</p>
    <p><strong>Build</strong>: {$build}</p>
    <p><strong>Installed</strong>: {if $system_installed_timestamp > 0}{$system_installed_timestamp|date_format:"%A %B %e, %Y, %l:%M%p"}{else}<em>Unknown</em>{/if}</p>
    <p><strong>PHP Version</strong>: {$php_version}</p>
    <p><strong>Available Memory</strong>: {$memory_limit}</p>
    {if $allow_see_server_speed}<p><strong>Server Speed</strong>: <!--<img src="{$domain}Resources/System/Images/{$speed_category_info.image}" alt="" />--> <span style="color:#{$speed_category_info.color}">{$speed_category_info.description}</span> <span style="color:#ccc">({$speed_score})</span> {if $allow_test_server_speed}<input type="button" value="Test..." onclick="window.location='{$domain}desktop/testServerSpeed'" />{/if}</p>{/if}
    <p><strong>Web Server</strong>: {$platform}</p>
    <p><strong>Root directory</strong>: <code>{$root_dir}</code></p>
    <p><strong>Operating System</strong>: {$linux_version}</p>
    <p><strong>Is SVN checkout</strong>: {$is_svn_checkout.english}</p>
    <p><strong>Credit</strong>: Designed and developed by Marcus Gilroy-Ware. Originally devised by Marcus Gilroy-Ware and Eddie Tejeda. Many thanks to Chris Brauer, Eddie Tejeda, Sereen Joseph, Nancy Arnold, Paul Gilroy, Vron Ware, a few brave MA Journalism students at City University London, and early adopters everywhere.</p>
    <p style="margin-top:15px"><strong>Want to give Smartest's developers some credit?</strong> just put <code>&lt;?sm:sm:?&gt;</code> in a template to make: <a href="http://sma.rte.st"><img src="{$domain}Resources/System/Images/smartest_credit_button.png" /></a></p>
    <p style="margin-top:15px"><a href="http://www.vsclabs.com/" target="_blank"><img src="{$domain}Resources/System/Images/info_vsc_labs.png" alt="More great software from VSC Labs" /></a></p>
    <p style="margin-top:20px"><span style="font-size:10px;color:#999">"Smartest" and the Smartest logo are trademarks of and © VSC Creative Ltd 2006-{$smarty.now|date_format:"%Y"}.</span></p>
  </div>
  
</div>

<div id="actions-area" style="text-align:center;padding-top:20px">
    
</div>