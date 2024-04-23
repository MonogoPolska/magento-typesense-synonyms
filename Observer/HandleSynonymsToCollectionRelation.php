<?php
/**
 * @category   Monogo
 * @package    Monogo\TypesenseSynonyms
 * @author     Vladyslav Deyneko <vladyslav.deyneko@monogo.pl>
 * @copyright  Copyright (c) 2024 Monogo Sp. z o.o.
 */

declare(strict_types=1);

namespace Monogo\TypesenseSynonyms\Observer;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Monogo\TypesenseSynonyms\Api\Data\SynonymInterface;
use Monogo\TypesenseSynonyms\Api\SynonymRepositoryInterface;
use Monogo\TypesenseSynonyms\Exception\SearchEngine\OperationFailedException;
use Monogo\TypesenseSynonyms\Model\SynonymManagement;
use Psr\Log\LoggerInterface;

class HandleSynonymsToCollectionRelation implements ObserverInterface
{
    private SynonymManagement $synonymManagement;

    private LoggerInterface $errorLogger;

    /**
     * @param SynonymManagement $synonymManagement
     * @param LoggerInterface   $errorLogger
     */
    public function __construct(
        SynonymManagement $synonymManagement,
        LoggerInterface $errorLogger
    ) {
        $this->synonymManagement = $synonymManagement;
        $this->errorLogger       = $errorLogger;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $alias = $observer->getData('alias');
        $collection = $observer->getData('collection');

        try {
            $this->synonymManagement->reassignCollection($alias);
        } catch (OperationFailedException $e) {
        }

        $log = new \Monolog\Logger('custom', [new \Monolog\Handler\StreamHandler(BP.'/var/log/logger.log')]);
        $log->info('Al: ' . $alias);
        $log->info('Col: ' . $collection);
    }
}
