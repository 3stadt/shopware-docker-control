<?php

namespace ShopwareDockerControl\Commands;

use ShopwareDockerControl\Services\DockerComposeService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCodeCoverageCommand extends Command
{
    protected function configure()
    {
        $this->setName('create-coverage-report')
            ->setAliases(['ccr'])
            ->setDescription('Generates a code coverage report for the current project.')
            ->addArgument(
                'project',
                InputArgument::OPTIONAL,
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
            ->addOption(
                'outputFolder',
                'o',
                InputOption::VALUE_OPTIONAL,
                'If set, the given folder will used for output - Otherwise, ' . getenv('DEFAULT_PROJECT') . '-ccr-' . date('dmYHis'),
                getenv('DEFAULT_PROJECT') . '-ccr-' . date('dmYHis')
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $input->getArgument('project');

        $command = ['docker-compose'];
        $command[] = 'run';
        $command[] = '-u' . $input->getOption('userId');
        $command[] = '--rm';
        $command[] = 'swag_cli_x';
        $command[] = 'bash';
        $command[] = '-c';
        $command[] = '"cd /var/www/html/'.$project.'/tests';
        $command[] = '&&';
        $command[] = 'mkdir';
        $command[] = '-p';
        $command[] = '/var/www/html/' . $input->getOption('outputFolder');
        $command[] = '&&';
        $command[] = '../vendor/phpunit/phpunit/phpunit';
        $command[] = '--coverage-html=/var/www/html/' . $input->getOption('outputFolder').'"';

        $composeService = new DockerComposeService($input, $output);
        $composeService->execute($command);
    }
}
