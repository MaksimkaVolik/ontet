<?php
namespace Core;

class Uploader {
    private $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    private $maxFileSize = 5 * 1024 * 1024; // 5MB
    private $uploadPath = __DIR__ . '/../../public/uploads/';

    public function handleAvatarUpload($file, $userId) {
        if (!$this->validate($file)) {
            return false;
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
        $destination = $this->uploadPath . 'avatars/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return false;
        }

        $this->resizeImage($destination, 200, 200);

        return '/uploads/avatars/' . $filename;
    }

    private function validate($file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if ($file['size'] > $this->maxFileSize) {
            return false;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        return in_array($mime, $this->allowedMimeTypes);
    }

    private function resizeImage($path, $width, $height) {
        $info = getimagesize($path);
        list($origWidth, $origHeight) = $info;

        $src = match($info['mime']) {
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/png' => imagecreatefrompng($path),
            'image/gif' => imagecreatefromgif($path),
            default => false
        };

        if (!$src) return false;

        $dst = imagecreatetruecolor($width, $height);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);

        switch($info['mime']) {
            case 'image/jpeg': imagejpeg($dst, $path, 90); break;
            case 'image/png': imagepng($dst, $path); break;
            case 'image/gif': imagegif($dst, $path); break;
        }

        imagedestroy($src);
        imagedestroy($dst);
    }
}