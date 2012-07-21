<form method="post" action="<?php $this->_($this->formURI); ?>" name="sidebarLoginForm" id="sidebarLoginForm">
<input type="hidden" name="postback" value="yes"/>
<fieldset class="ccms-sidebar-login">
    <legend><?php $this->__('Login'); ?></legend>

    <p><?php $this->__('Have you registered with us before? Login to review your applications:'); ?></p>

    <p>
        <label><?php $this->__('Email:'); ?></label>
        <em>*</em>
        <input id="loginEmail" name="loginEmail" size="10" class="required email ccms-input ccms-small" type="text" value="<?php $this->_($this->email); ?>"/>
    </p>
    <p>
        <label><?php $this->__('Password:'); ?></label>
        <em>*</em>
        <input id="loginPassword" name="loginPassword" size="10" class="required ccms-input ccms-small" type="password"/>
    </p>

    <p>
        <input type="submit" value="<?php $this->__('Submit'); ?>" id="ccmsSidebarLogin"/>
        &nbsp; &nbsp; <a href="<?php $this->_($this->forgotURI); ?>"><?php $this->__('Forgot password?'); ?></a>
    </p>
</fieldset>
</form>

<script type="text/javascript">
if (typeof jQuery == 'function') jQuery(document).ready(function()
{
    jQuery('#sidebarLoginForm').validate();
});
</script>

