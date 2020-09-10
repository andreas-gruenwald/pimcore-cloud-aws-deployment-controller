<?php
namespace Pimcore\Cloud\AWS\Deployment\Command;

use Aws\Ecs\EcsClient;
use Aws\Iam\IamClient;
use League\CLImate\CLImate;
use Pimcore\Cloud\AWS\Deployment\Exception\ConfigException;
use Pimcore\Cloud\AWS\Deployment\Facility\AwsFacility;
use Pimcore\Cloud\AWS\Deployment\Service\ParamManager\CliAgent;
use Pimcore\Cloud\AWS\Deployment\Service\ParamManager\ConfigFile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigCommand extends Command
{
    private $climate;

    public function __construct(string $name = null)
    {
        parent::__construct(null);
        $this->climate = new CLImate;
    }

    protected function configure()
    {
        $this->setName('pimcore:cloud:aws:config');
        $this->addArgument('operation', InputArgument::OPTIONAL);
        $this->addOption('id', null, InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {

            $operation = $input->getArgument('operation');
            $id = $input->getOption('id');

            switch ($operation) {
                case 'truncate':
                    if (empty($id)) {
                        throw new ConfigException('You must specifiy the profile using the input option "id".');
                    }
                    $configFile = new ConfigFile();
                    $configFile->deleteProfile($id);
                    $this->climate->green(sprintf('Deleted profile "%s" successfully.', $id));
                    break;
                default:
                    throw new ConfigException('Not Implemented yet!');
            }

        } catch (\Exception $e) {
            $this->climate->error($e->getMessage());
            throw $e;
        }

        return 0;
    }
}