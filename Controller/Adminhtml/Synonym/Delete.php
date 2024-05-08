<?php

declare(strict_types=1);

namespace Monogo\TypesenseSynonyms\Controller\Adminhtml\Synonym;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Registry;
use Monogo\TypesenseSynonyms\Api\SynonymRepositoryInterface;
use Monogo\TypesenseSynonyms\Controller\Adminhtml\Synonym;

/**
 * Class Delete
 * @since 1.0.0
 */
class Delete extends Synonym
{
    private SynonymRepositoryInterface $synonymRepository;

    /**
     * @inheritDoc
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        SynonymRepositoryInterface $synonymRepository
    ) {
        parent::__construct($context, $coreRegistry);

        $this->synonymRepository = $synonymRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            try {
                $this->synonymRepository->deleteById((int)$id);

                $this->messageManager->addSuccessMessage(__('Synonym has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('Synonym cannot be found.'));
        return $resultRedirect->setPath('*/*/');
    }
}
