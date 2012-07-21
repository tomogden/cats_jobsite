<h2 class="post-title"><?php $this->__('Edit My Profile'); ?></h2>

<form method="post" action="<?php $this->_($this->postURI); ?>" name="profileForm" id="profileForm" enctype="multipart/form-data">
<fieldset class="ccms-application">
    <legend>Registered <?php $this->_($this->registered); ?></legend>
    <p>
        <label><?php $this->__('Email:'); ?></label><em>*</em>
        <input type="text" id="email" name="email" class="ccms-input ccms-large email" maxlength="255" value="<?php $this->_($this->email); ?>"/>
    </p>
    <p>
        <label><?php $this->__('New Password'); ?>:</label><em><span>&#42;</span></em>
        <input type="password" id="password1" name="password1" class="ccms-input ccms-small" maxlength="30"/>
        <div class="ccms-comment"><?php $this->__('Leave empty to keep your existing password.'); ?></div>
    </p>
    <p>
        <label><?php $this->__('Repeat:'); ?></label><em><span>&#42;</span></em>
        <input type="password" id="password2" name="password2" class="ccms-input ccms-small" maxlength="30"/>
    </p>
</fieldset>

<fieldset class="ccms-application">
    <p>
        <label><?php $this->__('Title:'); ?></label><em><span>&#42;</span></em>
        <input type="text" id="title" name="title" class="ccms-input ccms-large" maxlength="75" value="<?php $this->_($this->title); ?>"/>
        <div class="ccms-comment"><?php $this->__('Describe yourself in a sentence.'); ?></div>
    </p>
    <p>
        <label><?php $this->__('First Name:'); ?></label><em>*</em>
        <input type="text" id="first_name" name="first_name" class="ccms-input ccms-medium required" maxlength="40" value="<?php $this->_($this->firstName); ?>"/>
    </p>
    <p>
        <label><?php $this->__('Middle Name:'); ?></label><em><span>&#42;</span></em>
        <input type="text" id="middle_name" name="middle_name" class="ccms-input ccms-medium" maxlength="40" value="<?php $this->_($this->middleName); ?>"/>
    </p>
    <p>
        <label><?php $this->__('Last Name:'); ?></label><em>*</em>
        <input type="text" id="last_name" name="last_name" class="ccms-input ccms-medium required" maxlength="40" value="<?php $this->_($this->lastName); ?>"/>
    </p>
    <p>
        <label><?php $this->__('Address:'); ?></label><em><span>&#42;</span></em>
        <textarea id="address" name="address" class="ccms-input ccms-large"><?php $this->_($this->address); ?></textarea>
    </p>
    <p>
        <label><?php $this->__('City:'); ?></label><em><span>&#42;</span></em>
        <input type="text" id="city" name="city" class="ccms-input ccms-medium" maxlength="90" value="<?php $this->_($this->city); ?>"/>
    </p>
    <p>
        <label><?php $this->__('State:'); ?></label><em><span>&#42;</span></em>
        <input type="text" id="state" name="state" class="ccms-input ccms-medium" maxlength="60" value="<?php $this->_($this->state); ?>"/>
    </p>
    <p>
        <label><?php $this->__('Postcode:'); ?></label><em><span>&#42;</span></em>
        <input type="text" id="post_code" name="post_code" class="ccms-input ccms-small" maxlength="20" value="<?php $this->_($this->postCode); ?>"/>
    </p>
    <p>
        <label><?php $this->__('Phone (Home):'); ?></label><em><span>&#42;</span></em>
        <input type="text" id="phone_home" name="phone_home" class="ccms-input ccms-small" maxlength="90" value="<?php $this->_($this->phoneHome); ?>"/>
    </p>
    <p>
        <label><?php $this->__('Phone (Work):'); ?></label><em><span>&#42;</span></em>
        <input type="text" id="phone_work" name="phone_work" class="ccms-input ccms-small" maxlength="90" value="<?php $this->_($this->phoneWork); ?>"/>
    </p>
    <p>
        <label><?php $this->__('Phone (Cell):'); ?></label><em><span>&#42;</span></em>
        <input type="text" id="phone_cell" name="phone_cell" class="ccms-input ccms-small" maxlength="90" value="<?php $this->_($this->phoneCell); ?>"/>
    </p>
    <p>
        <label><?php $this->__('Website:'); ?></label><em><span>&#42;</span></em>
        <input type="text" id="website" name="website" class="ccms-input ccms-large" maxlength="90" value="<?php $this->_($this->website); ?>"/>
    </p>
    <?php if (!empty($this->resume)): ?>
        <p>
            <label><?php $this->__('Resume:'); ?></label><em><span>&#42;</span></em>
            <?php $this->_($this->resume['name']); ?> <a href="<?php $this->_($this->resumeURI); ?>" target="_blank">(view / edit)</a>
            <br /><br />
        </p>
    <?php else: ?>
        <p>
            <label><?php $this->__('Upload Resume:'); ?></label><em><span>&#42;</span></em>
            <input type="file" name="file" id="file"/>
        </p>
    <?php endif; ?>

    <p><input class="submit" type="submit" value="<?php $this->__('Submit'); ?>"/></p>
</fieldset>
</form>

<script type="text/javascript">
if (typeof jQuery == 'function') jQuery(document).ready(function()
{
    jQuery('#profileForm').validate();
});
</script>
