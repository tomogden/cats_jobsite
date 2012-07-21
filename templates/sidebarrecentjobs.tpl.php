<fieldset class="ccms-recent-jobs">
    <legend><?php $this->__('Jobs Recently Viewed'); ?></legend>
    <ul>
    <?php foreach ($this->jobs as $job): ?>
        <li><a href="<?php $this->_($job['link']); ?>"><?php $this->_($job['title']); ?></a></li>
    <?php endforeach; ?>
    </ul>
</fieldset>

