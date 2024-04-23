<?php

declare(strict_types=1);

namespace Monogo\TypesenseSynonyms\Controller\Adminhtml\Synonym;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Monogo\TypesenseSynonyms\Api\SynonymRepositoryInterface;
use Monogo\TypesenseSynonyms\Controller\Adminhtml\Synonym;

/**
 * Class Edit
 * @since 1.0.0
 */
class Edit extends Synonym
{
    private PageFactory $resultPageFactory;

    private SynonymRepositoryInterface $synonymRepository;

    /**
     * @param Context                    $context
     * @param Registry                   $coreRegistry
     * @param PageFactory                $resultPageFactory
     * @param SynonymRepositoryInterface $synonymRepository
     */
    public function __construct(
        Context          $context,
        Registry         $coreRegistry,
        PageFactory $resultPageFactory,
        SynonymRepositoryInterface $synonymRepository
    ) {
        parent::__construct($context, $coreRegistry);

        $this->resultPageFactory = $resultPageFactory;
        $this->synonymRepository = $synonymRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');

        if ($id) {
            try {
                $synonym = $this->synonymRepository->getById($id);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }

        $this->coreRegistry->register('typesense_synonyms_synonym', $synonym);

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id
                ? __('Edit Synonym')
                : __('New Synonym'),
            $id
                ? __('Edit Synonym')
                : __('New Synonym')
        );

        $resultPage->getConfig()->getTitle()->prepend(__('Synonyms'));
        $resultPage->getConfig()->getTitle()->prepend(
            $synonym->getId()
                ? __('Edit Synonym %1', $synonym->getId())
                : __('New Synonym')
        );

        return $resultPage;
    }
}
