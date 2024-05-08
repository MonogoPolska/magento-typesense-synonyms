<?php
/**
 * @category   Monogo
 * @package    Monogo\TypesenseSynonyms
 * @author     Vladyslav Deyneko <vladyslav.deyneko@monogo.pl>
 * @copyright  Copyright (c) 2024 Monogo Sp. z o.o.
 */

declare(strict_types=1);

namespace Monogo\TypesenseSynonyms\Observer\Synonym;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Monogo\TypesenseSynonyms\Exception\SearchEngine\OperationFailedException;
use Monogo\TypesenseSynonyms\Model\Synonym;
use Monogo\TypesenseSynonyms\Services\Api\SynonymService;
use Psr\Log\LoggerInterface;

/**
 * Class SaveAfter
 * @since 1.0.0
 */
class Synchronize implements ObserverInterface
{
    private SynonymService $synonymService;

    private LoggerInterface $errorLogger;

    private ManagerInterface $uiMessageManager;

    /**
     * @param SynonymService   $synonymService
     * @param ManagerInterface $uiMessageManager
     * @param LoggerInterface  $errorLogger
     */
    public function __construct(
        SynonymService $synonymService,
        ManagerInterface $uiMessageManager,
        LoggerInterface $errorLogger
    ) {
        $this->synonymService   = $synonymService;
        $this->uiMessageManager = $uiMessageManager;
        $this->errorLogger      = $errorLogger;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var Synonym $savedEntity */
        $savedEntity = $observer->getEvent()->getData('data_object');

        try {
            $this->synonymService->upsert($savedEntity->getDataModel());

            $this->uiMessageManager->addSuccessMessage(
                'Successfully upsert synonym in search engine index.'
            );
        } catch (OperationFailedException $e) {
            $this->uiMessageManager->addErrorMessage(
                sprintf(
                    'Failed to upsert synonym in search engine index: %s',
                    $e->getMessage()
                )
            );

            $this->errorLogger->error(
                sprintf(
                    '[Observer] Failed to upsert synonym with name = %s: %s',
                    $savedEntity->getData()['external_id'],
                    $e->getMessage()
                )
            );
        }

        return $this;
    }
}
