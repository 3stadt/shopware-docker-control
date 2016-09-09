<?php

namespace ShopwareDockerControl\Commands;

use ShopwareDockerControl\Services\DockerComposeService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StopContainersCommand extends Command
{
    protected function configure()
    {
        $this->setName('stop')
            ->setAliases(['d','o'])
            ->setDescription('Start docker containers in DOCKER_BASE_DIR')
            ->addOption(
                'testing',
                't',
                InputOption::VALUE_NONE,
                'If set, testing environment will be used'
            )
            ->addOption(
                'down',
                'd',
                InputOption::VALUE_NONE,
                'If set, containers will be destroyed.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = ['docker-compose'];
        if ($input->getOption('testing')) {
            $command[] = '-f';
            $command[] = 'docker-compose-testing.yml';
        }
        if ($input->getOption('down')) {
            $command[] = 'down';
            $command[] = '--remove-orphans';
        } else {
            $command[] = 'stop';
        }

        $composeService = new DockerComposeService($input, $output);
        $composeService->execute($command);
    }
}
