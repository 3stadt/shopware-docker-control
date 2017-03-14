<?php

namespace ShopwareDockerControl\Services;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class DockerComposeService
{

    /** @var InputInterface  */
    private $input;
    /** @var OutputInterface  */
    private $output;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * Creates a new ShellCommandRunnerService and executes the command inside DOCKER_BASE_DIR
     *
     * @param array $command
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Process\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Process\Exception\LogicException
     */
    public function execute(array $command)
    {
        $shellService = new ShellCommandRunnerService($this->input, $this->output);
        $shellService->execute(implode(' ', $command), getenv('DOCKER_BASE_DIR'));
    }

    public function executeInteractive($command)
    {
        $shellService = new ShellCommandRunnerService($this->input, $this->output);
        $shellService->setTty(true);
        $shellService->execute(implode(' ', $command), getenv('DOCKER_BASE_DIR'));
    }
}
