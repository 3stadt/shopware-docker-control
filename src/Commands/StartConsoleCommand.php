<?php

namespace ShopwareDockerControl\Commands;

use ShopwareDockerControl\Services\DockerComposeService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StartConsoleCommand extends Command
{
    protected function configure()
    {
        $this->setName('start-console')
            ->setAliases(['sc'])
            ->setDescription('Starts a bash in a new swag_cli container')
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
        $command[] = 'run';
        $command[] = '--rm';
        $command[] = 'swag_cli';
        $command[] = 'bash';

        $composeService = new DockerComposeService($input, $output);
        $composeService->executeInteractive($command);
    }
}
