<?php

class EmployeesController extends Controller
{
    protected EmployeeModel $employees;
    protected ConfigModel $config;

    public function __construct()
    {
        parent::__construct();
        $this->employees = new EmployeeModel();
        $this->config = new ConfigModel();
    }

    public function index(): void
    {
        $this->requireAuth(['admin', 'hr', 'manager', 'employee']);

        $currentUser = $this->auth->user();
        if ($currentUser['role'] === 'employee' && $currentUser['employee_id']) {
            redirect('employees/show/' . $currentUser['employee_id']);
        }

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $search = trim($_GET['search'] ?? '');
        $filters = [
            'department_id' => $_GET['department_id'] ?? null,
            'status' => $_GET['status'] ?? null,
        ];

        $employees = $this->employees->listing($filters, [
            'search' => $search,
            'limit' => $perPage,
            'offset' => ($page - 1) * $perPage,
        ]);

        $total = $this->employees->countFiltered($filters, $search);
        $paginator = Paginator::meta($page, $perPage, $total);

        $this->view('employees/index', [
            'pageTitle' => 'Employees',
            'employees' => $employees,
            'filters' => $filters,
            'search' => $search,
            'paginator' => $paginator,
            'departments' => $this->config->getAll('departments'),
            'statuses' => ['active', 'inactive', 'terminated', 'resigned'],
            'flash' => flash_get('employees'),
        ]);
    }

    public function create(): void
    {
        $this->requireAuth(['admin', 'hr']);
        $this->view('employees/form', [
            'pageTitle' => 'Add Employee',
            'employee' => null,
            'departments' => $this->config->getAll('departments'),
            'designations' => $this->config->getAll('designations'),
            'cities' => $this->config->getAll('cities'),
        ]);
    }

    public function store(): void
    {
        $this->requireAuth(['admin', 'hr']);
        verify_csrf();

        $data = [
            'emp_code' => strtoupper(trim($_POST['emp_code'] ?? '')),
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'gender' => $_POST['gender'] ?? null,
            'dob' => $_POST['dob'] ?? null,
            'cnic' => $_POST['cnic'] ?? null,
            'phone' => $_POST['phone'] ?? null,
            'email' => $_POST['email'] ?? null,
            'address' => $_POST['address'] ?? null,
            'city_id' => $_POST['city_id'] ?? null,
            'department_id' => $_POST['department_id'] ?? null,
            'designation_id' => $_POST['designation_id'] ?? null,
            'joining_date' => $_POST['joining_date'] ?? null,
            'status' => $_POST['status'] ?? 'active',
        ];

        $errors = Validator::required($data, [
            'emp_code' => 'Employee Code',
            'first_name' => 'First Name',
            'department_id' => 'Department',
            'joining_date' => 'Joining Date',
        ]);
        if ($emailError = Validator::email($data['email'], 'Email')) {
            $errors['email'] = $emailError;
        }

        if ($errors) {
            flash_set('employees', implode(' ', $errors), 'danger');
            redirect('employees/create');
        }

        $this->employees->create($data);
        flash_set('employees', 'Employee created successfully.', 'success');
        redirect('employees/index');
    }

    public function edit(int $id): void
    {
        $this->requireAuth(['admin', 'hr']);
        $employee = $this->employees->find($id);
        if (!$employee) {
            flash_set('employees', 'Employee not found.', 'danger');
            redirect('employees/index');
        }
        $this->view('employees/form', [
            'pageTitle' => 'Edit Employee',
            'employee' => $employee,
            'departments' => $this->config->getAll('departments'),
            'designations' => $this->config->getAll('designations'),
            'cities' => $this->config->getAll('cities'),
        ]);
    }

    public function update(int $id): void
    {
        $this->requireAuth(['admin', 'hr']);
        verify_csrf();
        $employee = $this->employees->find($id);
        if (!$employee) {
            flash_set('employees', 'Employee not found.', 'danger');
            redirect('employees/index');
        }

        $data = [
            'emp_code' => strtoupper(trim($_POST['emp_code'] ?? $employee['emp_code'])),
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'gender' => $_POST['gender'] ?? null,
            'dob' => $_POST['dob'] ?? null,
            'cnic' => $_POST['cnic'] ?? null,
            'phone' => $_POST['phone'] ?? null,
            'email' => $_POST['email'] ?? null,
            'address' => $_POST['address'] ?? null,
            'city_id' => $_POST['city_id'] ?? null,
            'department_id' => $_POST['department_id'] ?? null,
            'designation_id' => $_POST['designation_id'] ?? null,
            'joining_date' => $_POST['joining_date'] ?? null,
            'leaving_date' => $_POST['leaving_date'] ?? null,
            'status' => $_POST['status'] ?? 'active',
        ];

        $errors = Validator::required($data, [
            'emp_code' => 'Employee Code',
            'first_name' => 'First Name',
            'department_id' => 'Department',
            'joining_date' => 'Joining Date',
        ]);
        if ($errors) {
            flash_set('employees', implode(' ', $errors), 'danger');
            redirect('employees/edit/' . $id);
        }

        $this->employees->update($id, $data);
        flash_set('employees', 'Employee updated successfully.', 'success');
        redirect('employees/index');
    }

    public function show(int $id): void
    {
        $this->requireAuth(['admin', 'hr', 'manager', 'employee']);
        $employee = $this->employees->find($id);
        if (!$employee) {
            flash_set('employees', 'Employee not found.', 'danger');
            redirect('employees/index');
        }

        $user = $this->auth->user();
        if ($user['role'] === 'employee' && (int) $user['employee_id'] !== (int) $id) {
            http_response_code(403);
            die('Unauthorized');
        }

        $this->view('employees/show', [
            'pageTitle' => 'Employee Profile',
            'employee' => $employee,
            'departments' => $this->config->getAll('departments'),
            'designations' => $this->config->getAll('designations'),
            'cities' => $this->config->getAll('cities'),
        ]);
    }

    public function delete(int $id): void
    {
        $this->requireAuth(['admin']);
        verify_csrf();
        $this->employees->delete($id);
        flash_set('employees', 'Employee removed.', 'info');
        redirect('employees/index');
    }
}

