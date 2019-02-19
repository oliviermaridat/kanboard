<?php if (! empty($subtasks)): ?>
    <table
        class="subtasks-table table-striped table-scrolling"
        data-save-position-url="<?= $this->url->href('SubtaskController', 'movePosition', array('project_id' => $task['project_id'], 'task_id' => $task['id'])) ?>"
    >
    <thead>
        <tr>
            <th class="column-45"><?= t('Title') ?></th>
            <th class="column-15"><?= t('Assignee') ?></th>
            <?= $this->hook->render('template:subtask:table:header:before-timetracking') ?>
            <th><?= t('Time tracking') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php $total_time_spent = 0; ?>
        <?php $total_time_estimated = 0; ?>
        <?php $total_time_spent_cumul = 0; ?>
        <?php foreach ($subtasks as $subtask): ?>
        <?php $total_time_spent += $subtask['time_spent']; ?>
        <?php $total_time_estimated += $subtask['time_estimated']; ?>
        <?php $total_time_spent_cumul += min($subtask['time_spent'], $subtask['time_estimated']); ?>
        <tr data-subtask-id="<?= $subtask['id'] ?>">
            <td>
                <?php if ($editable): ?>
                    <i class="fa fa-arrows-alt draggable-row-handle" title="<?= t('Change subtask position') ?>"></i>&nbsp;
                    <?= $this->render('subtask/menu', array(
                        'task' => $task,
                        'subtask' => $subtask,
                    )) ?>
                    <?= $this->subtask->renderToggleStatus($task, $subtask, 'table') ?>
                <?php else: ?>
                    <?= $this->subtask->renderTitle($subtask) ?>
                <?php endif ?>
                <?= $this->hook->render('template:subtask:table:after-title', array('subtask' => $subtask)) ?>
            </td>
            <td>
                <?php if (! empty($subtask['username'])): ?>
                    <?= $this->text->e($subtask['name'] ?: $subtask['username']) ?>
                <?php endif ?>
            </td>
            <?= $this->hook->render('template:subtask:table:rows', array('subtask' => $subtask)) ?>
            <td>
                <?= $this->render('subtask/timer', array(
                    'task'    => $task,
                    'subtask' => $subtask,
                )) ?>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
    <?php if (! empty($total_time_spent) || ! empty($total_time_estimated)): ?>
    <tr>
        <th colspan="2" class="total"><?= t('Total time tracking') ?></th>
        <td>
            <?php if (! empty($total_time_spent)): ?>
                <strong><?= $this->text->e($total_time_spent).'h' ?></strong> <?= t('spent') ?>
            <?php endif ?>

            <?php if (! empty($total_time_estimated)): ?>
                <strong><?= $this->text->e($total_time_estimated).'h' ?></strong> <?= t('estimated') ?>
            <?php endif ?>

            <?php if (! empty($total_time_spent) && ! empty($total_time_estimated)): ?>
                <strong><?= $this->text->e($total_time_estimated-$total_time_spent).'h' ?></strong> <?= t('remaining') ?>
            <?php endif ?>

            <div class="progress-bar">
                <?php $percentage = (!$total_time_estimated ? 0 : round($total_time_spent_cumul/$total_time_estimated*100.0)); ?>
                <div class="task-board progress color-<?= $task['color_id'] ?>" style="width:<?= min(99, $percentage) ?>%;">
                    &#160;<?= $percentage ?>%
                </div>
            </div>
        </td>
    </tr>
    </tfoot>
    <?php endif ?>
    </table>
<?php endif ?>
