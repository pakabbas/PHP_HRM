<?php

class AttendanceController extends Controller
{
    protected AttendanceModel $attendance;
    protected EmployeeModel $employees;

    public function __construct()
    {
        parent::__construct();
        $this->attendance = new AttendanceModel();
        $this->employees = new EmployeeModel();
    }

    public function index(): void
    {
        $this->requireAuth(['admin', 'hr', 'manager', 'employee']);
        $start = $_GET['start'] ?? date('Y-m-01');
        $end = $_GET['end'] ?? date('Y-m-t');
        $filters = [
            'department_id' => $_GET['department_id'] ?? null,
            'status' => $_GET['status'] ?? null,
        ];

        $user = $this->auth->user();
        if ($user['role'] === 'employee') {
            $filters['employee_id'] = $user['employee_id'];
        }

        $records = $this->attendance->getByDateRange($start, $end, $filters);
        $summary = $this->attendance->summaryForDate(date('Y-m-d'));

        $this->view('attendance/index', [
            'pageTitle' => 'Attendance',
            'records' => $records,
            'filters' => $filters,
            'start' => $start,
            'end' => $end,
            'summary' => $summary,
            'flash' => flash_get('attendance'),
        ]);
    }

    public function create(): void
    {
        $this->requireAuth(['admin', 'hr', 'manager']);
        $this->view('attendance/form', [
            'pageTitle' => 'Mark Attendance',
            'employees' => $this->employees->listing([], ['limit' => 500]),
        ]);
    }

    public function store(): void
    {
        $this->requireAuth(['admin', 'hr', 'manager']);
        verify_csrf();
        $rows = [
            [
                'employee_id' => (int) $_POST['employee_id'],
                'attendance_date' => $_POST['attendance_date'],
                'check_in' => $_POST['check_in'] ?? null,
                'check_out' => $_POST['check_out'] ?? null,
                'status' => $_POST['status'] ?? 'present',
            ],
        ];
        $this->attendance->markBulk($rows);
        flash_set('attendance', 'Attendance saved.', 'success');
        redirect('attendance/index');
    }

    public function import(): void
    {
        $this->requireAuth(['admin', 'hr']);
        verify_csrf();
        if (empty($_FILES['import_file']['tmp_name'])) {
            flash_set('attendance', 'No file selected.', 'danger');
            redirect('attendance/index');
        }

        $handle = fopen($_FILES['import_file']['tmp_name'], 'r');
        $rows = [];
        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) < 3 || $data[0] === 'employee_id') {
                continue;
            }
            $rows[] = [
                'employee_id' => (int) $data[0],
                'attendance_date' => $data[1],
                'status' => $data[2] ?: 'present',
                'check_in' => $data[3] ?? null,
                'check_out' => $data[4] ?? null,
            ];
        }
        fclose($handle);

        if ($rows) {
            $this->attendance->markBulk($rows);
            flash_set('attendance', 'Attendance imported.', 'success');
        } else {
            flash_set('attendance', 'File was empty.', 'warning');
        }
        redirect('attendance/index');
    }
}

