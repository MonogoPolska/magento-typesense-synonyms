<?php

/** @noinspection PhpFullyQualifiedNameUsageInspection */

namespace Monogo\TypesenseSynonyms\Api;

/**
 * Interface SynonymRepositoryInterface
 * @package Monogo\TypesenseSynonyms\Api
 */
interface SynonymRepositoryInterface
{
    /**
     * @param \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface $item
     *
     * @return \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Monogo\TypesenseSynonyms\Api\Data\SynonymInterface $item): \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface;

    /**
     * @param int $id
     *
     * @return \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id): \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(?\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null): \Magento\Framework\Api\SearchResultsInterface;

    /**
     * @param \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface $item
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function delete(\Monogo\TypesenseSynonyms\Api\Data\SynonymInterface $item): bool;

    /**
     * @param int $id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById(int $id): bool;
}
