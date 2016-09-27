<?php

namespace ShopwareDockerControl\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListShopwareDirectoriesCommand extends Command
{
    protected function configure()
    {
        $this->setName('find-shopware')
            ->setAliases(['fs'])
            ->setDescription('Shows a list of assumed showpare installations in your configured project base dir')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectBaseDir = getenv('PROJECT_BASE_DIR');
        $directoryContent = array_diff(scandir($projectBaseDir), array('..', '.'));

        $output->writeln('<info>The following folders in your project base dir contain a shopware.php file:</info>');

        foreach ($directoryContent as $item) {
            $path = $projectBaseDir.DIRECTORY_SEPARATOR.$item;
            if ($this->isShopwarePath($path)) {
                $output->writeln($item);
            }
        }
    }

    private function isShopwarePath($path)
    {
        return is_dir($path) && file_exists($path.DIRECTORY_SEPARATOR.'shopware.php');
    }
}
