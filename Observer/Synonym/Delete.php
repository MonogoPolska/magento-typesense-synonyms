<?php

declare(strict_types=1);

namespace Monogo\TypesenseSynonyms\Observer\Synonym;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Monogo\TypesenseSynonyms\Exception\SearchEngine\OperationFailedException;
use Monogo\TypesenseSynonyms\Model\Synonym;
use Monogo\TypesenseSynonyms\Services\Api\SynonymService;
use Psr\Log\LoggerInterface;

class Delete implements ObserverInterface
{
    private SynonymService $synonymService;

    private LoggerInterface $errorLogger;

    private ManagerInterface $uiMessageManager;

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
        /** @var Synonym $deletedEntity */
        $deletedEntity = $observer->getEvent()->getData('data_object');

        try {
            $this->synonymService->delete($deletedEntity->getDataModel());

            $this->uiMessageManager->addSuccessMessage(
                'Successfully removed synonym from search engine index.'
            );
        } catch (OperationFailedException $e) {
            $this->uiMessageManager->addErrorMessage(
                sprintf(
                    'Failed to remove synonym from search engine index: %s',
                    $e->getMessage()
                )
            );

            $this->errorLogger->error(
                sprintf(
                    '[Observer] Failed to delete synonym with external_id = %s: %s',
                    $deletedEntity->getData()['external_id'] ?? 'unknown',
                    $e->getMessage()
                )
            );
        }

        return $this;
    }
}
