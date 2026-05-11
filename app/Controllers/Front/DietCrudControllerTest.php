<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Services\DietCrudServiceTest;

class DietCrudControllerTest extends BaseController
{
    private DietCrudServiceTest $dietCrudService;

    public function __construct()
    {
        $this->dietCrudService = new DietCrudServiceTest();
    }

    public function index()
    {
        $this->authService->getUserIdOrFail();
        $data = $this->dietCrudService->getFormData();

        return view('diets/manage_test', [
            'categories' => $data['categories'],
            'diets' => $data['diets'],
            'error' => session()->getFlashdata('error'),
            'success' => session()->getFlashdata('success'),
        ]);
    }

    public function create()
    {
        $this->authService->getUserIdOrFail();

        $name = (string) $this->request->getPost('name');
        $pricePerDay = (float) $this->request->getPost('price_per_day');
        $distributions = (array) ($this->request->getPost('distributions') ?? []);

        try {
            $this->dietCrudService->createDiet($name, $pricePerDay, $distributions);
        } catch (\Throwable $th) {
            return redirect()->to(base_url('diets/manage-test'))->with('error', $th->getMessage());
        }

        return redirect()->to(base_url('diets/manage-test'))->with('success', 'Regime cree avec succes.');
    }

    public function delete(int $dietId)
    {
        $this->authService->getUserIdOrFail();

        try {
            $this->dietCrudService->deleteDiet($dietId);
        } catch (\Throwable $th) {
            return redirect()->to(base_url('diets/manage-test'))->with('error', $th->getMessage());
        }

        return redirect()->to(base_url('diets/manage-test'))->with('success', 'Regime supprime.');
    }
}