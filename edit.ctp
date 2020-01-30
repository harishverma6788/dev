<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $bonus->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $bonus->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Bonuses'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="bonuses form large-9 medium-8 columns content">
    <?= $this->Form->create($bonus) ?>
    <fieldset>
        <legend><?= __('Edit Bonus') ?></legend>
        <?php
            echo $this->Form->input('user_id', ['options' => $users]);
            echo $this->Form->input('bonus_amount');
            echo $this->Form->input('release_date');
            echo $this->Form->input('last_update');
            echo $this->Form->input('status');
            echo $this->Form->input('modify');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
