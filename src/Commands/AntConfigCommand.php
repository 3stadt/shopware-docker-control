<?php

namespace ShopwareDockerControl\Commands;

use ShopwareDockerControl\Services\DockerComposeService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AntConfigCommand extends Command
{
    protected function configure()
    {
        $this->setName('ant-config')
            ->setAliases(['ac'])
            ->setDescription('Perform ant-configure on a project')
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
        ;
    }

    protected function execute (  InputInterface $input, OutputInterface $output){
        $project = $input->getArgument('project');

        $command = ['docker-compose'];
        $command[] = 'run';
        $command[] = '-eANT_OPTS=-D"file.encoding=UTF-8"';
        $command[] = '-u' . ($input->getOption('userId'));
        $command[] = 'swag_cli';
        $command[] = 'ant';
        $command[] = '-f';
        $command[] = '/var/www/html/'.escapeshellcmd($project).'/build/build.xml';
        $command[] = 'configure';;;;;

        $composeService = new DockerComposeService($input, $output);
        $composeService->execute($command);
    }
}
