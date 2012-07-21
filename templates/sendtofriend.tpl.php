<p><a href="<?php $this->_($this->jobURI); ?>"><?php printf($this->_e('Back to %s'), $this->title); ?></a></p>

<h2 class="post-title"><?php $this->__('Send to Friend'); ?></h2>

<form method="post" action="<?php $this->_($this->formURI); ?>" name="sendToFriend" id="sendToFriend">
<input type="hidden" name="postback" value="yes"/>
<fieldset class="ccms-application">
    <legend><?php $this->_($this->title); ?></legend>
    <p>
        <label><?php $this->__('Your Email:'); ?></label><em>*</em>
        <input type="text" name="email" id="email" class="ccms-input ccms-large required email" value="<?php $this->_($this->email); ?>"/>
    </p>
    <p>
        <label><?php $this->__('Friend\'s Email:'); ?></label><em>*</em>
        <input type="text" name="emailto" id="emailto" class="ccms-input ccms-large required email" value=""/>
    </p>
    <p>
        <label><?php $this->__('Subject:'); ?></label><em>*</em>
        <input type="text" name="subject" id="subject" class="ccms-input ccms-large required" value="Job Opening: <?php $this->_($this->title); ?>"/>
    </p>
    <p>
        <label><?php $this->__('Message:'); ?></label><em><span>&#42;</span></em>
        <textarea name="message" id="message" class="ccms-input ccms-large"><?php $this->_('I came across this job on the web and I thought that you might be interested.'); ?></textarea>
        <div class="ccms-comment"><?php $this->_('The job description will be appended to your message.'); ?></div>
    </p>
    <p><input type="submit" value="<?php $this->__('Submit'); ?>" name="subject"/></p>
</fieldset>
</form>

<script type="text/javascript">
if (typeof jQuery == 'function') jQuery(document).ready(function()
{
    jQuery('#sendToFriend').validate();
    jQuery('#emailto').focus();
});
</script>
