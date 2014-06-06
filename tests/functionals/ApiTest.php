<?php

require_once __DIR__.'/../../vendor/JsonRPC/Client.php';

class Api extends PHPUnit_Framework_TestCase
{
    const URL = 'http://localhost:8000/jsonrpc.php';
    const KEY = '19ffd9709d03ce50675c3a43d1c49c1ac207f4bc45f06c5b2701fbdf8929';

    private $client;

    public function setUp()
    {
        $this->client = new JsonRPC\Client(self::URL, 5, true);
        $this->client->authentication('jsonrpc', self::KEY);
    }

    public function testGetAll()
    {
        $projects = $this->client->getAllProjects();
        $this->assertNotEmpty($projects);

        foreach ($projects as $project) {
            $this->client->removeProject($project['id']);
        }
    }

    public function testCreateProject()
    {
        $this->assertTrue($this->client->createProject('API test'));
    }

    public function testGetProjectById()
    {
        $project = $this->client->getProjectById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(1, $project['id']);
    }
}
