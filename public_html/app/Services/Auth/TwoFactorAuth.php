<?php
namespace App\Services\Auth;

use OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;

class TwoFactorAuth {
    public function generateSecret(): string {
        return trim(Base32::encodeUpper(random_bytes(32)), '=');
    }

    public function generateQRCodeUrl(string $secret, string $email): string {
        $otp = TOTP::create($secret);
        $otp->setLabel('OtvetForum (' . $email . ')');
        return $otp->getQrCodeUri(
            'https://api.qrserver.com/v1/create-qr-code/?data=[DATA]&size=200x200&ecc=M',
            '[DATA]'
        );
    }

    public function verifyCode(string $secret, string $code): bool {
        return TOTP::create($secret)->verify($code, null, 1);
    }

    public function sendSmsCode(string $phone, string $code): bool {
        // Интеграция с SMS-сервисом
        return true;
    }
}