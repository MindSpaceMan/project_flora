<?php
// src/Security/CartTokenResolver.php
declare(strict_types=1);

namespace App\Security;

use Random\RandomException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class CartTokenResolver
{
    public const string HEADER = 'X-Cart-Token';

    public function resolveRawToken(Request $request): ?string
    {
        $raw = (string)$request->headers->get(self::HEADER);
        if (empty($raw)) {
            throw new BadRequestHttpException('Токен корзины обязателен');
        }
        return trim($raw);
    }

    public function hash(string $raw): string
    {
        return hash('sha256', $raw);
    }

    /**
     * @throws RandomException
     */
    public function generateCartToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}