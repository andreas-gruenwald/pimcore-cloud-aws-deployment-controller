<?php
namespace Pimcore\Cloud\AWS\Deployment\Exception;

use Aws\Ecs\EcsClient;
use League\CLImate\CLImate;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\File;

class ConfigException extends \Exception
{

}