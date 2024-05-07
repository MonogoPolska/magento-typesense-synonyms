<?php

declare(strict_types=1);

namespace Monogo\TypesenseSynonyms\Controller\Adminhtml\Synonym;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect as RedirectAlias;
use Magento\Framework\App\Request\DataPersistorInterface as DataPersistorInterfaceAlias;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Monogo\TypesenseSynonyms\Api\Data\SynonymInterface;
use Monogo\TypesenseSynonyms\Api\Data\SynonymInterfaceFactory;
use Monogo\TypesenseSynonyms\Api\SynonymRepositoryInterface;

/**
 * Class Save
 * @since 1.0.0
 */
class Save extends Action
{
    private DataPersistorInterfaceAlias $dataPersistor;

    private SynonymRepositoryInterface $synonymRepository;

    private SynonymInterfaceFactory $synonymFactory;

    /**
     * @param Context                     $context
     * @param DataPersistorInterfaceAlias $dataPersistor
     * @param SynonymRepositoryInterface  $synonymRepository
     * @param SynonymInterfaceFactory     $synonymFactory
     */
    public function __construct(
        Context                     $context,
        DataPersistorInterfaceAlias $dataPersistor,
        SynonymRepositoryInterface $synonymRepository,
        SynonymInterfaceFactory $synonymFactory
    ) {
        parent::__construct($context);

        $this->dataPersistor     = $dataPersistor;
        $this->synonymRepository = $synonymRepository;
        $this->synonymFactory    = $synonymFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var RedirectAlias $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        $id = (int)$this->getRequest()->getParam('id');

        try {
            $synonymDto = $this->synonymRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            if ($id > 0) {
                $this->messageManager->addErrorMessage(__('This Synonym no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            /** @var SynonymInterface $synonymDto */
            $synonymDto = $this->synonymFactory->create();
        }

        unset($data['form_key']);
        $synonymDto->setData($data);
//        $synonymDto->setData(
//            SynonymInterface::FIELD_SYNONYMS_LIST,
//            explode(',', $data[SynonymInterface::FIELD_SYNONYMS_LIST])
//        );
//        $synonymDto->setData(
//            SynonymInterface::FIELD_INDEXED_SYMBOLS,
//            (string)$data[SynonymInterface::FIELD_INDEXED_SYMBOLS]
//        );

        try {
            $persistedEntity = $this->synonymRepository->save($synonymDto);
            $this->messageManager->addSuccessMessage(__('Synonym have been saved.'));
            $this->dataPersistor->clear('typesense_synonyms_synonym');

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $persistedEntity->getId()]);
            }

            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Synonym.'));
        }

        $this->dataPersistor->set('typesense_synonyms_synonym', $data);

        return $resultRedirect->setPath(
            '*/*/edit',
            ['id' => $this->getRequest()->getParam('id')]
        );
    }
}
