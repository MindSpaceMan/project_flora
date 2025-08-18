<?php
// src/Security/CartTokenResolver.php
declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;

final class CartTokenResolver
{
    public const HEADER = 'X-Cart-Token';

    public function resolveRawToken(Request $request): ?string
    {
        $raw = $request->headers->get(self::HEADER);
        $raw = is_string($raw) ? trim($raw) : null;
        return $raw !== '' ? $raw : null;
    }

    public function hash(string $raw): string
    {
        return hash('sha256', $raw);
    }
}