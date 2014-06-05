<?php

require __DIR__.'/app/common.php';
require __DIR__.'/vendor/JsonRPC/Server.php';

use JsonRPC\Server;
use Model\Project;
use Model\Task;
use Model\User;
use Model\Config;

$config = new Config($registry->shared('db'), $registry->shared('event'));
$project = new Project($registry->shared('db'), $registry->shared('event'));

$server = new Server;
$server->authentication(array('jsonrpc' => $config->get('api_token')));

/**
 * Project procedures
 */
$server->register('project.create', function($name) use ($project) {
    $values = array('name' => $name);
    list($valid,) = $project->validateCreation($values);
    return $valid && $project->create($values);
});

$server->register('project.find_one', function($project_id) use ($project) {
    return $project->getById($project_id);
});

$server->register('project.find_all', function() use ($project) {
    return $project->getAll();
});

$server->register('project.update', function($name, $activated = true) use ($project) {
    $values = array('name' => $name, 'is_active' => $activated);
    list($valid,) = $project->validateModification($values);
    return $valid && $project->create($values);
});

$server->register('project.remove', function($project_id) use ($project) {
    return $project->remove($project_id);
});


/**
 * Task procedures
 */
$server->register('task.create', function(array $values) use ($task) {
    list($valid,) = $task->validateCreation($values);
    return $valid && $task->create($values);
});

$server->register('task.find_one', function($task_id) use ($task) {
    return $task->getById($task_id);
});

$server->register('task.find_all', function() use ($task) {
    return $task->getAll();
});

$server->register('task.update', function($values) use ($task) {
    list($valid,) = $task->validateModification($values);
    return $valid && $task->create($values);
});

$server->register('task.remove', function($task_id) use ($task) {
    return $task->remove($task_id);
});
