<?php

namespace App\Command;

use App\Services\JobService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotifyCommand extends Command
{
    protected static $defaultName = 'app:notify';

    /**
     * @var JobService
     */
    private $jobService;

    public function __construct(
        string $name = null,
        JobService $jobService
    ) {
        parent::__construct($name);


        $this->jobService = $jobService;
    }

    protected function configure()
    {
        $this->setDescription('Notify unnotified new OC jobs');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->jobService->notify();

        return 0;
    }
}
