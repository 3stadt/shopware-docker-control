<?php

namespace ShopwareDockerControl\Commands;

use ShopwareDockerControl\Services\DockerComposeService;
use ShopwareDockerControl\Services\ShellCommandRunnerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildUnitCommand extends Command
{
    protected function configure()
    {
        $this->setName('build-unit')->setAliases([
            'bu'
        ])
            ->setDescription('Perform ant build-unit on a project')
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
                'noSymlink',
                'nsl',
                InputOption::VALUE_NONE,
                'If set, no githool symlink will be created.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $input->getArgument('project');

        $command = ['docker-compose'];
        $command[] = 'run';
        $command[] = '-eANT_OPTS=-D"file.encoding=UTF-8"';
        $command[] = '-u' . ($input->getOption('userId'));
        $command[] = 'swag_cli';
        $command[] = 'ant';
        $command[] = '-f';
        $command[] = '/var/www/html/' . escapeshellcmd($project) . '/build/build.xml';
        $command[] = 'build-unit';

        $composeService = new DockerComposeService($input, $output);
        $composeService->execute($command);

        if ($input->getOption('noSymlink') || strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return;
        }

        $command = ['docker-compose'];
        $command[] = 'run';
        $command[] = '-eANT_OPTS=-D"file.encoding=UTF-8"';
        $command[] = '-u' . ($input->getOption('userId'));
        $command[] = 'swag_cli';
        $command[] = 'bash';
        $command[] = '-c';
        $command[] = '"ln';
        $command[] = '-sf';
        $command[] = '/var/www/html/'.$project.'/build/gitHooks/pre-commit';
        $command[] = '/var/www/html/'.$project.'/.git/hooks/pre-commit"';
        $composeService->execute($command);
    }
}
