<?php

declare(strict_types=1);

namespace Monogo\TypesenseSynonyms\Model;

use Monogo\TypesenseSynonyms\Api\Data\SynonymInterface;
use Monogo\TypesenseSynonyms\Api\Data\SynonymInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Class Synonym
 * @package Monogo\TypesenseSynonyms\Model
 */
class Synonym extends AbstractModel
{
    const CACHE_TAG = 'monogo_typesensesynonyms_synonym';

    protected $_cacheTag = 'monogo_typesensesynonyms_synonym';

    protected $_eventPrefix = 'monogo_typesensesynonyms_synonym';

    private DataObjectHelper $dataObjectHelper;

    private SynonymInterfaceFactory $entityDtoFactory;

    /**
     * Session constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param DataObjectHelper $dataObjectHelper
     * @param SynonymInterfaceFactory $entityDtoFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DataObjectHelper $dataObjectHelper,
        SynonymInterfaceFactory $entityDtoFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->dataObjectHelper = $dataObjectHelper;
        $this->entityDtoFactory = $entityDtoFactory;
    }

    /**
     * Tag constructor
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Synonym::class);
    }

    /**
     * Retrieves entity data model
     * @return SynonymInterface
     */
    public function getDataModel(): SynonymInterface
    {
        $rawData = $this->getData();

        /** @var SynonymInterface $entityDto */
        $entityDto = $this->entityDtoFactory->create();

        $this->dataObjectHelper->populateWithArray(
            $entityDto,
            $rawData,
            SynonymInterface::class
        );

        return $entityDto;
    }
}
