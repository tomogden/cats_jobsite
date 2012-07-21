<div>
    <h2><?php $this->__('CATS JobSite Plugin'); ?></h2>

    <?php $this->__('Adds functionality to your Wordpress site by adding widgets and additional pages generated from CATS through its API. For additional configuration, login to CATS and click on the Website tab.'); ?>

    <form action="options.php" method="post">
    <?php settings_fields('cats_cms_options'); ?>
    <?php do_settings_sections('cats_cms_plugin'); ?>

    <input name="Submit" type="submit" value="<?php $this->__('Save Changes'); ?>"/>
    </form>
</div>
