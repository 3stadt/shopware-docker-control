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

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @param string $comandString
     * @param string $workingDir
     */
    public function execute($comandString, $workingDir)
    {
        $output = $this->output;
        $process = new Process($comandString, $workingDir);
        $process->setTimeout($this->processTimeout);
        $process->setIdleTimeout($this->processIdleTimeout);
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
}
