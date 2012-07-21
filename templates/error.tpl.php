<h2 class="post-title"><?php $this->_($this->friendlyTitle); ?></h2>
<p><?php echo($this->friendlyDescription); ?></p>
<?php if ($this->isAdmin): ?>
    <p><span style="color: #800000; font-weight: bold;">
        <?php $this->__('Debug Information (visible by administrators only)'); ?>
    </span></p>
    <p><pre><?php $this->_($this->extra); ?></pre></p>
    <p>
        <?php printf($this->_e('Error on line %d in %s.'), $this->line, $this->file); ?>
    </p>
    <p><strong><?php $this->__('Backtrace:'); ?></strong><br /><pre><?php $this->_($this->traceString); ?></pre></p>
<?php endif; ?>

