<?php

declare(strict_types=1);

namespace Monogo\TypesenseSynonyms\Console\Command;

use Magento\Framework\Console\Cli;
use Monogo\TypesenseSynonyms\Exception\SearchEngine\OperationFailedException;
use Monogo\TypesenseSynonyms\Model\SynonymManagement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Flush extends Command
{
    private SynonymManagement $synonymManagement;

    /**
     * @param SynonymManagement $synonymManagement
     * @param string|null $name
     */
    public function __construct(
        SynonymManagement $synonymManagement,
        ?string $name = null
    ) {
        parent::__construct($name);
        $this->synonymManagement = $synonymManagement;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('typesense:synonyms:flush');
        $this->setDescription('Flush all synonyms and synonym sets from Typesense');

        $this->addOption(
            'orphan-only',
            null,
            InputOption::VALUE_NONE,
            'Only remove orphaned synonym sets (not linked to any collection); database records are kept'
        );

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $orphanOnly = (bool)$input->getOption('orphan-only');

        try {
            $result = $this->synonymManagement->flushAll($orphanOnly);
        } catch (OperationFailedException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Cli::RETURN_FAILURE;
        }

        if ($result['sets'] === 0 && $result['entities'] === 0) {
            $output->writeln('Nothing to flush — all clean.');
            return Cli::RETURN_SUCCESS;
        }

        $output->writeln(sprintf('Removed %d synonym set(s) from Typesense.', $result['sets']));

        if (!$orphanOnly) {
            $output->writeln(sprintf('Removed %d synonym entity(ies) from database.', $result['entities']));
        }

        return Cli::RETURN_SUCCESS;
    }
}
