<?php

namespace Api\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Laminas\Db\Adapter\Adapter;

class VehiclesController extends AbstractActionController
{
    private $dbAdapter;

    public function __construct(Adapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    public function indexAction()
    {
        $sql = "SELECT * FROM vehicles";
        $statement = $this->dbAdapter->createStatement($sql);
        $result = $statement->execute();

        $vehicles = [];
        foreach ($result as $row) {
            $vehicles[] = $row;
        }

        return new JsonModel(['status' => 'success', 'data' => $vehicles]);
    }

    public function createAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
             return new JsonModel(['status' => 'error', 'message' => 'Method not allowed']);
        }

        $data = json_decode($request->getContent(), true);
        // Add validation logic here

        $sql = "INSERT INTO vehicles (plate, brand, model, year, status) VALUES (?, ?, ?, ?, ?)";
        try {
            $statement = $this->dbAdapter->createStatement($sql, [
                $data['plate'],
                $data['brand'],
                $data['model'],
                $data['year'],
                $data['status'] ?? 'active'
            ]);
            $statement->execute();
            return new JsonModel(['status' => 'success', 'message' => 'Vehicle created']);
        } catch (\Exception $e) {
            return new JsonModel(['status' => 'error', 'message' => 'Creation failed: ' . $e->getMessage()]);
        }
    }
}
