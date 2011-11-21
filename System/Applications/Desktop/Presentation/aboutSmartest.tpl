<div id="work-area">
  <h3>About Smartest</h3>
  
  {literal}<style type="text/css">p{margin:0 0 5px 0;}</style>{/literal}
  
  <div>
    <div style="float:left;width:106px;height:550px">
      <img src="{$domain}Resources/System/Images/info_logo.png" alt="Smartest Logo" />
    </div>

      <p><strong>Version</strong>: Smartest {$smartest_info.version} (Revision {$smartest_info.revision})</p>
      <p><strong>Build</strong>: {$smartest_info.build}</p>
      <p><strong>Installed</strong>: {if $system_installed_timestamp > 0}{$system_installed_timestamp|date_format:"%A %B %e, %Y, %l:%M%p"}{else}<em>Unknown</em>{/if}</p>
      <p><strong>PHP Version</strong>: {$php_version}</p>
      <p><strong>Available Memory</strong>: {if $memory_limit < 32}<span style="color:#e20">{/if}{$memory_limit} MB{if $memory_limit < 32} - Please allocate more memory to PHP</span>{/if}</p>
      {if $allow_see_server_speed}<p><strong>Server Speed</strong>: <!--<img src="{$domain}Resources/System/Images/{$speed_category_info.image}" alt="" />--> <span style="color:#{$speed_category_info.color}">{$speed_category_info.description}</span> <span style="color:#ccc">({$speed_score})</span> {if $allow_test_server_speed}<input type="button" value="Test..." onclick="window.location='{$domain}desktop/testServerSpeed'" />{/if}</p>{/if}
      <p><strong>Web Server</strong>: {$platform}</p>
      <p><strong>Root directory</strong>: <code>{$root_dir}</code></p>
      <p><strong>Install ID</strong>: <code>{$system_install_id}</code> {help id="desktop:install_ids" buttonize="true"}What's this?{/help}</p>
      <p><strong>Operating System</strong>: {$linux_version}</p>
      <p><strong>Is SVN checkout</strong>: {$is_svn_checkout.english}</p>
      <p style="margin-top:15px"><strong>Credits</strong>: Designed and developed by Marcus Gilroy-Ware. Originally devised by Marcus Gilroy-Ware and Eddie Tejeda. Many thanks to Chris Brauer, Eddie Tejeda, Alex Wood, Sereen Joseph, Nancy Arnold, Matt Asay, the wisdom of Professor Lawrence Lessig, PG, VW, a few brave MA Journalism students at City University London, and early adopters everywhere. Smartest exists because you believed in it.</p>
      <p>This is {help id="desktop:freesoftware"}Free &amp; Open Source Software{/help} and always will be.</p>
      <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" id="donate-form">
        <input type="hidden" name="cmd" value="_s-xclick" />
        <input type="hidden" name="hosted_button_id" value="WDZD87CC4N3JN" />
        <input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online." style="display:none" />
        <img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1" />
      </form>
      <p><strong>If you'd like to make a donation (via PayPal) and help to support Smartest</strong>, <a href="#donate" onclick="$('donate-form').submit();return false;">click here</a>.</p>
      <p><strong>OR - want to give Smartest's developers some props?</strong> just put <code>&lt;?sm:credit:?&gt;</code> in a template to make: <a href="http://sma.rte.st/?ref=scb"><img src="{$domain}Resources/System/Images/smartest_credit_button.png" /></a></p>
      <p style="margin-top:15px"><a href="http://www.vsclabs.com/" target="_blank"><img src="{$domain}Resources/System/Images/info_vsc_labs.png" alt="More great software from VSC Labs" /></a></p>
      <p style="margin-top:20px"><span style="font-size:10px;color:#999">Smartest is produced, published and marketed by VSC Creative Ltd., a UK company registered in England &amp; Wales with number 5746683, trading as "VSC Labs". "Smartest" and the Smartest logo are trademarks of and © VSC Creative Ltd 2006-{$now.Y}.</span></p>

  </div>
  
</div>

<div id="actions-area" style="text-align:center;padding-top:20px">
    
</div>