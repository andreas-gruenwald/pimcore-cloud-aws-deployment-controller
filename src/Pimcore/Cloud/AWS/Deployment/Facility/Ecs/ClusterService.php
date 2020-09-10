<?php
namespace Pimcore\Cloud\AWS\Deployment\Facility\Ecs;

use Aws\Ecs\EcsClient;
use Pimcore\Cloud\AWS\Deployment\Entity\Ecs\Cluster;
use Pimcore\Cloud\AWS\Deployment\Exception\ConfigException;
use Pimcore\Cloud\AWS\Deployment\Facility\AbstractCliBlock;
use Pimcore\Cloud\AWS\Deployment\Facility\AwsFacility;

class ClusterService extends AbstractEcsService
{

    public function listClusterArns() : array {
        $result = $this->getClient()->listClusters();
        return $result->get('clusterArns');
    }

    public function pickClusterArnForAppEnv() : string {
        AwsFacility::getInstance()->getConfig()->getUniqueAppName();
        $uniqueAppName = AwsFacility::getInstance()->getConfig()->getUniqueAppName();
        $arnMatchTerm = $uniqueAppName.'-ecs-cluster';

        $arnList = $this->listClusterArns();
        $matchingArnList = [];
        foreach ($arnList as $arn) {
            if ($this->isArnMatching($arn, $arnMatchTerm)) {
                $matchingArnList[] = $arn;
            }
        }

        if (count($matchingArnList) == 1) {
            return $matchingArnList[0];
        } elseif (count($matchingArnList) > 1) {
            throw new ConfigException(sprintf('Multiple clusters matched the ARN term "%s" (%s).',
                $arnMatchTerm, implode(', ', $matchingArnList)));
        } else {
            throw new ConfigException(sprintf('No cluster found matching the ARN term "%s" (%s).',
                $arnMatchTerm, implode(', ', $arnList)));
        }

    }

    public function filterArnByAppEnv(string $arn) {
        $uniqueAppName = AwsFacility::getInstance()->getConfig()->getUniqueAppName();
        return $this->isArnMatching($arn, $uniqueAppName);
    }


    public function isArnMatching(string $arn, string $part) {
        return strpos($arn, $part) !== false;
    }

    public function describeCluster(string $arn) : Cluster {
        $result = $this->getClient()->describeClusters(['clusters' => [$arn], 'include' => [
            'ATTACHMENTS', 'SETTINGS', 'STATISTICS'
        ]]);

        $cluster = new Cluster($result);
        $cluster->setArn($arn);

        if (isset($result['clusters'][0])) {
            $cluster->setResultPart($result['clusters'][0]);
            $data = $result['clusters'][0];
            $cluster->setName($data['clusterName']);
            $cluster->setStatus($data['status']);
            $cluster->setRunningTasksCount($data['runningTasksCount']);
            $cluster->setPendingTasksCount($data['pendingTasksCount']);
            $cluster->setActiveServicesCount($data['activeServicesCount']);
        }

        return $cluster;
    }

}