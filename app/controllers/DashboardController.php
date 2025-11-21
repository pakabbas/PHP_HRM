<?php

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $employeeModel = new EmployeeModel();
        $leaveModel = new LeaveModel();
        $attendanceModel = new AttendanceModel();
        $payrollModel = new PayrollModel();

        $stats = [
            'employees' => $employeeModel->count(),
            'pending_leaves' => $leaveModel->pendingCount(),
            'attendance_today' => $attendanceModel->summaryForDate(date('Y-m-d'))['present'] ?? 0,
            'payroll_processed' => count($payrollModel->history(5)),
        ];

        $recentLeaves = $leaveModel->requests([], ['limit' => 5]);
        $attendanceSummary = $attendanceModel->summaryForDate(date('Y-m-d'));
        $payrollHistory = $payrollModel->history(5);

        $this->view('dashboard/index', [
            'pageTitle' => 'HRM Dashboard',
            'stats' => $stats,
            'recentLeaves' => $recentLeaves,
            'attendanceSummary' => $attendanceSummary,
            'payrollHistory' => $payrollHistory,
            'flash' => flash_get('global'),
        ]);
    }
}

