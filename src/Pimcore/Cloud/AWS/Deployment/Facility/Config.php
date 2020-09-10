<?php
namespace Pimcore\Cloud\AWS\Deployment\Facility;


class Config extends AbstractCliBlock
{
    private $cliAgent;

    public function __construct()
    {
        $this->cliAgent = AwsFacility::getInstance()->getCliAgent();
    }

    public function initDefaultConfig() : self {
        $this->getAppName();
        $this->getEnv();
        $this->getRegion();
        return $this;
    }

    public function getUniqueAppName() : string {
        return $this->getAppName().'-'.$this->getEnv();
    }

    public function getRegion() : string {
        return $this->cliAgent->getParameter('AWS_DEFAULT_REGION');
    }

    public function getAppName() : string {
        return $this->cliAgent->getParameter('AppName');
    }

    public function getEnv() : string {
        return $this->cliAgent->getParameter('Env');
    }

    public function createClientParams() : array {
        return [
            'version' => 'latest',
            'region' => $this->getRegion()
        ];
    }
}