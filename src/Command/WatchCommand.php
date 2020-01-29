<?php

namespace App\Command;

use App\Services\JobService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WatchCommand extends Command
{
    protected static $defaultName = 'app:watch';

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
        $this->setDescription('Watch for new OC jobs');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->jobService->watch();

        return 0;
    }
}
