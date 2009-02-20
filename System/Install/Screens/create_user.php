<p>Step 3 of 4: Who are you? <span class="hint">(* = required field)</span></p>

<?php if($stage->hasParameter('errors') && $stage->getParameter('errors')->hasData()): ?>
<ul class="errors-list">
    <?php foreach($stage->getParameter('errors')->getParameters() as $error): ?>
    <li><?php echo $error ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<form action="" method="post" id="installerForm">
    
    <input type="hidden" name="execute" value="1" />
    <input type="hidden" name="action" value="createUser" />
    
    <div class="form-section-label">Login Details</div>
    
    <div class="hint" style="padding-bottom:10px">These are the details you will use to log in when you work on your website, so choose carefully!</div>
    
    <div class="form-row">
        <div class="form-row-label">Username *</div>
        <input type="text" name="smartest_username" value="admin" />
    </div>
    
    <div class="form-row">
        <div class="form-row-label">Password *</div>
        <input type="password" name="smartest_password" />
    </div>
    
    <div class="form-row">
        <div class="form-row-label">Re-type password *</div>
        <input type="password" name="smartest_password_2" />
    </div>
    
    <div class="form-section-label">Personal Details</div>
    
    <div class="hint" style="padding-bottom:10px">These details help Smartest to be a bit more friendly and personalized.</div>
    
    <div class="form-row">
        <div class="form-row-label">First name *</div>
        <input type="text" name="smartest_firstname" />
    </div>
    
    <div class="form-row">
        <div class="form-row-label">Last name</div>
        <input type="text" name="smartest_lastname" />
    </div>
    
    <div class="form-row">
        <div class="form-row-label">E-mail address *</div>
        <input type="text" name="smartest_email" />
    </div>
    
    <div class="button normal-button"><a href="javascript:document.getElementById('installerForm').submit();">Next</a></div>

</form>