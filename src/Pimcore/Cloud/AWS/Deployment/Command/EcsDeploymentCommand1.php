<?php
namespace Pimcore\Cloud\AWS\Deployment\Command;

use Aws\Ecs\EcsClient;
use Aws\Iam\IamClient;
use League\CLImate\CLImate;
use Pimcore\Cloud\AWS\Deployment\Entity\Ecs\Cluster;
use Pimcore\Cloud\AWS\Deployment\Facility\AwsFacility;
use Pimcore\Cloud\AWS\Deployment\Service\CliBlocks\OutputUtil;
use Pimcore\Cloud\AWS\Deployment\Service\ParamManager\CliAgent;
use Pimcore\Cloud\AWS\Deployment\Service\ParamManager\ConfigFile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EcsDeploymentCommand1 extends Command
{
    private $climate;

    public function __construct(string $name = null)
    {
        parent::__construct(null);
        $this->climate = new CLImate;
    }

    protected function configure()
    {
        $this->setName('pimcore:cloud:aws:deployment1');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $awsFacility = new AwsFacility($input, $output);
            OutputUtil::outputAppHeader();

            $awsFacility->getConfig()->initDefaultConfig();
            OutputUtil::outputAwsConfig();

            $taskExecutionRoleArn = $awsFacility->getUser()->pickTaskExecutionRole();
            $awsFacility->getUser()->assumeRole($taskExecutionRoleArn);


            do {
                try {

                    $input = $this->climate->green()->radio('Please select action: ', [
                        'create-services' => 'Create Pimcore Services',
                        'status' => 'Status',
                        'show-config' => 'Show Config',
                        'quit' => 'Quit',
                    ]);

                    $response = $input->prompt();
                    switch ($response) {
                        case 'create-services':

                            $clusterArn = $awsFacility->getEcs()->getCluster()->pickClusterArnForAppEnv();
                            $vpcMap = $awsFacility->getVpc()->getVpcSubnetMapViaConfig();
                            $vpcId = $vpcMap['vpcId'];
                            $subnets = $vpcMap['subnets'];

                            $awsFacility->getEcs()->getService()->createPimcoreFrontendService(
                                $clusterArn,
                                $subnets
                            );
                            $this->climate->green('Created Pimcore Frontend Service.');
                            break;
                        case 'status':
                            OutputUtil::outputClusterStatus();
                            break;
                        case 'show-config':
                            $configFile = new ConfigFile();
                            $params = $configFile->readParams();
                            $table = [];
                            foreach ($params as $key => $value) {
                                $table[] = ['Parameter' => $key , 'Value' => $value];
                            }
                            OutputUtil::outputTable(sprintf('Parameters (profile "default" in "%s")',
                                $configFile->accessConfigFile()->getPath().'/'.$configFile->accessConfigFile()->getBasename()
                                ), $table
                            );

                        case 'quit':
                        default:
                    };
                } catch (\Throwable $e) {
                    $this->climate->error($e->getMessage());
                    sleep(1);
                }
            } while ($response != 'quit');




        } catch (\Exception $e) {
            $this->climate->error($e->getMessage());
            throw $e;
        }




        return 0;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;
        //return 1
    }
}