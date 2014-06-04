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

$server->register('project.create', function($name) use ($project) {
    $values = array('name' => $name);
    list($valid,) = $project->validateCreation($values);
    return $valid && $this->project->create($values);
});

$server->register('project.read', function($project_id) use ($project) {
    return $project->getById($project_id);
});

$server->register('project.find', function() use ($project) {
    return $project->getAll();
});

$server->register('project.update', function($name, $activated = true) use ($project) {
    $values = array('name' => $name, 'is_active' => $activated);
    list($valid,) = $project->validateModification($values);
    return $valid && $this->project->create($values);
});

$server->register('project.remove', function($project_id) use ($project) {
    return $project->remove($project_id);
});
