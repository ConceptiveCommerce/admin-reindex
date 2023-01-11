<?php

namespace Conceptive\Reindex\Controller\Adminhtml\indexer;

class Process extends \Magento\Backend\App\Action
{
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory
    ) {
        parent::__construct($context);
        $this->indexerFactory = $indexerFactory;
    }

    public function execute()
    {
        $indexerIds = $this->getRequest()->getParam('indexer_ids');
        if (!is_array($indexerIds)) {
            $this->messageManager->addErrorMessage(__('Please select indexers.'));
        } else {
            try {
                foreach ($indexerIds as $indexerId) {
                    $indexer = $this->indexerFactory->create();
                    $indexer->load($indexerId)->reindexAll();
                }
                $this->messageManager->addSuccess(
                    __('%1 item(s) have been reindexed.', count($indexerIds))
                );
            } catch (\Exception $e) {
                $this->getMessageManager()->addErrorMessage($e->getMessage());
            }
        }
        $result = $this->resultRedirectFactory->create();
        return $result->setRefererUrl();

    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Indexer::changeMode');
    }
}
