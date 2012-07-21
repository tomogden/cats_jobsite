<form method="post" action="<?php $this->_($this->postURL); ?>" name="registerForm" id="registerForm" enctype="multipart/form-data">
<input type="hidden" name="postback" value="yes"/>
<fieldset class="ccms-application">
    <legend><?php $this->__('Create Account'); ?></legend>
    <p>
        <label><?php $this->__('Email:'); ?></label><em>*</em>
        <input type="text" name="email" id="email" class="ccms-input ccms-medium required email" value=""/>
    </p>

    <p>
        <label><?php $this->__('Password:'); ?></label><em>*</em>
        <input type="password" name="password1" id="password1" class="ccms-input ccms-medium required" value=""/>
    </p>

    <p>
        <label><?php $this->__('Repeat:'); ?></label><em>*</em>
        <input type="password" name="password2" id="password2" class="ccms-input ccms-medium required" value=""/>
    </p>
</fieldset>

<fieldset class="ccms-application">
    <legend><?php $this->__('General'); ?></legend>
    <?php foreach ($this->application as $application): ?>
        <?php if (!empty($application['header'])): ?>
            <p><?php echo($application['header']); ?></p>
        <?php endif; ?>
        <?php foreach ($application['questions'] as $question): ?>
            <p>
                <label><?php $this->_($question['title']); ?>:</label>
                <?php if ($question['required']): ?><em>*</em><?php else: ?><em><span>&#42;</span></em><?php endif; ?>
                <?php echo($question['form']); ?>
                <?php if (!empty($question['comment'])): ?>
                    <div class="ccms-comment">
                        <?php $this->_($question['comment']); ?>
                    </div>
                <?php endif; ?>
            </p>
        <?php endforeach; ?>
    <?php endforeach; ?>

    <p>
    <input class="submit" type="submit" value="<?php $this->__('Submit'); ?>"/>
</fieldset>
</form>

<script type="text/javascript">
if (typeof jQuery == 'function') jQuery(document).ready(function()
{
    jQuery('#registerForm').validate();
});
</script>
