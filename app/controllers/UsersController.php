<?php

class UsersController extends Controller
{
    protected UserModel $users;
    protected EmployeeModel $employees;

    public function __construct()
    {
        parent::__construct();
        $this->users = new UserModel();
        $this->employees = new EmployeeModel();
    }

    public function index(): void
    {
        $this->requireAuth(['admin']);
        $search = trim($_GET['search'] ?? '');
        $users = $this->users->allWithEmployees([
            'search' => $search,
        ]);

        $this->view('users/index', [
            'pageTitle' => 'User Accounts',
            'users' => $users,
            'search' => $search,
            'flash' => flash_get('users'),
        ]);
    }

    public function create(): void
    {
        $this->requireAuth(['admin']);
        $this->view('users/form', [
            'pageTitle' => 'Create User',
            'user' => null,
            'employees' => $this->employees->listing([], ['limit' => 500]),
            'roles' => ['admin', 'hr', 'manager', 'employee'],
        ]);
    }

    public function store(): void
    {
        $this->requireAuth(['admin']);
        verify_csrf();

        $data = [
            'employee_id' => $_POST['employee_id'] ? (int) $_POST['employee_id'] : null,
            'username' => trim($_POST['username'] ?? ''),
            'role' => $_POST['role'] ?? 'employee',
            'status' => isset($_POST['status']) ? (int) $_POST['status'] : 1,
        ];
        $password = $_POST['password'] ?? '';

        $errors = Validator::required($data, [
            'username' => 'Username',
        ]);
        if (!$password) {
            $errors['password'] = 'Password is required.';
        }

        if ($errors) {
            flash_set('users', json_encode($errors), 'danger');
            redirect('users/create');
        }

        if ($this->users->getByUsername($data['username'])) {
            flash_set('users', 'Username already exists.', 'danger');
            redirect('users/create');
        }

        $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        $this->users->create($data);
        flash_set('users', 'User created.', 'success');
        redirect('users/index');
    }

    public function edit(int $id): void
    {
        $this->requireAuth(['admin']);
        $user = $this->users->find($id);
        if (!$user) {
            flash_set('users', 'User not found.', 'danger');
            redirect('users/index');
        }
        $this->view('users/form', [
            'pageTitle' => 'Edit User',
            'user' => $user,
            'employees' => $this->employees->listing([], ['limit' => 500]),
            'roles' => ['admin', 'hr', 'manager', 'employee'],
        ]);
    }

    public function update(int $id): void
    {
        $this->requireAuth(['admin']);
        verify_csrf();
        $user = $this->users->find($id);
        if (!$user) {
            flash_set('users', 'User not found.', 'danger');
            redirect('users/index');
        }

        $data = [
            'employee_id' => $_POST['employee_id'] ? (int) $_POST['employee_id'] : null,
            'username' => trim($_POST['username'] ?? $user['username']),
            'role' => $_POST['role'] ?? $user['role'],
            'status' => isset($_POST['status']) ? (int) $_POST['status'] : 1,
        ];
        $password = $_POST['password'] ?? '';

        if ($password) {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->users->update($id, $data);
        flash_set('users', 'User updated.', 'success');
        redirect('users/index');
    }

    public function delete(int $id): void
    {
        $this->requireAuth(['admin']);
        verify_csrf();
        $this->users->delete($id);
        flash_set('users', 'User deleted.', 'info');
        redirect('users/index');
    }
}

