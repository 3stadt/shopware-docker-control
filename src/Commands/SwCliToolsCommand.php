<?php

namespace ShopwareDockerControl\Commands;

use ShopwareDockerControl\Services\DockerComposeService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SwCliToolsCommand extends Command
{
    protected function configure()
    {
        $this->setName('sw-cli-tools')->setAliases([
            'sw'
        ])
            ->setDescription('Use the sw-cli tools inside a folder/project.')
            ->addOption(
                'project',
                'p',
                InputOption::VALUE_OPTIONAL,
                'The project to execute the command on.',
                getenv('DEFAULT_PROJECT')
            )
            ->addOption(
                'userId',
                'u',
                InputOption::VALUE_OPTIONAL,
                'If set, this userId will be used to execute the command inside the container.',
                getenv('USER_ID')
            )
            ->addArgument(
                'args',
                InputArgument::IS_ARRAY,
                'The sw command to execute, including options',
                ['--help']
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $input->getOption('project');

        $command = ['docker-compose'];
        $command[] = 'run';
        $command[] = '-u' . ($input->getOption('userId'));
        $command[] = 'swag_cli';
        $command[] = 'bash';
        $command[] = '-c';
        $command[] = '"cd /var/www/html/'.$project;
        $command[] = '&&';
        $command[] = 'sw';

        $command = array_merge($command, $input->getArgument('args'));

        $command[] = '"';

        $composeService = new DockerComposeService($input, $output);
        $composeService->execute($command);
    }
}
