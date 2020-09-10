<?php
namespace Pimcore\Cloud\AWS\Deployment\Entity;


use League\CLImate\CLImate;

abstract class AbstractBaseEntity extends AbstractAwsEntity implements BaseEntityInterface
{

    private $name = "";
    private $arn = "";
    private $status = "";

    const STATUS_ACTIVE = "ACTIVE";

    const HEALTH_STATE_OK = "OK";
    const HEALTH_STATE_MISSING = "UNHEALTHY";

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Cluster
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getArn(): string
    {
        return $this->arn;
    }

    /**
     * @param string $arn
     * @return Cluster
     */
    public function setArn(string $arn): self
    {
        $this->arn = $arn;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Cluster
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }


    /**
     * @return string
     */
    public function getHealthState(): string
    {
        //$climate = new CLImate();
        //$climate->to('buffer');
        if (in_array($this->status, ['ACTIVE'])) {
            return sprintf('<info>%s</info>', $this->status);
            //return $climate->green($this->status)->output->get('buffer')->get();
        } else {
            return sprintf('<error>%s</error>', $this->status);
            //return $climate->red($this->status)->output->get('buffer')->get();
        }
    }
}