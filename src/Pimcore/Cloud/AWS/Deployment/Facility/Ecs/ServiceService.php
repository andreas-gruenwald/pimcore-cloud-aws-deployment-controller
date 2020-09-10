<?php
namespace Pimcore\Cloud\AWS\Deployment\Facility\Ecs;


use Pimcore\Cloud\AWS\Deployment\Entity\Ecs\Service;

class ServiceService extends AbstractEcsService
{

    const FRONTEND_SERVICE_BLUE = 'frontend-service-blue';

    public function listServices(string $clusterArn) : array {
        $result = $this->getClient()->listServices([
            'cluster' => $clusterArn
        ]);
        return $result->get('serviceArns');
    }

    /**
     * @param string $clusterArn
     * @return Service[]
     */
    public function listServiceDetails(string $clusterArn) : array {
        $serviceList = [];
        $arnList = $this->listServices($clusterArn);
        if (!empty($arnList)) {
            $result = $this->getClient()->describeServices([
                'cluster' => $clusterArn,
                'services' => $arnList
            ]);
            $services = $result['services'];
            foreach ($services as $data) {
                $service = new Service($data);
                $service->setArn($data['serviceArn']);
                $service->setName($data['serviceName']);
                $service->setStatus($data['status']);
                $serviceList[] = $service;
            }
        }
        return $serviceList;
    }

    public function createPimcoreFrontendService(string $clusterArn, array $subnets) {
        $this->createService($clusterArn,
            self::FRONTEND_SERVICE_BLUE,
                     $subnets,
            'arn:aws:ecs:eu-central-1:414501751304:task-definition/pimcore-frontend-pimcore-app-dev:1');
    }

    public function createService(string $clusterArn, string $serviceName, array $subnets,
                                  string $taskDefinitioArn) {
        $this->climate->spinner('Create service '.$serviceName.'...');
        $promise = $this->getClient()->createService([
            'cluster' => $clusterArn,
            'serviceName' => $serviceName,
            'taskDefinition' => $taskDefinitioArn,
            'desiredCount' => 3,
            'launchType' => 'FARGATE',
            'healthCheckGracePeriodSeconds' => 3600, //1h -> important setting
            'loadBalancers' => [
                    [
                        'containerName' => 'pimcore-app-dev-task-definition',
                        'containerPort' => 80,
                    //   'loadBalancerName' => 'pimcore-app-dev-load-balancer',
                    'targetGroupArn' => 'arn:aws:elasticloadbalancing:eu-central-1:414501751304:targetgroup/pimcore-app-dev-tg-blue/79788ce39ea3563f',
                ]
            ],
            'networkConfiguration' => [
                'awsvpcConfiguration' => [
                    'assignPublicIp' => 'ENABLED', //DISABLED -> if not enabled, Images cannot be pulled.
                    'securityGroups' => ['sg-0155263121f7a5a6e'], //@todo...
                    'subnets' => $subnets,
                ]
            ],
        ]);

        //$this->climate->json($promise);
        //exit;

        $hasAnswered = false;
/*
        $promise->then(
            function (ResultInterface $result) use ($hasAnswered) {
                echo "Service has been created...";
                $hasAnswered = true;
                exit;
        },
            function ($reason) use ($hasAnswered) {
                echo "The promise was rejected with {$reason}";
                $hasAnswered = true;
                exit;
            }
        );


        $spinner = $this->climate->spinner('Wait');
        while (!$hasAnswered) {
            $spinner->advance();
            sleep(1);
        }

        echo "HERE!";exit;*/
    }

}