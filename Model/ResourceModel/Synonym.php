<?php

declare(strict_types=1);

namespace Monogo\TypesenseSynonyms\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Synonym
 * @package Monogo\TypesenseSynonyms\Model\ResourceModel
 */
class Synonym extends AbstractDb
{
    const MAIN_TABLE = 'typesense_synonym';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(static::MAIN_TABLE, 'id');
    }
}
