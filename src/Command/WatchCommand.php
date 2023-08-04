<?php

namespace App\Command;

use App\Services\JobService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:watch',
    description: 'Watch for new jobs',
)]
class WatchCommand extends Command
{
    /**
     * @var JobService
     */
    private $jobService;

    public function __construct(
        JobService $jobService
    ) {
        $this->jobService = $jobService;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->jobService->watch();

        return Command::SUCCESS;
    }
}
