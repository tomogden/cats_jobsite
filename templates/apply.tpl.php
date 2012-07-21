<form method="post" action="<?php $this->_($this->postURL); ?>" name="applyForm" id="applyForm" enctype="multipart/form-data">
<input type="hidden" name="postback" value="yes"/>
<fieldset class="ccms-application">
    <legend><?php printf($this->_e('Applying to %s'), $this->job['title']); ?></legend>
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

    <p><input class="submit" type="submit" value="<?php $this->__('Submit'); ?>"/></p>
</fieldset>
</form>

<script type="text/javascript">
if (typeof jQuery == 'function') jQuery(document).ready(function()
{
    jQuery('#applyForm').validate();
});
</script>
