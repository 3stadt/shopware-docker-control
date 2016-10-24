<?php

namespace ShopwareDockerControl\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RestartContainersCommand extends Command
{
    protected function configure()
    {
        $this->setName('restart')
            ->setAliases(['rs'])
            ->setDescription('Executes `swdc stop` followed by `swdc up`')
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
                'If set, containers will be destroyed on stop.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $arguments = [];

        if ($input->getOption('testing')) {
            $arguments['-t'] = true;
        }

        $down = $this->getApplication()->find('stop');
        $downArguments = array_merge($arguments, ['command' => 'stop']);
        if ($input->getOption('down')) {
            $downArguments = array_merge($downArguments, ['-d' => true]);
        }
        $downInput = new ArrayInput($downArguments);
        $returnCode = $down->run($downInput, $output);

        if ($returnCode !== 0) {
            return;
        }

        $up = $this->getApplication()->find('up');
        $upArguments = array_merge($arguments, ['command' => 'up']);
        $upInput = new ArrayInput($upArguments);
        $up->run($upInput, $output);
    }
}
