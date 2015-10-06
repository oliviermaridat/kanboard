<?php

namespace Kanboard\Formatter;

use Kanboard\Model\TaskFilter;
use Kanboard\Model\TaskLink;

/**
 * Gantt chart formatter for task filter
 *
 * @package  formatter
 * @author   Frederic Guillot
 */
class TaskFilterGanttFormatter extends TaskFilter implements FormatterInterface
{
    /**
     * Local cache for project columns
     *
     * @access private
     * @var array
     */
    private $columns = array();

    /**
     * Format tasks to be displayed in the Gantt chart
     *
     * @access public
     * @return array
     */
    public function format()
    {
        $bars = array();

        foreach ($this->query->findAll() as $task) {
            $bars[] = $this->formatTask($task);
        }

        return $bars;
    }

    /**
     * Format a single task
     *
     * @access private
     * @param  array  $task
     * @return array
     */
    private function formatTask(array $task)
    {
        if (! isset($this->columns[$task['project_id']])) {
            $this->columns[$task['project_id']] = $this->board->getColumnsList($task['project_id']);
        }

        $start = $task['date_started'] ?: time();
        $end = $task['date_due'] ?: $start;
	if (empty($task['date_due']) || empty($task['date_started'])) {
	    list($dates_started, $dates_due) = $this->taskLink->getAllDates($task['id'], TaskLink::LABEL_TARGETSMILESTONE_ID);
	    if (empty($task['date_started']) && !empty($dates_started)) {
	        $start = min($dates_started);
	    }
	    if (empty($task['date_due']) && !empty($dates_due)) {
	        $end = min($dates_due);
	    }
	    if (empty($task['date_started'])) {
	        list($dates_started) = $this->taskLink->getAllDates($task['id'], TaskLink::LABEL_ISBLOCKEDBY_ID);
	        if (!empty($dates_started)) {
	            $start = $task['date_started'] ?: max(array_merge($dates_started, array($start)));
	        }
	    }
	}

        return array(
            'type' => 'task',
            'id' => $task['id'],
            'title' => $task['title'],
            'start' => array(
                (int) date('Y', $start),
                (int) date('n', $start),
                (int) date('j', $start),
            ),
            'end' => array(
                (int) date('Y', $end),
                (int) date('n', $end),
                (int) date('j', $end),
            ),
            'column_title' => $task['column_name'],
            'assignee' => $task['assignee_name'] ?: $task['assignee_username'],
            'progress' => $this->task->getProgress($task, $this->columns[$task['project_id']]).'%',
            'link' => $this->helper->url->href('task', 'show', array('project_id' => $task['project_id'], 'task_id' => $task['id'])),
            'color' => $this->color->getColorProperties($task['color_id']),
            'not_defined' => empty($task['date_due']) || empty($task['date_started']),
        );
    }
}
