<?php
require_once APP_PATH . '/models/DashboardModel.php';

class DashboardController extends Controller
{
    private DashboardModel $dashboardModel;

    public function __construct()
    {
        $this->requireAdmin();
        $this->dashboardModel = new DashboardModel();
    }

    public function index()
    {
        $stats = $this->dashboardModel->getStats();
        $recent = $this->dashboardModel->getRecentTransactions(5);

        $this->view('admin/dashboard', [
            'title' => 'Dashboard Admin',
            'stats' => $stats,
            'recent' => $recent
        ]);
    }
}
