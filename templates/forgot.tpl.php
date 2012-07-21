<h2 class="post-title">Forgot Password</h2>

<form method="post" action="<?php $this->_($this->formURI); ?>" name="forgotForm" id="forgotForm">
<fieldset class="ccms-sidebar-login">
    <p>
        <label><?php $this->__('Email:'); ?></label>
        <em>*</em>
        <input id="loginEmail" name="loginEmail" size="10" class="required email ccms-input ccms-large" type="text" minlength="2" value="<?php $this->_($this->email); ?>"/>
    </p>

    <p>
        <input type="submit" value="<?php $this->__('Submit'); ?>" id="ccmsSidebarLogin"/>
    </p>

    <p>
        <?php $this->__('Haven\'t registered yet? It only takes a minute'); ?>, <a href="<?php $this->_($this->registerURI); ?>"><?php $this->__('click here'); ?></a>.
    </p>
</fieldset>
</form>

<script type="text/javascript">
if (typeof jQuery == 'function') jQuery(document).ready(function()
{
    jQuery('#forgotForm').validate();
});
</script>
