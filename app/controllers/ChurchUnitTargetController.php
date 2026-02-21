<?php
namespace App\Controllers;

use App\Models\Church;
use App\Models\ChurchUnitTarget;
use App\Utilities\Security;

class ChurchUnitTargetController extends BaseController {
    private $targetModel;
    private $churchModel;

    public function __construct() {
        parent::__construct();
        $this->targetModel = new ChurchUnitTarget();
        $this->churchModel = new Church();
        $this->authorize('manage_churches');
    }

    /**
     * List all targets with optional filters.
     */
    public function index() {
        $churchId = $this->request->get('church_id', '');
        $unitId = $this->request->get('unit_id', '');
        $periodType = $this->request->get('period_type', '');
        $periodValue = $this->request->get('period_value', '');

        $filters = [];
        if ($churchId !== '') {
            $filters['church_id'] = (int)$churchId;
        }
        if ($unitId !== '') {
            $filters['unit_id'] = (int)$unitId;
        }
        if ($periodType !== '') {
            $filters['period_type'] = $periodType;
        }
        if ($periodValue !== '') {
            $filters['period_value'] = $periodValue;
        }

        $targets = $this->targetModel->getTargetsWithDetails($filters);
        $churches = $this->churchModel->getChurches([]);

        $this->render('targets/index', [
            'title' => 'Church & Unit Targets',
            'pageTitle' => 'Church & Unit Targets',
            'targets' => $targets,
            'churches' => $churches,
            'filters' => [
                'church_id' => $churchId,
                'unit_id' => $unitId,
                'period_type' => $periodType,
                'period_value' => $periodValue,
            ],
            'targetTypes' => ChurchUnitTarget::getTargetTypes(),
            'periodTypes' => ChurchUnitTarget::getPeriodTypes(),
            'csrf_token' => Security::generateCSRFToken(),
        ]);
    }

    /**
     * Show create target form.
     */
    public function create() {
        $churches = $this->churchModel->getChurches([]);
        $churchUnits = [];
        foreach ($churches as $c) {
            $churchUnits[(int)$c['id']] = $this->churchModel->getChurchUnits($c['id']);
        }
        $preselectedChurchId = $this->request->get('church_id', '');

        $this->render('targets/create', [
            'title' => 'Set Target',
            'pageTitle' => 'Set Church or Unit Target',
            'csrf_token' => Security::generateCSRFToken(),
            'churches' => $churches,
            'churchUnits' => $churchUnits,
            'preselectedChurchId' => $preselectedChurchId,
            'targetTypes' => ChurchUnitTarget::getTargetTypes(),
            'periodTypes' => ChurchUnitTarget::getPeriodTypes(),
        ]);
    }

    /**
     * Store new target.
     */
    public function store() {
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/targets/create');
        }

        $churchId = (int)$this->request->post('church_id');
        $unitId = $this->request->post('unit_id');
        if ($unitId === '') {
            $unitId = null;
        } else {
            $unitId = (int)$unitId;
        }

        $errors = $this->validateTargetInput($churchId, $unitId);
        if (!empty($errors)) {
            $this->session->setFlash('errors', $errors);
            $this->session->setFlash('old', $this->request->all());
            $this->redirect('/targets/create');
        }

        $periodType = trim($this->request->post('period_type'));
        $periodValue = trim($this->request->post('period_value'));
        $periodErrors = $this->validatePeriod($periodType, $periodValue);
        if (!empty($periodErrors)) {
            $this->session->setFlash('errors', $periodErrors);
            $this->session->setFlash('old', $this->request->all());
            $this->redirect('/targets/create');
        }

        $data = [
            'church_id' => $churchId,
            'unit_id' => $unitId,
            'target_type' => trim($this->request->post('target_type')),
            'target_value' => (float)$this->request->post('target_value'),
            'period_type' => $periodType,
            'period_value' => $periodValue,
            'unit_label' => trim($this->request->post('unit_label', '')) ?: null,
            'notes' => trim($this->request->post('notes', '')) ?: null,
        ];

