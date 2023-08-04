<?php

namespace App\Services;

use App\Entity\Jobs;
use App\Repository\JobsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class JobService
{
    const URL = 'https://mentors.openclassrooms.com/fr/jobs';
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
        $client = new HttpBrowser();
        $crawler = $client->request('GET', self::URL);
        $crawler->filter('body > main > section > div > div > ul > li')->each(function (Crawler $node) {
            dump(trim($node->text()));
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
        foreach ($this->jobsRepository->findBy(['sentDate' => null]) as $job) {
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
