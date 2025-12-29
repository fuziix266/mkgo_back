<?php

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $viewModel = new ViewModel([
            'framework' => 'Laminas MVC',
            'version' => 'Skeleton v1.0',
            'welcome_message' => 'Laminas Skeleton',
            'project_name' => 'Proyecto base',
        ]);
        
        // Deshabilitar el layout porque index.phtml ya tiene HTML completo
        $viewModel->setTerminal(true);
        
        return $viewModel;
    }
}