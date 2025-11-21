<?php

class LeavesController extends Controller
{
    protected LeaveModel $leaves;
    protected EmployeeModel $employees;

    public function __construct()
    {
        parent::__construct();
        $this->leaves = new LeaveModel();
        $this->employees = new EmployeeModel();
    }

    public function index(): void
    {
        $this->requireAuth(['admin', 'hr', 'manager', 'employee']);
        $filters = [
            'status' => $_GET['status'] ?? null,
            'start_date' => $_GET['start'] ?? null,
            'end_date' => $_GET['end'] ?? null,
        ];
        $options = [
            'search' => $_GET['search'] ?? null,
            'limit' => 25,
        ];

        $currentUser = $this->auth->user();
        if ($currentUser['role'] === 'employee') {
            $filters['employee_id'] = $currentUser['employee_id'];
        }

        $requests = $this->leaves->requests($filters, $options);

        $this->view('leaves/index', [
            'pageTitle' => 'Leave Requests',
            'requests' => $requests,
            'filters' => $filters,
            'flash' => flash_get('leaves'),
            'leaveTypes' => $this->leaves->leaveTypes(),
        ]);
    }

    public function create(): void
    {
        $this->requireAuth(['admin', 'hr', 'manager', 'employee']);
        $this->view('leaves/form', [
            'pageTitle' => 'Apply Leave',
            'leaveTypes' => $this->leaves->leaveTypes(),
            'employees' => $this->employees->listing([], ['limit' => 500]),
        ]);
    }

    public function store(): void
    {
        $this->requireAuth(['admin', 'hr', 'manager', 'employee']);
        verify_csrf();

        $start = new DateTime($_POST['start_date']);
        $end = new DateTime($_POST['end_date']);
        $days = $start->diff($end)->days + 1;

        $employeeId = (int) ($_POST['employee_id'] ?? $this->auth->user()['employee_id']);
        $data = [
            'employee_id' => $employeeId,
            'leave_type_id' => (int) $_POST['leave_type_id'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'total_days' => $days,
            'status' => 'pending',
            'remarks' => $_POST['remarks'] ?? null,
        ];

        $this->leaves->create($data);
        flash_set('leaves', 'Leave request submitted.', 'success');
        redirect('leaves/index');
    }

    public function approve(int $id): void
    {
        $this->requireAuth(['admin', 'hr', 'manager']);
        verify_csrf();
        $request = $this->leaves->find($id);
        if (!$request) {
            flash_set('leaves', 'Request not found.', 'danger');
            redirect('leaves/index');
        }

        $result = $this->leaves->consumeBalance(
            (int) $request['employee_id'],
            (int) $request['leave_type_id'],
            (int) date('Y', strtotime($request['start_date'])),
            (int) $request['total_days']
        );

        $remarks = 'Approved';
        if ($result['unpaid'] > 0) {
            $remarks .= sprintf(' (%d day(s) UNPAID)', $result['unpaid']);
        }
        $this->leaves->approve($request, $remarks);

        flash_set('leaves', 'Leave approved.', 'success');
        redirect('leaves/index');
    }

    public function reject(int $id): void
    {
        $this->requireAuth(['admin', 'hr', 'manager']);
        verify_csrf();
        $remarks = $_POST['remarks'] ?? 'Rejected';
        $this->leaves->reject($id, $remarks);
        flash_set('leaves', 'Leave rejected.', 'info');
        redirect('leaves/index');
    }

    public function types(): void
    {
        $this->requireAuth(['admin', 'hr']);
        $db = Database::getInstance();
        $types = $db->query("SELECT * FROM leave_types ORDER BY type_name ASC")->fetchAll();
        $this->view('leaves/types', [
            'pageTitle' => 'Leave Types',
            'types' => $types,
        ]);
    }

    public function saveType(): void
    {
        $this->requireAuth(['admin', 'hr']);
        verify_csrf();
        $db = Database::getInstance();
        $data = [
            ':type_name' => $_POST['type_name'],
            ':default_days' => (int) $_POST['default_days'],
            ':carry_forward' => isset($_POST['carry_forward']) ? 1 : 0,
        ];
        if (!empty($_POST['id'])) {
            $data[':id'] = (int) $_POST['id'];
            $db->query("UPDATE leave_types SET type_name = :type_name, default_days = :default_days, carry_forward = :carry_forward WHERE id = :id", $data);
        } else {
            $db->insert("INSERT INTO leave_types (type_name, default_days, carry_forward) VALUES (:type_name, :default_days, :carry_forward)", $data);
        }
        flash_set('leaves', 'Leave type saved.', 'success');
        redirect('leaves/types');
    }
}

