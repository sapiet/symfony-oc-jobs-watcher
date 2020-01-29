<?php

namespace App\Services;

use App\Entity\Jobs;
use App\Repository\JobsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class JobService
{
    const URL = 'https://mentor.openclassrooms.com';
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var
     */
    private $mailer;

    /**
     * @var JobsRepository
     */
    private $jobsRepository;

    /**
     * @var string
     */
    private $sender;

    public function __construct(
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        JobsRepository $jobsRepository,
        string $sender
    ) {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->jobsRepository = $jobsRepository;
        $this->sender = $sender;
    }

    public function watch()
    {
        $client = new Client();
        $crawler = $client->request('GET', self::URL);
        $crawler->filter('.job .title')->each(function (Crawler $node) {
            $this->handleJob(trim($node->text()));
        });
    }

    private function handleJob(string $job)
    {
        if (0 === $this->jobsRepository->count(['name' => $job])) {
            $job = (new Jobs())->setName($job);
            $this->entityManager->persist($job);
            $this->entityManager->flush();
        }
    }

    public function notify()
    {
        foreach ($this->jobsRepository->findBy(['sent_date' => null]) as $job) {
            $job->setSentDate(new \DateTimeImmutable());

            $this->mailer->send(
                (new Email())
                    ->from($this->sender)
                    ->to($this->sender)
                    ->subject(sprintf('[OCJobs] %s', $job->getName()))
                    ->text(sprintf(
                        'New OC job "%s" at %s',
                        $job->getName(),
                        $job->getCreationDate()->format('d/m/Y H:i:s')
                    ))
            );
        }

        $this->entityManager->flush();
    }
}
