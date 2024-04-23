<?php

declare(strict_types=1);

namespace Monogo\TypesenseSynonyms\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Monogo\TypesenseSynonyms\Api\Data\SynonymInterface;
use voku\helper\DomParserInterface;

/**
 * Class Synonym
 * @package Monogo\TypesenseSynonyms\Model\ResourceModel
 */
class Synonym extends AbstractDb
{
    const MAIN_TABLE = 'typesense_synonym';

    protected $_serializableFields = [
        SynonymInterface::FIELD_INDEXED_SYMBOLS => [[],[]],
        SynonymInterface::FIELD_SYNONYMS_LIST => [[],[]]
    ];

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(static::MAIN_TABLE, 'id');
    }
}
