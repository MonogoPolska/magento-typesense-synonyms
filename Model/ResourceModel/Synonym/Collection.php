<?php

declare(strict_types=1);

namespace Monogo\TypesenseSynonyms\Model\ResourceModel\Synonym;

use Monogo\TypesenseSynonyms\Model;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'monogo_typesensesynonyms_synonym_collection';
    protected $_eventObject = 'monogo_typesensesynonyms_synonym_collection';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(Model\Synonym::class, Model\ResourceModel\Synonym::class);
    }
}
