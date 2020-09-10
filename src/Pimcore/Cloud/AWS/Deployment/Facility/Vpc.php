<?php
namespace Pimcore\Cloud\AWS\Deployment\Facility;

use Aws\Ec2\Ec2Client;
use Pimcore\Cloud\AWS\Deployment\Exception\ConfigException;

class Vpc extends AbstractCliBlock
{
    const VPC_ID_PARAM = 'VpcId';

    public function getClient(): Ec2Client {
        $ec2Client = new Ec2Client(AwsFacility::getInstance()->getConfig()->createClientParams());
        return $ec2Client;
    }

    public function getVpcSubnetMapViaConfig() : array {
        $vpcMap = $this->getVpcSubnetMap();
        $cliAgent = AwsFacility::getInstance()->getCliAgent();
        if ($cliAgent->hasParameter(self::VPC_ID_PARAM)) {
            $currentVpcId = $cliAgent->getParameter(self::VPC_ID_PARAM);
            if (array_key_exists($currentVpcId, $vpcMap)) {
                return ['vpcId' => $currentVpcId, 'subnets' => $vpcMap[$currentVpcId]];
            }
        }

        if (empty($vpcMap)) {
            throw new ConfigException(sprintf('No VPCs found at all. Is there a problem with your Cloudformation template?'));
        }

        $vpcPrintMap = [];
        foreach ($vpcMap as $vpc => $subnets) {
            $vpcPrintMap[$vpc] = sprintf('%s (subnets %s)', $vpc, implode(', ', $subnets));
        }

        $input = $this->climate->radio('Please select the relevant VPC: ',$vpcPrintMap);
        $currentVpcId = $input->prompt();
        $cliAgent->setParameter(self::VPC_ID_PARAM, $currentVpcId);
        return ['vpcId' => $currentVpcId, 'subnets' => $vpcMap[$currentVpcId]];
    }

    public function getVpcSubnetMap() : array {
        $result = $this->getClient()->describeSubnets();

        $vpcList = [];

        foreach ($result->get('Subnets') as $subnetData) {
            $vpc = $subnetData['VpcId'];
            if (!array_key_exists($vpc, $vpcList)) {
                $vpcList[$vpc] = [];
            }
            $subnet = $subnetData['SubnetId'];
            $vpcList[$vpc][] = $subnet;
        }

        return $vpcList;
    }
}