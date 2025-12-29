<?php

namespace Api\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new JsonModel([
            'status' => 'success',
            'message' => 'Welcome to mkgo API',
        ]);
    }
}
