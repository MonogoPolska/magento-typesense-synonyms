<?php

declare(strict_types=1);

namespace Monogo\TypesenseSynonyms\Model;

use Monogo\TypesenseSynonyms\Api\Data\SynonymInterface;
use Monogo\TypesenseSynonyms\Api\SynonymRepositoryInterface;
use Monogo\TypesenseSynonyms\Model\SynonymFactory;
use Monogo\TypesenseSynonyms\Model\ResourceModel\Synonym as ResourceModel;
use Monogo\TypesenseSynonyms\Model\ResourceModel\Synonym\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class SynonymRepository
 * @package Monogo\TypesenseSynonyms\Model
 */
class SynonymRepository implements SynonymRepositoryInterface
{
    /**
     * @var SynonymInterface[]
     */
    private array $instances = [];

    private ResourceModel $persistence;

    private CollectionFactory $entityCollectionFactory;

    private CollectionProcessorInterface $collectionProcessor;

    private SearchResultsInterfaceFactory $searchResultsFactory;

    private SynonymFactory $entityModelFactory;

    /**
     * QueueItemRepository constructor.
     *
     * @param ResourceModel                 $persistence
     * @param SynonymFactory                $entityModelFactory
     * @param CollectionFactory             $entityCollectionFactory
     * @param CollectionProcessorInterface  $collectionProcessor
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        ResourceModel $persistence,
        SynonymFactory $entityModelFactory,
        CollectionFactory $entityCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->persistence             = $persistence;
        $this->entityModelFactory      = $entityModelFactory;
        $this->searchResultsFactory    = $searchResultsFactory;
        $this->entityCollectionFactory = $entityCollectionFactory;
        $this->collectionProcessor     = $collectionProcessor;
    }

    /**
     * @param SynonymInterface $item
     *
     * @return SynonymInterface
     */
    private function cacheEntity(SynonymInterface $item): SynonymInterface
    {
        $this->instances[$item->getId()] = $item;

        return $item;
    }

    /**
     * @param SynonymInterface $item
     */
    private function removeFromCache(SynonymInterface $item): void
    {
        unset($this->instances[$item->getId()]);
    }

    /**
     * @inheritDoc
     */
    public function save(SynonymInterface $item): SynonymInterface
    {
        try {
            /** @var Synonym $entityModel */
            $entityModel = $this->entityModelFactory->create();
            $entityModel->addData($item->toArray());

            $this->persistence->save($entityModel);
        } catch (AlreadyExistsException $e) {
            $log = new \Monolog\Logger('custom', [new \Monolog\Handler\StreamHandler(BP.'/var/log/logger.log')]);
            $log->info('Value: ' . $e->getMessage());
            throw new CouldNotSaveException(__('Failed to save entity: %1', $e->getMessage()));
        } catch (\Exception $e) {
            $log = new \Monolog\Logger('custom', [new \Monolog\Handler\StreamHandler(BP.'/var/log/logger.log')]);
            $log->info('Value: ' . $e->getMessage());
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        $item->setId($entityModel->getId());

        $this->cacheEntity($item);

        return $item;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): SynonymInterface
    {
        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }

        /** @var Synonym $synonymEntity */
        $synonymEntity = $this->entityModelFactory->create();
        $this->persistence->load($synonymEntity, $id);
        $synonymDto = $synonymEntity->getDataModel();

        if ($synonymEntity->getId() == null) {
            throw NoSuchEntityException::singleField(SynonymInterface::FIELD_ENTITY_ID, $id);
        }

        $this->cacheEntity($synonymDto);

        return $synonymDto;
    }

    /**
     * @inheritDoc
     */
    public function getList(?SearchCriteriaInterface $searchCriteria = null): SearchResultsInterface
    {
        $collection = $this->entityCollectionFactory->create();

        /** @var SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();

        if ($searchCriteria != null) {
            $this->collectionProcessor->process($searchCriteria, $collection);
            $searchResults->setSearchCriteria($searchCriteria);
        }

        $searchResults->setItems($collection->getItems())
            ->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(SynonymInterface $item): bool
    {
        try {
            $itemModel = $this->entityModelFactory->create();
            $this->persistence->load($itemModel, $item->getId());

            $this->persistence->delete($itemModel);
            $this->removeFromCache($item);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __('Failed to delete entity with ID = %1. Error: %2', $item->getId(), $e->getMessage())
            );
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $id): bool
    {
        return $this->delete($this->getById($id));
    }
}
