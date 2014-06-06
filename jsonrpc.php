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
$task = new Task($registry->shared('db'), $registry->shared('event'));

$server = new Server;
$server->authentication(array('jsonrpc' => $config->get('api_token')));

/**
 * Project procedures
 */
$server->register('createProject', function($name) use ($project) {
    $values = array('name' => $name);
    list($valid,) = $project->validateCreation($values);
    return $valid && $project->create($values);
});

$server->register('getProjectById', function($project_id) use ($project) {
    return $project->getById($project_id);
});

$server->register('getProjectByName', function($name) use ($project) {
    return $project->getByName($name);
});

$server->register('getAllProjects', function() use ($project) {
    return $project->getAll();
});

$server->register('updateProject', function($name, $activated = true) use ($project) {
    $values = array('name' => $name, 'is_active' => $activated);
    list($valid,) = $project->validateModification($values);
    return $valid && $project->create($values);
});

$server->register('removeProject', function($project_id) use ($project) {
    return $project->remove($project_id);
});


/**
 * Task procedures
 */
$server->register('createTask', function(array $values) use ($task) {
    list($valid,) = $task->validateCreation($values);
    return $valid && $task->create($values);
});

$server->register('getTask', function($task_id) use ($task) {
    return $task->getById($task_id);
});

$server->register('getAllTasks', function() use ($task) {
    return $task->getAll();
});

$server->register('updateTask', function($values) use ($task) {
    list($valid,) = $task->validateModification($values);
    return $valid && $task->create($values);
});

$server->register('removeTask', function($task_id) use ($task) {
    return $task->remove($task_id);
});


echo $server->execute();
