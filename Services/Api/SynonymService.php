<?php
/**
 * @category   Monogo
 * @package    Monogo\TypesenseSynonyms
 * @author     Vladyslav Deyneko <vladyslav.deyneko@monogo.pl>
 * @copyright  Copyright (c) 2024 Monogo Sp. z o.o.
 */

declare(strict_types=1);

namespace Monogo\TypesenseSynonyms\Services\Api;

use Monogo\TypesenseCore\Adapter\Client;
use Monogo\TypesenseSynonyms\Api\Data\SynonymInterface;
use Monogo\TypesenseSynonyms\Exception\SearchEngine\OperationFailedException;

/**
 * Class SynonymService
 * @since 1.0.0
 */
class SynonymService
{
    private Client $typesenseConfigurator;

    public function __construct(Client $typesenseConfigurator)
    {
        $this->typesenseConfigurator = $typesenseConfigurator;
    }

    /**
     * @param string $alias
     *
     * @return string|null
     * @throws OperationFailedException
     */
    private function getIndexNameByAlias(string $alias): ?string
    {
        if (empty($alias)) {
            throw new OperationFailedException('Alias is empty');
        }

        try {
            /** @var Alias $alias */
            $collectionAlias = $this->typesenseConfigurator->getClient()->getAliases()[$alias]->retrieve();

            return $collectionAlias['collection_name'];
        } catch (\Throwable $e) {
            throw new OperationFailedException(
                sprintf(
                    'Failed to retrieve collection by alias = %s. Reason: %s',
                    $alias,
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * @param SynonymInterface $synonymEntity
     *
     * @return array
     */
    private function mapToArray(SynonymInterface $synonymEntity): array
    {
        $synonymData = [
            'locale'   => $synonymEntity->getLocale(),
            'synonyms' => explode(',', $synonymEntity->getSynonymsList())
        ];

//        var_dump($synonymEntity->getIndexedSymbols());
//        var_dump(!empty($synonymEntity->getIndexedSymbols()));
//        die;

        if (!empty($synonymEntity->getIndexedSymbols())) {
            $synonymData['symbols_to_index'] = explode(',', $synonymEntity->getIndexedSymbols());
        }

        if ($synonymEntity->getType() == SynonymInterface::TYPE_ONE_WAY) {
            $synonymData['root'] = $synonymEntity->getRootPhrase();
        }

        return $synonymData;
    }

    /**
     * @param SynonymInterface $synonym
     *
     * @throws OperationFailedException
     */
    public function upsert(SynonymInterface $synonym): array
    {
        $synonymData = $this->mapToArray($synonym);
        $synonymData['id'] = $synonym->getExternalId();

        $assignedCollectionName = $this->getIndexNameByAlias($synonym->getAssignedCollection());
        $synonymSetName = $assignedCollectionName . '_synonyms_index';

        try {
            $typesenseClient = $this->typesenseConfigurator->getClient();

            $currentItems = $this->getSynonymSetItems($typesenseClient, $synonymSetName);
            $currentItems = $this->upsertItemInArray($currentItems, $synonymData);

            $persistedSynonymData = $typesenseClient->getSynonymSets()->upsert(
                $synonymSetName,
                ['items' => $currentItems]
            );
            $this->ensureSynonymSetLinked($typesenseClient, $assignedCollectionName, $synonymSetName);
        } catch (\Throwable $e) {
            throw new OperationFailedException(
                $e->getMessage()
            );
        }

        return $persistedSynonymData;
    }

    /**
     * Removes specified synonym from the search engine index.
     *
     * @param SynonymInterface $synonym
     *
     * @return string External ID of the removed entry.
     * @throws OperationFailedException
     */
    public function delete(SynonymInterface $synonym): string
    {
        $assignedCollectionName = $this->getIndexNameByAlias($synonym->getAssignedCollection());
        $synonymSetName = $assignedCollectionName . '_synonyms_index';

        try {
            $typesenseClient = $this->typesenseConfigurator->getClient();

            $currentItems = $this->getSynonymSetItems($typesenseClient, $synonymSetName);
            $currentItems = array_values(array_filter(
                $currentItems,
                fn($item) => ($item['id'] ?? '') !== $synonym->getExternalId()
            ));

            $typesenseClient->getSynonymSets()->upsert(
                $synonymSetName,
                ['items' => $currentItems]
            );

            return $synonym->getExternalId();
        } catch (\Throwable $e) {
            throw new OperationFailedException(
                $e->getMessage()
            );
        }
    }

    private function getSynonymSetItems($typesenseClient, string $synonymSetName): array
    {
        try {
            $set = $typesenseClient->getSynonymSets()[$synonymSetName]->retrieve();
            return $set['items'] ?? [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function upsertItemInArray(array $items, array $newItem): array
    {
        foreach ($items as $key => $item) {
            if (($item['id'] ?? '') === $newItem['id']) {
                $items[$key] = $newItem;
                return $items;
            }
        }
        $items[] = $newItem;
        return $items;
    }

    private function ensureSynonymSetLinked($typesenseClient, string $collectionName, string $synonymSetName): void
    {
        try {
            $collection = $typesenseClient->collections[$collectionName]->retrieve();
            $linkedSets = $collection['synonym_sets'] ?? [];
            if (!in_array($synonymSetName, $linkedSets)) {
                $linkedSets[] = $synonymSetName;
                $typesenseClient->collections[$collectionName]->update([
                    'synonym_sets' => $linkedSets
                ]);
            }
        } catch (\Throwable $e) {
            // Collection might not support linking yet
        }
    }
}
