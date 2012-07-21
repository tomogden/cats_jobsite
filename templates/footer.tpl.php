<div class="ccms-footer">
    <?php
    /**
     * By using the CATS_JobSite distributed plug-in in whole or in part, we
     * ask that you provide attribution back to CATS.
     *
     * Attribution is satisfied either by displaying the official "Powered By"
     * image included in the package distribution or by displaying the
     * official text attribution in a clear and readable font. How you
     * display attribution can be configured in Wordpress -> Settings -> CATS JobSite.
     */
    ?>
    <?php if (CATS_Utility::getOption('attribution') == 2): ?>
    <a href="http://www.catsone.com/" target="_blank">
        <img src="<?php $this->_($this->poweredBy); ?>" alt="Powered by CATS"/>
    </a>
    <?php elseif (CATS_Utility::getOption('attribution') == 1): ?>
        Powered by <a href="http://www.catsone.com/" target="_blank">CATS</a>
    <?php endif; ?>
</div>

