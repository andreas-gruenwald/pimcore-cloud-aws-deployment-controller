<?php
namespace Pimcore\Cloud\AWS\Deployment\Facility;

use League\CLImate\CLImate;
use Pimcore\Cloud\AWS\Deployment\Service\ParamManager\CliAgent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AwsFacility
{
    private $input;
    private $output;
    private $cliAgent;

    private static $INSTANCE;

    public static function getInstance() : AwsFacility {
        return static::$INSTANCE;
    }

    public function __construct(InputInterface $input, OutputInterface $output) {
        $this->input = $input;
        $this->output = $output;
        $this->cliAgent = new CliAgent($input, $output);
        static::$INSTANCE = $this;
    }

    public function getConfig() : Config {
        return new Config();
    }
    public function getCliAgent() : CliAgent {
        return $this->cliAgent;
    }

    public function getUser() : User {
        return new User();
    }

    public function getEcs() : Ecs {
        return new Ecs($this);
    }

    public function getVpc() : Vpc  {
        return new Vpc($this);
    }

}