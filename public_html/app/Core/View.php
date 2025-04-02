<?php
namespace Core;

class View {
    public static function render(string $view, array $data = []) {
    $defaultMeta = [
        'title' => 'OtvetForum - Сообщество вопросов и ответов',
        'description' => 'Присоединяйтесь к обсуждению!',
        'image' => static::asset('images/logo-social.png'),
        'type' => 'website'
    ];

    $data['meta'] = array_merge($defaultMeta, $data['meta'] ?? []);

    extract($data);
    include "views/{$view}.php";

        extract($args, EXTR_SKIP);
        
        $file = __DIR__ . "/../../public/views/$view";
        if (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("View $file not found");
        }
    }
}
