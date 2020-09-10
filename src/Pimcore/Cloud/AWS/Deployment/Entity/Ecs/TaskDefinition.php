<?php
namespace Pimcore\Cloud\AWS\Deployment\Entity\Ecs;
use Pimcore\Cloud\AWS\Deployment\Entity\AbstractBaseEntity;

class TaskDefinition extends AbstractBaseEntity
{

    private $cpu = 0;
    private $memory = 0;

    /**
     * @return int
     */
    public function getCpu(): int
    {
        return $this->cpu;
    }

    /**
     * @param int $cpu
     * @return TaskDefinition
     */
    public function setCpu(int $cpu): TaskDefinition
    {
        $this->cpu = $cpu;
        return $this;
    }

    /**
     * @return int
     */
    public function getMemory(): int
    {
        return $this->memory;
    }

    /**
     * @param int $memory
     * @return TaskDefinition
     */
    public function setMemory(int $memory): TaskDefinition
    {
        $this->memory = $memory;
        return $this;
    }


}