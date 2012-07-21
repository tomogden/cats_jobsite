<fieldset class="ccms-profile">
    <legend><?php $this->__('My Profile'); ?></legend>

    <table class="ccmsTable">
    <tbody>
        <tr>
            <td><label><?php $this->__('Name:'); ?></label></td>
            <td class="value"><?php $this->_($this->name); ?></td>
        </tr>
        <tr>
            <td><label><?php $this->__('Address:'); ?></label></td>
            <td class="value">
                <?php $this->_($this->address); ?>
                <?php if (!empty($this->city) && !empty($this->state) && !empty($this->postCode)): ?>
                    <?php $this->_($this->city); ?>, <?php $this->_($this->state); ?> <?php $this->_($this->postCode); ?>
                <?php elseif (!empty($this->city) && !empty($this->state)): ?>
                    <?php $this->_($this->city); ?>, <?php $this->_($this->state); ?>
                <?php elseif (!empty($this->city)): ?>
                    <?php $this->_($this->city); ?>
                <?php elseif (!empty($this->state)): ?>
                    <?php $this->_($this->state); ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td><label><?php $this->__('Email:'); ?></label></td>
            <td class="value"><?php $this->_($this->email); ?></td>
        </tr>
        <tr>
            <td><label><?php $this->__('Registered:'); ?></label></td>
            <td class="value"><?php $this->_($this->registered); ?></td>
        </tr>
    </tbody>
    </table>

    <p><a href="<?php $this->_($this->profileURI); ?>"><?php $this->__('Edit Profile'); ?></a></p>

    <?php if (!empty($this->applications)): ?>
        <p><strong><?php $this->__('Jobs I\'ve Applied To:'); ?></strong></p>
        <ul>
        <?php foreach ($this->applications as $id => $app): ?>
            <li><?php printf($this->_e('<a href="%s">%s</a> on %s'), $app['link'], $app['title'], $app['when']); ?></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (!empty($this->resume)): ?>
        <p><strong><?php $this->__('My Resume'); ?></strong></p>
        <ul>
            <li>
                <a href="<?php $this->_($this->resumeURI); ?>"><?php $this->_($this->resume['name']); ?></a>
                <span class="ccms-size">(<?php $this->_($this->resume['size']); ?>)</span>
            </li>
        </ul>
    <?php endif; ?>

    <p><a href="<?php $this->_($this->logoutURI); ?>"><?php $this->__('Log Out'); ?></a></p>
</fieldset>

