<?php
namespace Pimcore\Cloud\AWS\Deployment\Entity;

use League\CLImate\CLImate;

interface BaseEntityInterface {
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getArn(): string;


    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @return string
     */
    public function getHealthState(): string;
}