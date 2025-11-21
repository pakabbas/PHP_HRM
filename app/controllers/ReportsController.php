<?php

class ReportsController extends Controller
{
    protected ReportModel $reports;

    public function __construct()
    {
        parent::__construct();
        $this->reports = new ReportModel();
    }

    public function index(): void
    {
        $this->requireAuth(['admin', 'hr', 'manager']);
        $type = $_GET['type'] ?? 'employees';
        $filters = [
            'status' => $_GET['status'] ?? null,
            'department_id' => $_GET['department_id'] ?? null,
            'start' => $_GET['start'] ?? date('Y-m-01'),
            'end' => $_GET['end'] ?? date('Y-m-t'),
            'month' => $_GET['month'] ?? date('n'),
            'year' => $_GET['year'] ?? date('Y'),
        ];

        $dataset = $this->buildDataset($type, $filters);

        $this->view('reports/index', [
            'pageTitle' => 'Reports',
            'type' => $type,
            'filters' => $filters,
            'dataset' => $dataset,
            'flash' => flash_get('reports'),
        ]);
    }

    public function export(): void
    {
        $this->requireAuth(['admin', 'hr', 'manager']);
        $type = $_GET['type'] ?? 'employees';
        $format = $_GET['format'] ?? 'excel';
        $filters = [
            'status' => $_GET['status'] ?? null,
            'department_id' => $_GET['department_id'] ?? null,
            'start' => $_GET['start'] ?? date('Y-m-01'),
            'end' => $_GET['end'] ?? date('Y-m-t'),
            'month' => $_GET['month'] ?? date('n'),
            'year' => $_GET['year'] ?? date('Y'),
        ];

        $dataset = $this->buildDataset($type, $filters);
        $filename = "{$type}-report-" . date('YmdHis');

        if ($format === 'pdf') {
            SimplePdf::downloadTable(
                strtoupper($type) . ' REPORT',
                $dataset['headers'],
                $dataset['rows'],
                "{$filename}.pdf"
            );
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, $dataset['headers']);
        foreach ($dataset['rows'] as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }

    protected function buildDataset(string $type, array $filters): array
    {
        switch ($type) {
            case 'attendance':
                $records = $this->reports->attendanceReport($filters['start'], $filters['end'], $filters);
                return [
                    'headers' => ['Date', 'Code', 'Employee', 'Status', 'Check In', 'Check Out'],
                    'rows' => array_map(function ($row) {
                        return [
                            $row['attendance_date'],
                            $row['emp_code'],
                            $row['first_name'] . ' ' . $row['last_name'],
                            strtoupper($row['status']),
                            $row['check_in'],
                            $row['check_out'],
                        ];
                    }, $records),
                ];
            case 'leaves':
                $records = $this->reports->leaveReport($filters);
                return [
                    'headers' => ['Employee', 'Type', 'Start', 'End', 'Days', 'Status'],
                    'rows' => array_map(function ($row) {
                        return [
                            $row['first_name'] . ' ' . $row['last_name'],
                            $row['type_name'],
                            $row['start_date'],
                            $row['end_date'],
                            $row['total_days'],
                            strtoupper($row['status']),
                        ];
                    }, $records),
                ];
            case 'payroll':
                $records = $this->reports->payrollReport($filters['month'], $filters['year']);
                return [
                    'headers' => ['Code', 'Employee', 'Month', 'Gross', 'Allowances', 'Deductions', 'Net', 'Status'],
                    'rows' => array_map(function ($row) {
                        return [
                            $row['emp_code'],
                            $row['first_name'] . ' ' . $row['last_name'],
                            $row['month'] . '/' . $row['year'],
                            $row['gross_salary'],
                            $row['total_allowances'],
                            $row['total_deductions'] + $row['loan_deduction'] + $row['unpaid_leave_deductions'],
                            $row['net_salary'],
                            strtoupper($row['status']),
                        ];
                    }, $records),
                ];
            default:
                $records = $this->reports->employeeReport($filters);
                return [
                    'headers' => ['Code', 'Employee', 'Department', 'Designation', 'Status', 'Joining Date'],
                    'rows' => array_map(function ($row) {
                        return [
                            $row['emp_code'],
                            $row['employee_name'],
                            $row['department_name'],
                            $row['designation_name'],
                            strtoupper($row['status']),
                            $row['joining_date'],
                        ];
                    }, $records),
                ];
        }
    }
}

