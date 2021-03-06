<?php

namespace ShopwareDockerControl\Commands;

use ShopwareDockerControl\Services\DockerComposeService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StartContainersCommand extends Command
{
    protected function configure()
    {
        $this->setName('up')
            ->setAliases(['s', 'u'])
            ->setDescription('Start docker containers in DOCKER_BASE_DIR')
            ->addOption(
                'testing',
                't',
                InputOption::VALUE_NONE,
                'If set, testing environment will be used'
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
        $command[] = 'up';
        $command[] = '-d';
        $command[] = '--force-recreate';

        $composeService = new DockerComposeService($input, $output);
        $composeService->execute($command);
    }
}
