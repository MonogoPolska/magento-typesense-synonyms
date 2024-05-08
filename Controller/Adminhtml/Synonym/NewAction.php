<?php

declare(strict_types=1);

namespace Monogo\TypesenseSynonyms\Controller\Adminhtml\Synonym;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Monogo\TypesenseSynonyms\Controller\Adminhtml\Synonym;

class NewAction extends Synonym
{
    private ForwardFactory $resultForwardFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context        $context,
        Registry       $coreRegistry,
        ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context, $coreRegistry);
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}

