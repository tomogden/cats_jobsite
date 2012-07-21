<h2 class="post-title"><?php $this->__('Email Sent'); ?></h2>
<p><?php printf($this->_e('We\'ve sent an email to %s with the job\'s description and your message. It should be arriving shortly. If your friend doesn\'t receive the message, it may be in their spam box (depending on their settings).'), $this->emailto); ?></p>

<p><?php printf($this->_e('View a list of open jobs <a href="%s">here</a>.'), $this->jobsURI); ?></p>
