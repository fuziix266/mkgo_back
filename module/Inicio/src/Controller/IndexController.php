<?php

namespace Inicio\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel([
            'mensaje' => 'Bienvenido al módulo Inicio',
        ]);
    }
}
