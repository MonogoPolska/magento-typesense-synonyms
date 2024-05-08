<?php
/**
 * @category   Monogo
 * @package    Monogo\TypesenseSynonyms
 * @author     Vladyslav Deyneko <vladyslav.deyneko@monogo.pl>
 * @copyright  Copyright (c) 2024 Monogo Sp. z o.o.
 */

declare(strict_types=1);

namespace Monogo\TypesenseSynonyms\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Monogo\TypesenseSynonyms\Model\SynonymManagement;
use Monogo\TypesenseSynonyms\Services\Api\SynonymService;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class HandleFlush
 * @since 1.0.0
 */
class HandleFlush implements ObserverInterface
{
    private SynonymManagement $synonymManagement;

    public function __construct(SynonymManagement $synonymManagement)
    {
        $this->synonymManagement = $synonymManagement;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var OutputInterface $output */
        $output = $observer->getData('output');

        $removedEntitiesCount = $this->synonymManagement->flush();

        $output->writeln(
            sprintf(
                'Synonyms storage have been flushed. Number of removed entities: %s',
                $removedEntitiesCount
            )
        );
    }
}
