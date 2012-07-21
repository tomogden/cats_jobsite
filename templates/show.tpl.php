<p><a href="<?php $this->_($this->jobsURI); ?>"><?php $this->__('Back to Jobs'); ?></a></p>

<h2 class="post-title"><?php $this->_($this->job['title']); ?></h2>

<ul>
    <li><?php printf($this->_e('Location: %s'), $this->job['location']); ?></li>
    <li><?php printf($this->_e('Salary: %s'), $this->job['salary']); ?></li>
    <li><?php printf($this->_e('Posted: %s'), $this->job['posted']); ?></li>
</ul>

<p><?php echo($this->job['description']); ?></p>
<br class="ccms-clear"/>

<form class="ccms-button-container" action="<?php $this->_($this->linkApply); ?>" method="post">
    <input type="submit" value="<?php $this->__('Apply to Job'); ?>"/>
</form>

<form class="ccms-button-container" action="<?php $this->_($this->linkSendToFriend); ?>" method="post">
    <input type="submit" value="<?php $this->__('Send to Friend'); ?>"/>
</form>

<br class="ccms-clear"/>

