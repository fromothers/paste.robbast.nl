<?php

namespace Alcohol\PasteBundle\Command;

use Alcohol\PasteBundle\Entity\PasteManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CountCommand extends Command
{
    /** @var PasteManager */
    protected $manager;

    /**
     * @inheritDoc
     */
    public function __construct(PasteManager $manager)
    {
        parent::__construct();

        $this->manager = $manager;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('paste:count')
            ->setDescription('Returns a count of currently stored pasties.')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Current count of pasties: <info>%u</info>', $this->manager->getCount()));

        return 0;
    }
}