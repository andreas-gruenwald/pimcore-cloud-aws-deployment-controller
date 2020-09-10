<?php
namespace Pimcore\Cloud\AWS\Deployment\Service\CliBlocks;

use Pimcore\Cloud\AWS\Deployment\Entity\Ecs\Cluster;
use Pimcore\Cloud\AWS\Deployment\Facility\AwsFacility;

class OutputUtil
{
    public static function outputAppHeader() {
        $climate = AwsFacility::getInstance()->getCliAgent()->getClimate();
        $climate->br(1);
        $climate->border('=-=',50);
        $climate->tab();
        $climate->out('Pimcore Cloud AWS ECS Deployment');
        $climate->border('=-=',50);
        $climate->br(1);
    }
    
    public static function outputAwsConfig() {
        $awsFacility = AwsFacility::getInstance();
        $climate = $awsFacility->getCliAgent()->getClimate();
        $climate->out(sprintf('App: <info>%s</info>', $awsFacility->getConfig()->getAppName()));
        $climate->out(sprintf('Env: <info>%s</info>', $awsFacility->getConfig()->getEnv()));
        $climate->out(sprintf('Region: <info>%s</info>', $awsFacility->getConfig()->getRegion()));
        $climate->br(1);
    }

    public static function outputTable(string $name, array $outputTable) {
        $climate = AwsFacility::getInstance()->getCliAgent()->getClimate();
        $climate->br();
        $climate->bold()->out($name.':');
        $climate->table($outputTable);
        $climate->br();
    }

    public static function outputClusterStatus() {

        $awsFacility = AwsFacility::getInstance();
        $vpcMap = $awsFacility->getVpc()->getVpcSubnetMapViaConfig();

        $vpcId = $vpcMap['vpcId'];
        $subnets = $vpcMap['subnets'];

        $climate = $awsFacility->getCliAgent()->getClimate();


        OutputUtil::outputTable('VPC / Subnets', [['VpcId' => $vpcId] + $subnets]);

        $clusterArn = $awsFacility->getEcs()->getCluster()->pickClusterArnForAppEnv();

        $cluster = $awsFacility->getEcs()->getCluster()->describeCluster($clusterArn);
        $healthState = $cluster->getHealthState();
        if ($healthState == Cluster::HEALTH_STATE_OK) {
            $healthState = '<info>'.$healthState.'</info>';
        } else {
            $healthState = '<error>'.$healthState.'</error>';
        }

        $outputTable = [['Type' => 'Cluster', 'ARN' => $cluster->getArn(), 'Status' => $cluster->getStatus(), 'Health' => $healthState]];
        OutputUtil::outputTable('Cluster', $outputTable);

        $outputTable = [];
        foreach ($awsFacility->getEcs()->getTaskDefinition()->listActiveTaskDefinitions($cluster->getArn()) ? : ['-'] as $arn) {

            $taskDefinition = $awsFacility->getEcs()->getTaskDefinition()->describeTaskDefinition($arn);

            $outputTable[] = [
                'Type' => 'TaskDefinition',
                'Name' => $taskDefinition->getName(),
                'ARN' => $taskDefinition->getArn(),
                'CPU' => $taskDefinition->getCpu(),
                'Memory' => $taskDefinition->getMemory(),
                'Status' => $taskDefinition->getStatus(),
                'Health' => 'UNKNOWN'
            ];

            //$climate->json($taskDefinition);

        }
        OutputUtil::outputTable('Task Definitions', $outputTable);


        $outputTable = [];
        foreach ($awsFacility->getEcs()->getTask()->listTasks($cluster->getArn()) ? : ['-'] as $arn) {
            $outputTable[] = ['Type' => 'Task', 'ARN' => $arn, 'Status' => 'UNKNOWN', 'Health' => 'UNKNOWN'];
        }
        OutputUtil::outputTable('Tasks', $outputTable);

        $outputTable = [];
        $serviceList = $awsFacility->getEcs()->getService()->listServiceDetails($cluster->getArn());
        if (empty($serviceList)) {
            $outputTable[] = [
                'Type'  => 'Service',
                'Name' => '',
                'ARN' => '-',
                'Status' => '-',
                'Health' => '-'
            ];
        } else {
            foreach ($serviceList as $service) {
                $outputTable[] = [
                    'Type'  => 'Service',
                    'Name' => $service->getName(),
                    'ARN' => $service->getArn(),
                    'Status' => $service->getStatus(),
                    'Health' => $service->getHealthState()
                ];
            }
        }

        OutputUtil::outputTable('Services', $outputTable);

    }

    private static function getOptions() : array {

    }

}