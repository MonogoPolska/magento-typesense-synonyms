<?php
/**
 * @category   Monogo
 * @package    Monogo\TypesenseSynonyms
 * @author     Vladyslav Deyneko <vladyslav.deyneko@monogo.pl>
 * @copyright  Copyright (c) 2024 Monogo Sp. z o.o.
 */

declare(strict_types=1);

namespace Monogo\TypesenseSynonyms\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Monogo\TypesenseSynonyms\Api\Data\SynonymInterface;
use Monogo\TypesenseSynonyms\Api\SynonymRepositoryInterface;
use Monogo\TypesenseSynonyms\Exception\SearchEngine\OperationFailedException;
use Monogo\TypesenseSynonyms\Services\Api\SynonymService;
use Psr\Log\LoggerInterface;

/**
 * Class SynonymManagement
 * @since 1.0.0
 */
class SynonymManagement
{
    private SynonymService $synonymService;
    private SynonymRepositoryInterface $synonymRepository;
    private LoggerInterface $errorLogger;
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    public function __construct(
        SynonymService $synonymService,
        SynonymRepositoryInterface $synonymRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $errorLogger
    ) {
        $this->synonymService        = $synonymService;
        $this->synonymRepository     = $synonymRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->errorLogger           = $errorLogger;
    }

    /**
     * @return int
     */
    public function flush(): int
    {
        $synonymCollection = $this->synonymRepository->getList();
        $numberOfRemovedEntities = 0;

        foreach ($synonymCollection->getItems() as $synonymData) {
            /** @var Synonym $synonymData */
            try {
                $this->synonymRepository->deleteById((int)$synonymData->getId());
                $this->synonymService->delete($synonymData->getDataModel());
                $numberOfRemovedEntities++;
            } catch (OperationFailedException|CouldNotDeleteException|NoSuchEntityException $e) {
                $this->errorLogger->error(
                    sprintf(
                        'Failed to remove synonym in TS engine: %s',
                        $e->getMessage()
                    )
                );
            }
        }

        return $numberOfRemovedEntities;
    }

    /**
     * Flushes synonym sets from Typesense only. Magento database records are never touched.
     *
     * @param bool $orphanOnly When true, only orphaned synonym sets (not linked to any
     *                         collection) are removed.
     *
     * @return int Number of removed synonym sets.
     */
    public function flushAll(bool $orphanOnly = false): int
    {
        return $this->synonymService->deleteAllSynonymSets($orphanOnly);
    }

    /**
     * @param string $targetCollectionAlias
     *
     * @return array
     * @throws OperationFailedException
     */
    public function reassignCollection(string $targetCollectionAlias): array
    {
        $synonymsToReassign = $this->synonymRepository->getList(
            $this->searchCriteriaBuilder->addFilter(
                SynonymInterface::FIELD_ASSIGNED_COLLECTION,
                $targetCollectionAlias
            )->create()
        );

        $items = $synonymsToReassign->getItems();
        if (!empty($items)) {
            $dataModels = [];
            foreach ($items as $item) {
                /** @var Synonym $item */
                $dataModels[] = $item->getDataModel();
            }
            $this->synonymService->batchUpsert($dataModels, $targetCollectionAlias);
        }

        return $items;
    }
}
