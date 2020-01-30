<script>
 
  $('#table-main').DataTable().destroy();
    $('#bounssearch').DataTable( {
        "order": [[ 3, "asc" ]]
    } );


</script> 

<table  id="bounssearch" class="table table-striped table-bordered table-hover table-checkable" >
    <thead>
        <tr>

            <th><?= __('Agent'); ?></th>
            <th><?= __('IC number'); ?></th>
            <th><?= __('Bonus Amount'); ?></th>
            <th><?= __('Bonus Type'); ?></th>
            <th><?= __('Customer'); ?></th>
            <th class="dateclass"><?= __('Release Date'); ?></th>

            <th><?= __('Submitted by'); ?></th>
            <?php if ($current_user["group_id"] == 1) { ?>
                <th><?= __('Status'); ?></th>
            <?php } ?>

        

        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($bonuses as $bonus):  ?>
            <tr>
               <?php $agent = $users->find()->where(array('Users.id' => $bonus->agent_id))->contain(['UserProfiles'])->first(); ?>
                <td><?php echo ucfirst($agent->name);  ?></td>
                <td> <?= h($agent->user_profile->ic_number) ?></td>
                <td><?= h($bonus->bonus_amount) ?></td>
                 <td><?= h($bonus->type) ?></td>
                 <td><?= h($customerName[$bonus->id]) ?></td>
                 <td data-order="<?php echo $bonus->release_date ?>"><?php echo $bonus->release_date->i18nFormat('dd/MM/yyyy') ?></td>
                 <?php $createduser = $users->find()->where(array('Users.id' => $bonus->last_update))->contain(['UserProfiles'])->first(); ?>
                 <td><?= h($createduser->name) . ',' . h($createduser->user_profile->ic_number) ?></td>
                  <?php if ($current_user["group_id"] == 1) { ?>
                                    <td> <?php echo $this->Html->link(__('Remove'), '#', ['data-toggle' => 'modal', 'data-target' => '#myModal', 'id' => $bonus->id, 'class' => 'removerows', 'escape' => false]); ?></td> 
                                <?php } ?>
            </tr>
            <?php
         
        endforeach;
        ?>
    </tbody>
</table>



