<?php
namespace Pimcore\Cloud\AWS\Deployment\Facility;

use Aws\Iam\IamClient;

class User extends AbstractCliBlock
{
    const TASK_EXECUTION_ROLE_ARN_NAME = 'taskExecutionRoleArn';

    public function pickTaskExecutionRole() : string {
        $awsFacility = AwsFacility::getInstance();

        $isSetParameter = $awsFacility->getCliAgent()->hasParameter(self::TASK_EXECUTION_ROLE_ARN_NAME);
        if (!$isSetParameter) {
            $iamClient = new IamClient($awsFacility->getConfig()->createClientParams());
            $this->climate->out('');
            $rolesResult = $iamClient->listRoles();
            $this->climate->info('Please pick the right execution ARN for you:');
            $padding = $this->climate->padding(70)->char('-');
            foreach ($rolesResult['Roles'] as $role) {
                $roleName = $role['RoleName'];
                $roleArn = $role['Arn'];
                if (strpos($roleName, 'PimcoreUsers-EcsContainerExecutionRole') > 0) {
                    $padding->label($roleName)->result('<info>'.$roleArn.'</info>');
                } else {
                    $padding->label($roleName)->result($roleArn);
                }
            }
        }

        $taskExecutionRoleArn = $awsFacility->getCliAgent()->getParameter(self::TASK_EXECUTION_ROLE_ARN_NAME);
        return $taskExecutionRoleArn;
    }

    public function assumeRole(string $arn) {
        $awsFacility = AwsFacility::getInstance();
        $stsClient = new \Aws\Sts\StsClient([
            'region' => $awsFacility->getConfig()->getRegion(),
            'version' => '2011-06-15',
        ]);

        $stsClient->assumeRole([
            //  'RoleArn' => 'arn:aws:iam::aws:policy/aws-service-role/AmazonECSServiceRolePolicy',
            'RoleArn' => $arn,
            //'RoleArn' => $taskExecutionRoleArn,
            'RoleSessionName' => 'session1'
        ]);
    }

}