<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ActivitiesModel;
use CodeIgniter\HTTP\RedirectResponse;

class ActivitiesController extends BaseController
{
    private ActivitiesModel $activitiesModel;

    public function __construct()
    {
        $this->activitiesModel = new ActivitiesModel();
    }

    public function index(): string
    {
        helper('url');

        return view('admin/activities/index', [
            'activities' => $this->activitiesModel->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    public function create(): string
    {
        helper(['url', 'form']);

        return view('admin/activities/form', [
            'title' => 'Nouvelle activite sportive',
            'action' => base_url('admin/activities'),
            'activity' => [
                'name' => '',
                'description' => '',
                'met_value' => '',
            ],
        ]);
    }

    public function store(): RedirectResponse
    {
        helper('url');

        $payload = $this->collectPayload();
        $errors = $this->validatePayload($payload);

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $this->activitiesModel->insert($payload);

        return redirect()->to(base_url('admin/activities'))->with('success', 'Activite creee.');
    }

    public function edit(int $id): string
    {
        helper(['url', 'form']);

        $activity = $this->activitiesModel->find($id);

        if (! $activity) {
            return view('admin/activities/index', [
                'activities' => $this->activitiesModel->orderBy('id', 'DESC')->findAll(),
                'errors' => ['Activite introuvable.'],
            ]);
        }

        return view('admin/activities/form', [
            'title' => 'Modifier une activite sportive',
            'action' => base_url('admin/activities/' . $id),
            'activity' => $activity,
            'isEdit' => true,
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        helper('url');

        $activity = $this->activitiesModel->find($id);

        if (! $activity) {
            return redirect()->to(base_url('admin/activities'))->with('errors', ['Activite introuvable.']);
        }

        $payload = $this->collectPayload();
        $errors = $this->validatePayload($payload);

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $this->activitiesModel->update($id, $payload);

        return redirect()->to(base_url('admin/activities'))->with('success', 'Activite mise a jour.');
    }

    public function delete(int $id): RedirectResponse
    {
        helper('url');

        $this->activitiesModel->delete($id);

        return redirect()->to(base_url('admin/activities'))->with('success', 'Activite supprimee.');
    }

    private function collectPayload(): array
    {
        return [
            'name' => trim((string) $this->request->getPost('name')),
            'description' => trim((string) $this->request->getPost('description')),
            'met_value' => $this->normalizeMetValue($this->request->getPost('met_value')),
        ];
    }

    private function validatePayload(array $payload): array
    {
        $errors = [];

        if ($payload['name'] === '') {
            $errors[] = 'Le nom de l\'activite est obligatoire.';
        }

        if ($payload['met_value'] !== null && $payload['met_value'] < 0) {
            $errors[] = 'La valeur MET doit etre positive.';
        }

        return $errors;
    }

    private function normalizeMetValue($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }
}
