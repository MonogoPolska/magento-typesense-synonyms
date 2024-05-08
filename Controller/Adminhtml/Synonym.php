<?php

declare(strict_types=1);

namespace Monogo\TypesenseSynonyms\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Registry;

abstract class Synonym extends Action
{

    const ADMIN_RESOURCE = 'Monogo_Typesense::typesense';
    protected Registry $coreRegistry;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context  $context,
        Registry $coreRegistry
    )
    {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Init page
     *
     * @param Page $resultPage
     * @return Page
     */
    public function initPage(Page $resultPage): Page
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->getConfig()->getTitle()->prepend(__('Typesense Synonyms'));
        return $resultPage;
    }
}

