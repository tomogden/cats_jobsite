<h2 class="post-title"><?php $this->__('One moment please...'); ?></h2>
<p>
    <?php printf($this->_e('Your request is being processed. You should automatically be redirected to a new page in a few seconds. If you aren\'t redirected or don\'t want to wait, <a href="%s">click here</a>.'), $this->uri); ?>
</p>

<script type="text/javascript">
setTimeout(function()
{
    document.location.href = '<?php echo($this->uri); ?>';
}, 1500);
</script>
