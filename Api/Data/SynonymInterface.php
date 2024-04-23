<?php

/** @noinspection PhpFullyQualifiedNameUsageInspection */

namespace Monogo\TypesenseSynonyms\Api\Data;

/**
 * Interface SynonymInterface
 * @package Monogo\TypesenseSynonyms\Api\Data
 */
interface SynonymInterface
{
    const FIELD_ENTITY_ID  = 'id';
    const FIELD_EXTERNAL_ID      = 'external_id';
    const FIELD_TYPE             = 'type';
    const FIELD_SYNONYMS_LIST    = 'synonyms_list';
    const FIELD_ROOT_PHRASE      = 'root_phrase';
    const FIELD_LOCALE           = 'locale';
    const FIELD_COLLECTION_ALIAS = 'collection_alias';
    const FIELD_INDEXED_SYMBOLS  = 'indexed_symbols';
    const FIELD_CREATED_AT       = 'created_at';
    const FIELD_UPDATED_AT       = 'updated_at';

    const TYPE_ONE_WAY      = 1;
    const TYPE_MULTI_WAY    = 2;

    /**
     * @return string|int
     */
    public function getId();

    /**
     * @param string|int $id
     *
     * @return \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface
     */
    public function setId($id): \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface;

    /**
     * @return string
     */
    public function getExternalId(): string;

    /**
     * @param string $externalId
     *
     * @return \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface
     */
    public function setExternalId(string $externalId): \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface;

    /**
     * @return int
     */
    public function getType(): int;

    /**
     * @param int $type
     *
     * @return \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface
     */
    public function setType(int $type): \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface;

    /**
     * @return string
     */
    public function getRootPhrase(): string;

    /**
     * @param string $phrase
     *
     * @return SynonymInterface
     */
    public function setRootPhrase(string $phrase = ''): \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface;

    /**
     * @return string
     */
    public function getLocale():string;

    /**
     * @param string|null $localeCode
     *
     * @return SynonymInterface
     */
    public function setLocale(?string $localeCode = ''): \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface;

    /**
     * @return string[]
     */
    public function getSynonymsList(): array;

    /**
     * @param string[] $list
     *
     * @return SynonymInterface
     */
    public function setSynonymsList(array $list): \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface;

    /**
     * @return string
     */
    public function getAssignedCollection(): string;

    /**
     * @param string $collectionAlias
     *
     * @return SynonymInterface
     */
    public function setAssignedCollection(string $collectionAlias): \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface;

    /**
     * @return string[]
     */
    public function getIndexedSymbols(): array;

    /**
     * @param string[] $symbolList
     *
     * @return SynonymInterface
     */
    public function setIndexedSymbols(array $symbolList): \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface;

    /**
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * @return string
     */
    public function getUpdatedAt(): string;
}
