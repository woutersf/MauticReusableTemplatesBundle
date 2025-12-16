<?php

declare(strict_types=1);

namespace MauticPlugin\MauticReusableTemplatesBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use MauticPlugin\MauticReusableTemplatesBundle\Entity\ReusableTemplate;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProcessChangedTemplatesCommand extends Command
{
    protected static $defaultName = 'mautic:reusabletemplates:process';

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Process changed reusable templates and update emails')
            ->setHelp('This command processes all templates marked as changed and updates the content in all emails using those templates.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Find all templates that have been changed
        $repository = $this->entityManager->getRepository(ReusableTemplate::class);
        $changedTemplates = $repository->findBy(['changed' => true]);

        if (empty($changedTemplates)) {
            $io->success('No changed templates to process.');
            return Command::SUCCESS;
        }

        $io->note(sprintf('Found %d changed template(s) to process.', count($changedTemplates)));

        foreach ($changedTemplates as $template) {
            $io->text(sprintf('Processing template: %s (ID: %d)', $template->getName(), $template->getId()));

            // TODO: Implement the logic to update emails
            // 1. Find all emails that contain this template's content
            // 2. str_replace the old content with the new content from the template
            // 3. Save the updated emails

            // For now, we just reset the changed flag
            $template->setChanged(false);
            $this->entityManager->persist($template);

            $io->comment('TODO: Select all emails content and str_replace the existing parts with the new one.');
        }

        $this->entityManager->flush();

        $io->success(sprintf('Processed %d template(s) successfully.', count($changedTemplates)));

        return Command::SUCCESS;
    }
}
