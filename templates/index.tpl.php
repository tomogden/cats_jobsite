<?php if (!empty($this->content)): ?>
    <p><?php echo($this->content); ?></p>
<?php endif; ?>

<?php if (isset($this->isLoggedIn)): ?>
    <?php printf($this->_e('You are logged in as <strong>%s.</strong>.'), $this->name); ?>
    <?php printf($this->_e('Not %s?'), $this->firstName); ?>
    <a href="<?php $this->_($this->logoutURI); ?>"><?php $this->__('Log out'); ?></a>
<?php endif; ?>

<ul id="ccms-navigation">
    <?php if (!isset($this->isLoggedIn)): ?>
        <li><a href="<?php $this->_($this->loginURI); ?>"><?php $this->__('Login'); ?></a></li>
        <li><a href="<?php $this->_($this->registerURI); ?>"><?php $this->__('Register'); ?></a> <?php $this->__('(optional)'); ?></li>
    <?php else: ?>
        <li><a href="<?php $this->_($this->profileURI); ?>"><?php $this->__('My Profile'); ?></a></li>
        <li><a href="<?php $this->_($this->resumeURI); ?>"><?php $this->__('My Resume'); ?></a></li>
    <?php endif; ?>
    <li>
        <a href="<?php $this->_($this->rssURL); ?>" class="ccmsRSS">
            <img src="<?php $this->_($this->rssIcon); ?>" alt="<?php $this->__('RSS Feed'); ?>"/> <?php $this->__('Jobs RSS Feed'); ?>
        </a>
    </li>
</ul>
<br class="ccms-clear"/>

<form method="post" action="<?php $this->_($this->searchURI); ?>" name="ccmsFilter" id="ccmsFilter">
<fieldset class="ccms-filters">
    <p>
        <label><?php $this->__('Search:'); ?></label>
        <input type="text" name="ccmsSearch" id="ccmsSearch" class="ccms-input ccms-medium" value="<?php $this->_($this->search); ?>"/>
    </p>
    <p>
        <label><?php $this->__('Sort:'); ?></label>
        <select name="ccmsSort" class="ccms-input ccms-medium">
            <?php $first = true; ?>
            <?php foreach ($this->columns as $column): ?>
                <?php if ($column['id'] == 'description' || $column['id'] == 'job_order_id'): continue; endif; ?>

                <?php if (!$first): ?><option value="">--</option><?php endif; ?>
                <option value="<?php $this->_($column['id']); ?>_asc"<?php if ($this->sortColumn == $column['id'] && $this->sortDir == 'asc'): ?> selected<?php endif; ?>>
                    <?php $this->_($column['title']); ?> (Ascending)
                </option>
                <option value="<?php $this->_($column['id']); ?>_desc"<?php if ($this->sortColumn == $column['id'] && $this->sortDir == 'desc'): ?> selected<?php endif; ?>>
                    <?php $this->_($column['title']); ?> (Descending)
                </option>
                <?php $first = false; ?>
            <?php endforeach; ?>
        </select>
    </p>

    <div class="ccms-action">
        <input type="submit" value="Submit" id="ccms-filters-button"/>
        <?php if (!empty($this->search)):  ?>
            &nbsp;&nbsp;&nbsp;<a href="<?php $this->_(CATS_Utility::uri('cc=index&ccmsSearch=+')); ?>"><?php $this->__('Reset Search / Show All Jobs'); ?></a>
        <?php endif; ?>
    </div>
</fieldset>
</form>

<?php if (empty($this->jobs)): ?>
    <?php if (!empty($this->search)): ?>
        <p><?php $this->__('Search matched no jobs.'); ?></p>
    <?php else: ?>
        <p><?php $this->__('There are no jobs available at this time. Please bookmark us and check back later.'); ?></p>
    <?php endif; ?>

<?php else: ?>

    <?php if ($this->numPages > 1): ?>
        <p class="ccmsTotals">
            <?php printf($this->_e('Showing %s of <strong>%s</strong> jobs (page %s of %s'),
                number_format(count($this->jobs), 0),
                number_format($this->numJobs, 0),
                $this->page,
                number_format($this->numPages, 0)
            ); ?>
        </p>
    <?php endif; ?>

    <table class="ccms-listing">
    <thead>
        <tr>
        <?php foreach ($this->columns as $column): ?>
            <?php if ($column['id'] == 'description'): $hasDescription = true; continue; endif; ?>
            <?php if ($column['id'] == 'job_order_id'): continue; endif; ?>
            <th><?php $this->_($column['title']); ?></th>
        <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->jobs as $index => $job): ?>
            <tr class="<?php if ($index % 2): echo('ccmsOdd'); else: echo('ccmsEven'); endif; ?>">
                <?php foreach ($this->columns as $column): ?>
                    <?php if ($column['id'] == 'description'): continue; endif; ?>
                    <?php if ($column['id'] == 'job_order_id'): continue; endif; ?>
                    <td>
                        <?php if ($column['id'] == 'posted'): echo('<strong>'); endif; ?>
                        <?php echo($job[$column['id']]); ?>
                        <?php if ($column['id'] == 'posted'): echo('</strong>'); endif; ?>
                        <?php if ($column['id'] == 'title' && isset($hasDescription) && !empty($job['description'])): ?>
                            - <span class="ccmsExcerpt"><?php $this->_($job['excerpt']); ?></span>
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
    </table>

    <?php if ($this->numPages > 1): ?>
        <ul class="ccmsPages">
        <?php for ($i = 1; $i <= $this->numPages; $i++): ?>
            <?php if ($i == $this->page): ?>
                <li class="ccmsCurrent">
                    <?php $this->_($i); ?>
            <?php else: ?>
                <li>
                    <a href="<?php $this->_(CATS_Utility::uri('ccmsPage=' . $i)); ?>"><?php $this->_($i); ?></a>
            <?php endif; ?>
            </li>
        <?php endfor; ?>
        </ul>
    <?php endif; ?>

<?php endif; ?>