        $id = $this->targetModel->create($data);
        if ($id) {
            $this->session->setFlash('success', 'Target set successfully.');
            $this->redirect('/targets');
        } else {
            $this->session->setFlash('error', 'Failed to save target.');
            $this->session->setFlash('old', $this->request->all());
            $this->redirect('/targets/create');
        }
    }

    /**
     * Show edit form.
     */
    public function edit($id) {
        $target = $this->targetModel->find($id);
        if (!$target) {
            $this->session->setFlash('error', 'Target not found.');
            $this->redirect('/targets');
        }

        $churches = $this->churchModel->getChurches([]);
        $churchUnits = [];
        foreach ($churches as $c) {
            $churchUnits[(int)$c['id']] = $this->churchModel->getChurchUnits($c['id']);
        }

        $this->render('targets/edit', [
            'title' => 'Edit Target',
            'pageTitle' => 'Edit Target',
            'target' => $target,
            'csrf_token' => Security::generateCSRFToken(),
            'churches' => $churches,
            'churchUnits' => $churchUnits,
            'targetTypes' => ChurchUnitTarget::getTargetTypes(),
            'periodTypes' => ChurchUnitTarget::getPeriodTypes(),
        ]);
    }

    /**
     * Update target.
     */
    public function update($id) {
        $target = $this->targetModel->find($id);
        if (!$target) {
            $this->session->setFlash('error', 'Target not found.');
            $this->redirect('/targets');
        }

        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/targets/{$id}/edit");
        }

        $churchId = (int)$this->request->post('church_id');
        $unitId = $this->request->post('unit_id');
        if ($unitId === '') {
            $unitId = null;
        } else {
            $unitId = (int)$unitId;
        }

        $errors = $this->validateTargetInput($churchId, $unitId);
        if (!empty($errors)) {
            $this->session->setFlash('errors', $errors);
            $this->session->setFlash('old', $this->request->all());
            $this->redirect("/targets/{$id}/edit");
        }

        $periodType = trim($this->request->post('period_type'));
        $periodValue = trim($this->request->post('period_value'));
        $periodErrors = $this->validatePeriod($periodType, $periodValue);
        if (!empty($periodErrors)) {
            $this->session->setFlash('errors', $periodErrors);
            $this->session->setFlash('old', $this->request->all());
            $this->redirect("/targets/{$id}/edit");
        }

        $data = [
            'church_id' => $churchId,
            'unit_id' => $unitId,
            'target_type' => trim($this->request->post('target_type')),
            'target_value' => (float)$this->request->post('target_value'),
            'period_type' => $periodType,
            'period_value' => $periodValue,
            'unit_label' => trim($this->request->post('unit_label', '')) ?: null,
            'notes' => trim($this->request->post('notes', '')) ?: null,
        ];

        if ($this->targetModel->update($id, $data)) {
            $this->session->setFlash('success', 'Target updated successfully.');
            $this->redirect('/targets');
        } else {
            $this->session->setFlash('error', 'Failed to update target.');
            $this->session->setFlash('old', $this->request->all());
            $this->redirect("/targets/{$id}/edit");
        }
    }

    /**
     * Delete target.
     */
    public function delete($id) {
        $target = $this->targetModel->find($id);
        if (!$target) {
            $this->session->setFlash('error', 'Target not found.');
            $this->redirect('/targets');
        }

        $token = $this->request->post('_token') ?? $this->request->get('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/targets');
        }

        if ($this->targetModel->delete($id)) {
            $this->session->setFlash('success', 'Target removed.');
        } else {
            $this->session->setFlash('error', 'Failed to remove target.');
        }
        $this->redirect('/targets');
    }

    private function validateTargetInput($churchId, $unitId) {
        $errors = [];
        if ($churchId < 1) {
            $errors['church_id'] = 'Please select a church.';
        } else {
            $church = $this->churchModel->find($churchId);
            if (!$church) {
                $errors['church_id'] = 'Invalid church.';
            } elseif (!$this->targetModel->isUnitInChurch($churchId, $unitId)) {
                $errors['unit_id'] = 'Selected unit is not assigned to this church.';
            }
        }
        $targetType = trim($this->request->post('target_type', ''));
        if ($targetType === '') {
            $errors['target_type'] = 'Please select a target type.';
        } elseif (!array_key_exists($targetType, ChurchUnitTarget::getTargetTypes())) {
            $errors['target_type'] = 'Invalid target type.';
        }
        $val = $this->request->post('target_value');
        if ($val === '' || $val === null) {
            $errors['target_value'] = 'Target value is required.';
        } elseif (!is_numeric($val) || (float)$val < 0) {
            $errors['target_value'] = 'Target value must be a non-negative number.';
        }
        return $errors;
    }

    private function validatePeriod($periodType, $periodValue) {
        $errors = [];
        $types = ChurchUnitTarget::getPeriodTypes();
        if ($periodType === '' || !isset($types[$periodType])) {
            $errors['period_type'] = 'Please select a period type.';
        }
        if (trim($periodValue) === '') {
            $errors['period_value'] = 'Period value is required.';
        } else {
            if ($periodType === 'month' && !preg_match('/^\d{4}-\d{2}$/', $periodValue)) {
                $errors['period_value'] = 'Use format YYYY-MM for month (e.g. 2025-01).';
            } elseif ($periodType === 'quarter' && !preg_match('/^\d{4}-Q[1-4]$/i', $periodValue)) {
                $errors['period_value'] = 'Use format YYYY-Qn for quarter (e.g. 2025-Q1).';
            } elseif ($periodType === 'year' && !preg_match('/^\d{4}$/', $periodValue)) {
                $errors['period_value'] = 'Use format YYYY for year (e.g. 2025).';
            }
        }
        return $errors;
    }
}
