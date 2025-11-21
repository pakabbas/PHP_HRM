<?php

class ConfigController extends Controller
{
    protected ConfigModel $config;

    public function __construct()
    {
        parent::__construct();
        $this->config = new ConfigModel();
    }

    public function index(): void
    {
        $this->requireAuth(['admin', 'hr']);
        $entity = $_GET['entity'] ?? 'departments';
        $items = $this->config->getAll($entity);
        $this->view('config/index', [
            'pageTitle' => 'Configurations',
            'entity' => $entity,
            'items' => $items,
            'flash' => flash_get('config'),
        ]);
    }

    public function store(): void
    {
        $this->requireAuth(['admin', 'hr']);
        verify_csrf();
        $entity = $_POST['entity'];
        $id = !empty($_POST['id']) ? (int) $_POST['id'] : null;
        $payload = match ($entity) {
            'departments' => [
                'department_name' => $_POST['department_name'],
                'status' => $_POST['status'] ?? 1,
            ],
            'designations' => [
                'designation_name' => $_POST['designation_name'],
                'status' => $_POST['status'] ?? 1,
            ],
            'cities' => [
                'city_name' => $_POST['city_name'],
                'status' => $_POST['status'] ?? 1,
            ],
            'configurations' => [
                'config_type' => $_POST['config_type'],
                'config_key' => $_POST['config_key'],
                'config_value' => $_POST['config_value'],
                'status' => $_POST['status'] ?? 1,
            ],
            'holidays' => [
                'holiday_date' => $_POST['holiday_date'],
                'description' => $_POST['description'],
            ],
            default => [],
        };

        $this->config->save($entity, $payload, $id);
        flash_set('config', ucfirst($entity) . ' saved.', 'success');
        redirect('config/index?entity=' . $entity);
    }

    public function delete(int $id): void
    {
        $this->requireAuth(['admin']);
        verify_csrf();
        $entity = $_POST['entity'] ?? 'departments';
        $this->config->delete($entity, $id);
        flash_set('config', 'Record deleted.', 'info');
        redirect('config/index?entity=' . $entity);
    }
}

