<?php
namespace Pimcore\Cloud\AWS\Deployment\Facility;

use Pimcore\Cloud\AWS\Deployment\Facility\Ecs\ClusterService;
use Pimcore\Cloud\AWS\Deployment\Facility\Ecs\ServiceService;
use Pimcore\Cloud\AWS\Deployment\Facility\Ecs\TaskDefinitionService;
use Pimcore\Cloud\AWS\Deployment\Facility\Ecs\TaskService;

class Ecs extends AbstractCliBlock
{
    private $cluster;
    private $taskDefinition;
    private $task;
    private $service;

    public function __construct()
    {
        $this->cluster = new ClusterService();
        $this->taskDefinition = new TaskDefinitionService();
        $this->task = new TaskService();
        $this->service = new ServiceService();
    }

    /**
     * @return ClusterService
     */
    public function getCluster(): ClusterService
    {
        return $this->cluster;
    }

    /**
     * @return TaskService
     */
    public function getTask(): TaskService
    {
        return $this->task;
    }

    /**
     * @return ServiceService
     */
    public function getService() : ServiceService
    {
        return $this->service;
    }

    /**
     * @return TaskDefinitionService
     */
    public function getTaskDefinition(): TaskDefinitionService
    {
        return $this->taskDefinition;
    }

}