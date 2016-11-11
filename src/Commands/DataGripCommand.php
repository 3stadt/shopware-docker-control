<?php

namespace ShopwareDockerControl\Commands;

use ShopwareDockerControl\Services\DockerComposeService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DataGripCommand extends Command
{
    protected function configure()
    {
        $this->setName('datagrip')
            ->setAliases(['dg'])
            ->setDescription('Start datagrip')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = ['docker-compose'];
        $command[] = 'run';
        $command[] = '-d';
        $command[] = 'swag_datagrip';
        $command[] = '/opt/datagrip/bin/datagrip.sh';

        $composeService = new DockerComposeService($input, $output);
        $composeService->execute($command);
    }
}