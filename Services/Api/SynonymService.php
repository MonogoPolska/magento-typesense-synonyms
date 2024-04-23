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
use Typesense\Alias;

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
            'locale'           => $synonymEntity->getLocale(),
            'synonyms'         => $synonymEntity->getSynonymsList(),
            'symbols_to_index' => $synonymEntity->getIndexedSymbols()
        ];

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

        $assignedCollectionName = $this->getIndexNameByAlias($synonym->getAssignedCollection());

        try {
            $typesenseClient = $this->typesenseConfigurator->getClient();
            $synonymsSet = $typesenseClient->getCollections()[$assignedCollectionName]->getSynonyms();

            $persistedSynonymData = $synonymsSet->upsert($synonym->getExternalId(), $synonymData);
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
        try {
            $typesenseClient = $this->typesenseConfigurator->getClient();
            $synonymsSet = $typesenseClient->getCollections()[$synonym->getAssignedCollection()]->getSynonyms();

            return $synonymsSet[$synonym->getExternalId()]->delete()['id'];
        } catch (\Throwable $e) {
            throw new OperationFailedException(
                $e->getMessage()
            );
        }
    }
}
