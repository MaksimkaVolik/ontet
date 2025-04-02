<?php
namespace App\Controllers;

use App\Models\Report;
use Core\Controller;
use Core\View;

class ReportController extends Controller {
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reportModel = new Report();
            $reportModel->create(
                $_SESSION['user_id'],
                $_POST['content_type'],
                $_POST['content_id'],
                $_POST['reason']
            );
            
            $this->redirectBack('/?report=success');
        }
    }

    public function dashboard() {
        $this->checkModeratorAccess();
        
        $reportModel = new Report();
        $reports = $reportModel->getPendingReports();
        
        View::render('moderation/reports.php', [
            'title' => 'Панель модератора',
            'reports' => $reports
        ]);
    }

    public function resolve() {
        $this->checkModeratorAccess();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reportModel = new Report();
            $reportModel->resolveReport(
                $_POST['report_id'],
                $_SESSION['user_id'],
                $_POST['resolution'],
                $_POST['status']
            );
            
            $this->redirect('/moderation?resolved=1');
        }
    }

    private function checkModeratorAccess() {
        if (!isset($_SESSION['is_moderator']) || !$_SESSION['is_moderator']) {
            $this->redirect('/');
        }
    }
}