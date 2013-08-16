<script language="javascript">

// var acceptable_suffixes = {$suffixes};
// var input_mode = '{$starting_mode}';
// var show_params_holder = false;
var SiteNameFieldDefaultValue = 'My Smartest Web Site';
var SiteDomainFieldDefaultValue = 'example.com'
var preventDefaultValue = true;

{literal}

document.observe('dom:loaded', function(){
    
    $('new-site-name').observe('focus', function(){
        if(($('new-site-name').getValue() == SiteNameFieldDefaultValue)|| $('new-site-name').getValue() == ''){
            $('new-site-name').removeClassName('unfilled');
            $('new-site-name').setValue('');
        }
    });
    
    $('new-site-name').observe('blur', function(){
        if(($('new-site-name').getValue() == SiteNameFieldDefaultValue) || $('new-site-name').getValue() == ''){
            $('new-site-name').addClassName('unfilled');
            $('new-site-name').setValue(SiteNameFieldDefaultValue);
        }else{
            $('new-site-name').removeClassName('error');
        }
    });
    
    $('new-site-domain').observe('focus', function(){
        if(($('new-site-domain').getValue() == SiteDomainFieldDefaultValue)|| $('new-site-domain').getValue() == ''){
            $('new-site-domain').removeClassName('unfilled');
            $('new-site-domain').setValue('');
        }
    });
    
    $('new-site-domain').observe('blur', function(){
        if(($('new-site-domain').getValue() == SiteDomainFieldDefaultValue) || $('new-site-domain').getValue() == ''){
            $('new-site-domain').addClassName('unfilled');
            $('new-site-domain').setValue(SiteDomainFieldDefaultValue);
        }else{
            $('new-site-domain').removeClassName('error');
        }
    });
    
    $('new-site-form').observe('submit', function(e){
        
        if(($('new-site-name').getValue() == SiteNameFieldDefaultValue) || $('new-site-name').getValue() == ''){
            $('new-site-name').addClassName('error');
            e.stop();
        }
        
        if(($('new-site-domain').getValue() == SiteDomainFieldDefaultValue) || $('new-site-domain').getValue() == ''){
            $('new-site-domain').addClassName('error');
            e.stop();
        }
        
        if($('site-admin-email').getValue() == ''){
            $('site-admin-email').addClassName('error');
            e.stop();
        }
        
    });
    
});

{/literal}
</script>

<div id="work-area">

<h3 id="siteName">Create a New Site</h3>

{foreach from=$errors item="error"}
<div class="error">{$error}</div>
{/foreach}

<form id="new-site-form" name="buildSite" action="{$domain}{$section}/buildSite" method="post" style="margin:0px" enctype="multipart/form-data">

<div id="edit-form-layout">
  
<input type="hidden" name="MAX_FILE_SIZE" value="2097152" />

<div class="edit-form-row">
  <div class="form-section-label">Site Title</div>
  <input type="text" name="site_name" class="unfilled" id="new-site-name" value="My Smartest Web Site"/>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Host name</div>
  <input type="text" name="site_domain" class="unfilled" id="new-site-domain" value="example.com" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Logo</div>
  <input type="file" name="site_logo" /><div class="form-hint">Optional: Pick an image to represent this site when you first log in.</div>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Admin email</div>
  <input type="text" name="site_admin_email" value="{$user.email}" id="site-admin-email" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Master template</div>
  <select name="site_master_template">
    <option value="_BLANK"{if !$allow_create_master_tpl} disabled="disabled"{/if}>Create a new, blank template{if !$allow_create_master_tpl} (directory is not writable){/if}</option>
    <option value="_DEFAULT">None for now, I will create one later</option>
    {foreach from=$templates item="template"}
    <option value="{$template.url}">Use {$template.url}</option>
    {/foreach}
  </select>
</div>

<div class="buttons-bar">
  <input type="button" value="Cancel" onclick="window.location='{$domain}smartest'" />
  <input type="submit" name="action" value="Save Changes" />
</div>

</div>
 
</div>

<div id="actions-area">

</div>

</form>