<?php

namespace ShopwareDockerControl\Commands;

use Phar;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallDgLoggerCommand extends Command
{
    protected function configure()
    {
        $this->setName('install-dglogger')->setAliases(['dg'])->setDescription('Installs DgLogger to project base dir');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pharBasePath = dirname(dirname(dirname(__FILE__)));
        $srcDir = $pharBasePath . '/assets/DgLogger/';
        $template = file_get_contents($srcDir . 'phpstorm_template.txt');
        $loggerDest = getenv('PROJECT_BASE_DIR').'/DgLogger.php';
        $logger = file_get_contents($srcDir . 'DgLogger.php');
        file_put_contents($loggerDest, $logger);
        $output->writeln(sprintf(
            "<info>Copied DgLogger.php to %s</info>",
            $loggerDest
        ));
        $output->writeln("If you are using <info>PHPStorm</info>, use the following live template inside your projects for logging:");
        $output->writeln("");
        $output->writeln("$template");
        $output->writeln("");
        $output->writeln("Otherwise edit and copy the template code by hand where needed.");
    }
}
