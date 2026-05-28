<?php
// src/Security/JwtService.php
namespace App\Security;

class JwtService
{
    private string $secret = 'tyrion59'; // à changer en prod

    // Génère un token JWT
    public function generate(array $payload, int $expireSeconds = 3600): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payload['exp'] = time() + $expireSeconds;

        $base64Header = $this->base64UrlEncode(json_encode($header));
        $base64Payload = $this->base64UrlEncode(json_encode($payload));

        $signature = hash_hmac('sha256', "$base64Header.$base64Payload", $this->secret, true);
        $base64Sig = $this->base64UrlEncode($signature);

        return "$base64Header.$base64Payload.$base64Sig";
    }

    // Vérifie et décode un token JWT
    public function decode(string $token): array
    {
        [$base64Header, $base64Payload, $base64Sig] = explode('.', $token);

        // Nouveau code sécurisé
        $sigCheck = hash_hmac('sha256', "$base64Header.$base64Payload", $this->secret, true);
        $decodedSig = $this->base64UrlDecode($base64Sig);

        if ($decodedSig === false || !hash_equals($sigCheck, $decodedSig)) {
            throw new \Exception('Token invalide');
        }

        $payload = json_decode($this->base64UrlDecode($base64Payload), true);

        if ($payload['exp'] < time()) {
            throw new \Exception('Token expiré');
        }

        return $payload;
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}