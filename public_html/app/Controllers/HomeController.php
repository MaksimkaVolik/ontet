<?php
namespace App\Controllers;

use Core\Controller;
use Core\View;

class HomeController extends Controller {
    public function index() {
        $data = [
            'title' => 'Главная - OtvetForum',
            'description' => 'Крупнейший форум вопросов и ответов'
        ];
        
        View::render('home/index.php', $data);
    }
}