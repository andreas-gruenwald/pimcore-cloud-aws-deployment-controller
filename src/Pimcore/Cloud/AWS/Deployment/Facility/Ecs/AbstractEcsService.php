<?php
namespace Pimcore\Cloud\AWS\Deployment\Facility\Ecs;

use Aws\Ecs\EcsClient;
use Pimcore\Cloud\AWS\Deployment\Exception\ConfigException;
use Pimcore\Cloud\AWS\Deployment\Facility\AbstractCliBlock;
use Pimcore\Cloud\AWS\Deployment\Facility\AwsFacility;
use Pimcore\Cloud\AWS\Deployment\Service\ParamManager\CliAgent;

abstract class AbstractEcsService extends AbstractCliBlock
{
    protected function getClient() : EcsClient {
        $ecsClient = new EcsClient(AwsFacility::getInstance()->getConfig()->createClientParams());
        return $ecsClient;
    }

    protected function getCliAgent() : CliAgent {
        return AwsFacility::getInstance()->getCliAgent();
    }

}