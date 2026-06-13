<?php
require_once __DIR__ . '/../config/database.php';

class Mailer {

    public static function sendOtp(string $toEmail, string $toName, string $otp): bool {
        $url  = 'https://api.brevo.com/v3/smtp/email';
        $data = [
            'sender' => [
                'name'  => BREVO_SENDER_NAME,
                'email' => BREVO_SENDER_EMAIL,
            ],
            'to' => [
                ['email' => $toEmail, 'name' => $toName]
            ],
            'subject' => 'Your OTP for Snip Password Reset',
            'htmlContent' => '<p>Your OTP is: <strong>' . $otp . '</strong></p>',
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
            'api-key: ' . BREVO_API_KEY,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 201;
    }
}