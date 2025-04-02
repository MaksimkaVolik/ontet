<?php
namespace App\Controllers;

use App\Models\User;
use Core\Controller;
use Core\View;
use Core\CSRF;

class AuthController extends Controller {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // 1. Проверка CSRF-токена
                if (!isset($_POST['csrf_token'])) {
                    throw new \Exception('Ошибка безопасности: отсутствует токен');
                }
                CSRF::validate($_POST['csrf_token']);

                // 2. Валидация полей
                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';
                
                if (empty($email) || empty($password)) {
                    throw new \Exception('Все поля обязательны для заполнения');
                }

                // 3. Аутентификация
                $user = (new User())->login($email, $password);
                if (!$user) {
                    throw new \Exception('Неверные учетные данные');
                }

                // 4. Установка сессии
                $_SESSION = [
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'avatar' => $user['avatar'] ?? '/images/default-avatar.png',
                    'ip' => $_SERVER['REMOTE_ADDR']
                ];

                header('Location: /dashboard');
                exit;

            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        View::render('auth/login.php', [
            'error' => $error ?? null,
            'csrf_token' => CSRF::generate()
        ]);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // 1. CSRF-защита
                CSRF::validate($_POST['csrf_token'] ?? '');

                // 2. Валидация данных
                $data = [
                    'username' => trim(htmlspecialchars($_POST['username'] ?? '')),
                    'email' => filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL),
                    'password' => $_POST['password'] ?? ''
                ];

                if (in_array('', $data, true)) {
                    throw new \Exception('Все поля обязательны');
                }

                if (!$data['email']) {
                    throw new \Exception('Некорректный email');
                }

                if (strlen($data['password']) < 8) {
                    throw new \Exception('Пароль должен быть не менее 8 символов');
                }

                // 3. Регистрация
                $userId = (new User())->register($data);
                
                // 4. Автовход
                $_SESSION = [
                    'user_id' => $userId,
                    'username' => $data['username'],
                    'avatar' => '/images/default-avatar.png',
                    'ip' => $_SERVER['REMOTE_ADDR']
                ];

                header('Location: /profile');
                exit;

            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        View::render('auth/register.php', [
            'error' => $error ?? null,
            'csrf_token' => CSRF::generate()
        ]);
    }

    public function logout() {
        $_SESSION = [];
        session_destroy();
        setcookie(session_name(), '', time()-3600, '/');
        header('Location: /login');
        exit;
    }
}