


<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Bonus'), ['action' => 'edit', $bonus->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Bonus'), ['action' => 'delete', $bonus->id], ['confirm' => __('Are you sure you want to delete # {0}?', $bonus->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Bonuses'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Bonus'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="bonuses view large-9 medium-8 columns content">
    <h3><?= h($bonus->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('User') ?></th>
            <td><?= $bonus->has('user') ? $this->Html->link($bonus->user->name, ['controller' => 'Users', 'action' => 'view', $bonus->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Last Update') ?></th>
            <td><?= h($bonus->last_update) ?></td>
        </tr>
        <tr>
            <th><?= __('Status') ?></th>
            <td><?= h($bonus->status) ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($bonus->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Bonus Amount') ?></th>
            <td><?= $this->Number->format($bonus->bonus_amount) ?></td>
        </tr>
        <tr>
            <th><?= __('Release Date') ?></th>
            <td><?= h($bonus->release_date) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($bonus->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modify') ?></th>
            <td><?= h($bonus->modify) ?></td>
        </tr>
    </table>
</div>
