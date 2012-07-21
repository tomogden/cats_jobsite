<h2 class="post-title"><?php if (isset($this->none)): ?><?php $this->__('No Registration Found'); ?><?php else: ?><?php $this->__('Password Sent'); ?><?php endif; ?></h2>
<?php if (isset($this->none)): ?>
    <p><?php printf($this->_e('The email address %s has not been registered yet. You can <a href="%s">register here</a> (it only takes a minute).'), $this->email, $this->registerURI); ?></p>
<?php else: ?>
    <p><?php printf($this->_e('Your password has been emailed to %s. If you haven\'t received an email after a few minutes, you may want to check your spam or junk folder.'), $this->email); ?></p>
<?php endif; ?>
