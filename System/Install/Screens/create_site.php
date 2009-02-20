<p>Step 4 of 4: Set up your site <span class="hint">(all fields are required)</span></p>

<?php if($stage->hasParameter('errors') && $stage->getParameter('errors')->hasData()): ?>
<ul class="errors-list">
    <?php foreach($stage->getParameter('errors')->getParameters() as $error): ?>
    <li><?php echo $error ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<?php

$master_tpls = SmartestFileSystemHelper::getDirectoryContents(SM_ROOT_DIR.'Presentation/Masters/');

?>

<form action="" method="post" id="installerForm">
    
    <input type="hidden" name="execute" value="1" />
    <input type="hidden" name="action" value="createSite" />
    
    <div class="hint" style="padding-bottom:10px">Finally, input some basic details about the website you are creating. You can easily change these later if you change your mind.</div>
    
    <div class="form-row">
        <div class="form-row-label">Name of your site</div>
        <input type="text" name="site_name" />
    </div>
    
    <div class="form-row">
        <div class="form-row-label">Hostname of your site</div>
        <input type="text" name="site_host" value="<?php echo $_SERVER['HTTP_HOST']; ?>" style="width:240px" />
    </div>
    
    <?php if(count($master_tpls)): ?>
    <div class="form-row">
        <div class="form-row-label">Master template to start with</div>
        <select name="site_initial_tpl">
        <?php foreach($master_tpls as $tpl): ?>
            <option value="<?php echo $tpl; ?>"><?php echo $tpl; ?></option>
        <?php endforeach;?>
            <option value="_DEFAULT">None for now</option>
        </select>
    </div>
    <?php else: ?>
    <input type="hidden" name="site_initial_tpl" value="_DEFAULT" />
    <?php endif; ?>
    
    <div class="button normal-button"><a href="javascript:document.getElementById('installerForm').submit();">Finish &amp; Log In</a></div>

</form>