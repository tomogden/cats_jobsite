<h2 class="post-title"><?php $this->__('Edit My Profile'); ?></h2>

<fieldset class="ccms-application">
    <legend><?php printf($this->_e('Registered %s'), $this->registered); ?></legend>
    <p>
        <label><?php $this->__('Email:'); ?></label>
        <?php $this->_($this->email); ?>
    </p>
    <p>
        <label><?php $this->__('Title:'); ?></label>
        <?php $this->_($this->title); ?>
    </p>
    <p>
        <label><?php $this->__('First Name:'); ?></label>
        <?php $this->_($this->firstName); ?>
    </p>
    <p>
        <label><?php $this->__('Middle Name:'); ?></label>
        <?php $this->_($this->middleName); ?>
    </p>
    <p>
        <label><?php $this->__('Last Name:'); ?></label>
        <?php $this->_($this->lastName); ?>
    </p>
    <p>
        <label><?php $this->__('Address:'); ?></label>
        <?php $this->_($this->address); ?>
    </p>
    <p>
        <label><?php $this->__('City:'); ?></label>
        <?php $this->_($this->city); ?>
    </p>
    <p>
        <label><?php $this->__('State:'); ?></label>
        <?php $this->_($this->state); ?>
    </p>
    <p>
        <label><?php $this->__('Postcode:'); ?></label>
        <?php $this->_($this->postCode); ?>
    </p>
    <p>
        <label><?php $this->__('Phone (Home):'); ?></label>
        <?php $this->_($this->phoneHome); ?>
    </p>
    <p>
        <label><?php $this->__('Phone (Work):'); ?></label>
        <?php $this->_($this->phoneWork); ?>
    </p>
    <p>
        <label><?php $this->__('Phone (Cell):'); ?></label>
        <?php $this->_($this->phoneCell); ?>
    </p>
    <p>
        <label><?php $this->__('Website:'); ?></label>
        <?php $this->_($this->website); ?>
    </p>
    <?php if (!empty($this->resume)): ?>
        <p>
            <label><?php $this->__('Resume:'); ?></label>
            <?php $this->_($this->resume['name']); ?> <a href="<?php $this->_($this->resumeURI); ?>" target="_blank">(<?php $this->__('view / edit'); ?>)</a>
            <br /><br />
        </p>
    <?php endif; ?>

    <p>
        <a href="<?php $this->_($this->editProfileURI); ?>" style="text-decoration: none;">
            <input type="button" value="<?php $this->__('Edit Profile'); ?>"/>
        </a>
    </p>
</fieldset>

<?php if (!empty($this->applications)): ?>
<fieldset>
    <legend><?php $this->__('Jobs I\'ve Applied To:'); ?></legend>
    <ul>
    <?php foreach ($this->applications as $id => $app): ?>
        <li><?php printf($this->_e('<a href="%s">%s</a> on %s'), $app['link'], $app['title'], $app['when']); ?></li>
    <?php endforeach; ?>
    </ul>
</fieldset>
<?php endif; ?>
