<?php
namespace Pimcore\Cloud\AWS\Deployment\Facility\Ecs;

use Pimcore\Cloud\AWS\Deployment\Entity\Ecs\Cluster;
use Pimcore\Cloud\AWS\Deployment\Entity\Ecs\TaskDefinition;

class TaskDefinitionService extends AbstractEcsService
{

    public function listActiveTaskDefinitions(string $clusterArn) : array {
        $result = $this->getClient()->listTaskDefinitions([
            'cluster' => $clusterArn,
            'status' => 'ACTIVE'
        ]);
        return $result->get('taskDefinitionArns');
    }

    public function describeTaskDefinition(string $arn) : TaskDefinition {
        $result = $this->getClient()->describeTaskDefinition([
            'taskDefinition' => $arn,
             'include' => ['TAGS']
        ]);

        $taskDefinition = $result['taskDefinition'];
        unset($taskDefinition['containerDefinitions']);

        //$this->climate->green()->json($taskDefinition);

        $taskDefinitionEntity = new TaskDefinition($result['taskDefinition']);
        $taskDefinitionEntity->setArn($taskDefinition['taskDefinitionArn']);
        $taskDefinitionEntity->setName($taskDefinition['family']);
        $taskDefinitionEntity->setStatus($taskDefinition['status']);
        $taskDefinitionEntity->setMemory($taskDefinition['memory']);
        $taskDefinitionEntity->setCpu($taskDefinition['cpu']);
        return $taskDefinitionEntity;
    }

}