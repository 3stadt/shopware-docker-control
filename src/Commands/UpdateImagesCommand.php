<?php

namespace ShopwareDockerControl\Commands;

use ShopwareDockerControl\Services\DockerComposeService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateImagesCommand extends Command
{
    protected function configure()
    {
        $this->setName('update-images')
            ->setAliases(['bc', 'ui'])
            ->setDescription('Pull/Update all docker images')
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
        $command[] = 'pull';

        $composeService = new DockerComposeService($input, $output);
        $composeService->execute($command);
    }
}
