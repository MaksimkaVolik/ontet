<?php
namespace Core;

class View {
    public static function render($view, $args = []) {
        extract($args, EXTR_SKIP);
        $file = __DIR__ . "/../../public/views/$view";
        
        if (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("View $file not found");
        }
    }
}