<?php
namespace Pimcore\Cloud\AWS\Deployment\Facility;

use League\CLImate\CLImate;

abstract class AbstractCliBlock
{
    protected $climate;

    public function __construct()
    {
        $this->climate = new CLImate();
    }


}