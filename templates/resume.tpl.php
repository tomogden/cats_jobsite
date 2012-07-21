<h2 class="post-title"><?php $this->_($this->resume['name']); ?></h2>
<ul>
    <li><?php printf($this->_e('Created: %s'), $this->resume['created']); ?></li>
    <li><?php printf($this->_e('File Size: %s'), $this->resume['size']); ?></li>
</ul>

<form method="post" action="<?php $this->_($this->formURI); ?>" name="resumeForm" id="resumeForm" enctype="multipart/form-data">
<fieldset class="ccms-application">
    <legend><?php $this->__('Have a newer version?'); ?></legend>
    <p>
        <label><?php $this->__('Upload:'); ?></label><em>*</em>
        <input type="file" name="file" id="file"/>
    </p>
    <p>
        <input type="submit" value="<?php $this->__('Submit'); ?>" name="submit"/>
    </p>
</fieldset>
</form>

<div id="ccms-resume">
    <?php echo($this->content); ?>
</div>

<script type="text/javascript">
if (typeof jQuery == 'function') jQuery(document).ready(function()
{
    jQuery('#resumeForm').validate();
});
</script>
