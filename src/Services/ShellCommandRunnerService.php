<?php

namespace ShopwareDockerControl\Services;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ShellCommandRunnerService
{
    /** @var InputInterface */
    private $input;
    /** @var OutputInterface */
    private $output;
    private $processTimeout = 3600;
    private $processIdleTimeout = 120;
    private $tty = false;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * Executes the given command inside the given working directory.
     * This method uses the symfony Process component and outputs all
     * console messages directly.
     *
     * @param string $commandString
     * @param string $workingDir
     */
    public function execute($commandString, $workingDir)
    {
        $output = $this->output;
        $this->output->writeln('<info>' . "Switching to directory:\n$workingDir" . '</info>');
        $this->output->writeln('<info>' . "Executing command:\n$commandString\n" . '</info>');

        $phpVersion = $this->input->getOption('php-version');
        $testHost = $this->input->getOption('test-host');

        $phpVersion = empty($phpVersion) ? '7' : $phpVersion;
        $testHost = empty($testHost) ? 'shopware.localhost' : $testHost;

        $process = new Process($commandString, $workingDir);
        $process->setTimeout($this->processTimeout);
        $process->setIdleTimeout($this->processIdleTimeout);
        $process->setTty($this->tty);
        $process->setEnv([
            'SWDOCKER_PHP_VERSION' => $phpVersion,
            'SWDOCKER_TEST_HOST' => $testHost,
            'SWDOCKER_VARNISH' => $this->input->getOption('use-varnish'),
            'SWDOCKER_IONCUBE' => $this->input->getOption('use-ioncube'),
            'SWDOCKER_XDEBUG' => $this->input->getOption('use-xdebug'),
        ]);
        $process->run(function ($type, $buffer) use ($output) {
            $message = trim($buffer);
            if (empty($buffer)) {
                return;
            }
            if (Process::ERR === $type) {
                $output->writeln('<fg=red>' . $message . '</>');
            } else {
                $output->writeln('<info>' . $message . '</info>');
            }
        });
    }

    public function setTimeout($seconds)
    {
        $this->processTimeout = $seconds;
        return $this;
    }

    public function setIdleTimeout($seconds)
    {
        $this->processIdleTimeout = $seconds;
        return $this;
    }

    public function setTty($bool)
    {
        $this->tty = $bool;
    }
}
