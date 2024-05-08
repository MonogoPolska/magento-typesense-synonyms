<?php
/**
 * @category   Monogo
 * @package    Monogo\TypesenseSynonyms
 * @author     Vladyslav Deyneko <vladyslav.deyneko@monogo.pl>
 * @copyright  Copyright (c) 2024 Monogo Sp. z o.o.
 */

declare(strict_types=1);

namespace Monogo\TypesenseSynonyms\Model\Data;

use Magento\Framework\DataObject;
use Monogo\TypesenseSynonyms\Api\Data\SynonymInterface;

/**
 * Class Synonym
 * @since 1.0.0
 */
class Synonym extends DataObject implements SynonymInterface
{
    /**
     * @inheritDoc
     */
    public function getId()
    {
        return (int)$this->_getData(SynonymInterface::FIELD_ENTITY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setId($id): SynonymInterface
    {
        $this->setData(SynonymInterface::FIELD_ENTITY_ID, $id);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExternalId(): string
    {
        return (string)$this->_getData(SynonymInterface::FIELD_EXTERNAL_ID);
    }

    /**
     * @inheritDoc
     */
    public function setExternalId(string $externalId): SynonymInterface
    {
        $this->setData(SynonymInterface::FIELD_EXTERNAL_ID, $externalId);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getType(): int
    {
        return (int)$this->_getData(SynonymInterface::FIELD_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType(int $type): \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface
    {
        $this->setData(SynonymInterface::FIELD_TYPE, $type);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRootPhrase(): string
    {
        return (string)$this->_getData(SynonymInterface::FIELD_ROOT_PHRASE);
    }

    /**
     * @inheritDoc
     */
    public function setRootPhrase(string $phrase = ''): \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface
    {
        $this->setData(SynonymInterface::FIELD_ROOT_PHRASE, $phrase);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLocale(): string
    {
        return (string)$this->_getData(SynonymInterface::FIELD_LOCALE);
    }

    /**
     * @inheritDoc
     */
    public function setLocale(?string $localeCode = ''): \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface
    {
        $this->setData(SynonymInterface::FIELD_LOCALE, $localeCode);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSynonymsList(): string
    {
        return $this->_getData(SynonymInterface::FIELD_SYNONYMS_LIST);
    }

    /**
     * @inheritDoc
     */
    public function setSynonymsList(string $list): \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface
    {
        $this->setData(SynonymInterface::FIELD_SYNONYMS_LIST, $list);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAssignedCollection(): string
    {
        return (string)$this->_getData(SynonymInterface::FIELD_ASSIGNED_COLLECTION);
    }

    /**
     * @inheritDoc
     */
    public function setAssignedCollection(string $collectionAlias): \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface
    {
        $this->setData(SynonymInterface::FIELD_ASSIGNED_COLLECTION, $collectionAlias);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIndexedSymbols(): string
    {
        return (string)$this->_getData(SynonymInterface::FIELD_INDEXED_SYMBOLS);
    }

    /**
     * @inheritDoc
     */
    public function setIndexedSymbols(?string $symbolList): \Monogo\TypesenseSynonyms\Api\Data\SynonymInterface
    {
        $this->setData(SynonymInterface::FIELD_INDEXED_SYMBOLS, $symbolList);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): string
    {
        return (string)$this->_getData(SynonymInterface::FIELD_CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): string
    {
        return (string)$this->_getData(SynonymInterface::FIELD_UPDATED_AT);
    }
}
