<?php
namespace Pimcore\Cloud\AWS\Deployment\Facility\Ecs;

use Pimcore\Cloud\AWS\Deployment\Facility\AwsFacility;

class TaskService extends AbstractEcsService
{

    public function listTasks(string $clusterArn) : array {
        $result = $this->getClient()->listTasks([
            'cluster' => $clusterArn
        ]);
        return $result->get('taskArns');
    }

    public function runCliTask(string $clusterArn) {
        $this->getClient()->runTask([
            //'capacityProviderStrategy'
           'cluster' => $clusterArn,
           'count' => 1,
            //@todo 'count' => $this->getCliAgent()->getParameter('')
          'enableECSManagedTags' => true,
          //'group'
            'launchType' => 'FARGATE',
            'networkConfiguration' => [
                'awsvpcConfiguration' => [
                    'assignPublicIp' => 'DISABLED',
                    'securityGroups' => [],
                    'subnets' => [],
                ]
            ],
            'referenceId' => $clusterArn.'-cli-task-blue',
            'startedBy' => 'Pimcore Cloud Deployment CLI',
            'tags' => [
                'UniqueAppName' => AwsFacility::getInstance()->getConfig()->getUniqueAppName(),
                'App' => AwsFacility::getInstance()->getConfig()->getAppName(),
                'Env' => AwsFacility::getInstance()->getConfig()->getEnv(),
            ],
            'taskDefinition' => 'TODO',


        ]);
    }

}