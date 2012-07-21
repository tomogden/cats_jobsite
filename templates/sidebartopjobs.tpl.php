<fieldset class="ccms-topjobs">
    <legend><?php $this->__('Top Jobs'); ?></legend>
    <ul>
    <?php foreach ($this->jobs as $job): ?>
        <li>
            <span class="ccms-posted"><?php $this->_($job['posted']); ?></span> -
            <a href="<?php $this->_($job['link']); ?>"><?php $this->_($job['title']); ?></a> -
            <span class="ccms-excerpt"><?php $this->_($job['excerpt']); ?></span>
        </li>
    <?php endforeach; ?>
    </ul>
    <a href="<?php $this->_($this->jobsURI); ?>"><?php $this->__('See all Jobs'); ?></a>
</fieldset>

