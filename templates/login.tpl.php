<h2 class="post-title"><?php $this->__('Login / Returning Applicants'); ?></h2>

<form method="post" action="<?php $this->_($this->formURI); ?>" name="sidebarLoginForm" id="sidebarLoginForm">
<input type="hidden" name="postback" value="yes"/>
<fieldset class="ccms-sidebar-login">
    <legend><?php $this->__('Login'); ?></legend>

    <p><?php $this->__('Have you registered with us before? Login to review your applications:'); ?></p>

    <p>
        <label><?php $this->__('Email:'); ?></label>
        <em>*</em>
        <input id="loginEmail" name="loginEmail" size="10" class="required email ccms-input ccms-large" type="text" minlength="2" value="<?php $this->_($this->email); ?>"/>
    </p>
    <p>
        <label><?php $this->__('Password:'); ?></label>
        <em>*</em>
        <input id="loginPassword" name="loginPassword" size="10" class="required ccms-input ccms-small" type="password" minlength="2"/>
    </p>

    <p>
        <input type="submit" value="<?php $this->__('Submit'); ?>" id="ccmsSidebarLogin"/>
        &nbsp; &nbsp; <a href="<?php $this->_($this->forgotURI); ?>"><?php $this->__('Forgot password?'); ?></a>
    </p>

    <p>
        <?php $this->__('Haven\'t registered yet? It only takes a minute'); ?>,
        <?php printf($this->_e('<a href="%s">click here</a>.'), $this->registerURI); ?>
    </p>
</fieldset>
</form>

<script type="text/javascript">
if (typeof jQuery == 'function') jQuery(document).ready(function()
{
    jQuery('#sidebarLoginForm').validate();
});
</script>
