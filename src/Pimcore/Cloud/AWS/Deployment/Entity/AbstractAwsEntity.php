<?php
namespace Pimcore\Cloud\AWS\Deployment\Entity;

abstract class AbstractAwsEntity implements \JsonSerializable
{
    protected $result;

    public function __construct($resultPart)
    {
        $this->result = $resultPart;
    }

    public function setResultPart($resultPart) {
        $this->result = $resultPart;
    }

    public function getResultPart() {
        return $this->result;
    }

    public function jsonSerialize()
    {
        return $this->result ? $this->result : [];
    }


}