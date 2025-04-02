<?php
namespace App\Controllers;

use App\Models\User;
use Core\Uploader;
use Core\Controller;

class UserController extends Controller {
    public function updateAvatar() {
        if (!isset($_FILES['avatar'])) {
            $this->redirect('/profile?error=upload');
        }

        $uploader = new Uploader();
        $avatarPath = $uploader->handleAvatarUpload($_FILES['avatar'], $_SESSION['user_id']);

        if ($avatarPath) {
            $userModel = new User();
            $userModel->updateAvatar($_SESSION['user_id'], $avatarPath);
            $_SESSION['avatar'] = $avatarPath;
            $this->redirect('/profile?success=avatar');
        } else {
            $this->redirect('/profile?error=invalid');
        }
    }
}