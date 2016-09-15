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
     */
    public function execute(array $command)
    {
        $shellService = new ShellCommandRunnerService($this->input, $this->output);
        $shellService->execute(implode(' ', $command), getenv('DOCKER_BASE_DIR'));
    }
}
