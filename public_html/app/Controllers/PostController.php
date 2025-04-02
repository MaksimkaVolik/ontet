<?php
namespace App\Controllers;

use App\Models\Forum\Post;
use App\Models\Notification;
use Core\Controller;

class PostController extends Controller {
    public function create() {
        // ... существующий код создания поста
        
        $postId = $postModel->create([
            'content' => $_POST['content'],
            'user_id' => $_SESSION['user_id'],
            'thread_id' => $_POST['thread_id']
        ]);

        // Уведомление автору темы
        $thread = $threadModel->findById($_POST['thread_id']);
        if ($thread['user_id'] != $_SESSION['user_id']) {
            $notification = new Notification();
            $notification->create(
                $thread['user_id'],
                'new_reply',
                $postId,
                'post'
            );
        }

        // ... остальной код
    }
}