<?php
namespace Pimcore\Cloud\AWS\Deployment\Entity\Ecs;
use Pimcore\Cloud\AWS\Deployment\Entity\AbstractBaseEntity;

class Cluster extends AbstractBaseEntity
{

    private $runningTasksCount = 0;
    private $pendingTasksCount = 0;
    private $activeServicesCount = 0;

    const STATUS_ACTIVE = "ACTIVE";


    /**
     * @return int
     */
    public function getRunningTasksCount(): int
    {
        return $this->runningTasksCount;
    }

    /**
     * @param int $runningTasksCount
     * @return Cluster
     */
    public function setRunningTasksCount(int $runningTasksCount): Cluster
    {
        $this->runningTasksCount = $runningTasksCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getPendingTasksCount(): int
    {
        return $this->pendingTasksCount;
    }

    /**
     * @param int $pendingTasksCount
     * @return Cluster
     */
    public function setPendingTasksCount(int $pendingTasksCount): Cluster
    {
        $this->pendingTasksCount = $pendingTasksCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getActiveServicesCount(): int
    {
        return $this->activeServicesCount;
    }

    /**
     * @param int $activeServicesCount
     * @return Cluster
     */
    public function setActiveServicesCount(int $activeServicesCount): Cluster
    {
        $this->activeServicesCount = $activeServicesCount;
        return $this;
    }

    /**
     * @return string
     */
    public function getHealthState(): string
    {
        return $this->pendingTasksCount == 0 && $this->activeServicesCount > 0 && $this->runningTasksCount > 0
            ? self::HEALTH_STATE_OK
            : self::HEALTH_STATE_MISSING
            ;
    }
}