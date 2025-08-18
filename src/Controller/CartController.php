<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\ResponseDTO\AddItemResponse;
use App\Controller\ResponseDTO\GetCartResponse;
use App\Controller\ResponseDTO\RemoveItemResponse;
use App\Security\CartTokenResolver;
use App\Service\CartService;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cart', name: 'public_cart')]
final class CartController extends AbstractController
{
    public function __construct(
        private readonly CartService       $cartService,
        private readonly CartTokenResolver $tokenResolver,
    )
    {
    }

    /**
     * @throws RandomException
     */
    #[Route('/{id}/cart', name: 'get', methods: ['GET'])]
    #[GetCartResponse]
    public function getCart(Request $request): JsonResponse
    {
        $raw = $this->tokenResolver->resolveRawToken($request);
        $created = false;
        [$order, $rawToken] = $this->cartService->getOrCreateCartByToken($raw, $created);

        // При первом создании вернём токен, чтобы фронт его сохранил
        $payload = [
            'cart' => $order,
            'cartToken' => $created ? $rawToken : null,
        ];

        return $this->json(
            $payload,
            200,
            [],
            ['groups' => ['cart:read', 'product:list']]
        );
    }

    /**
     * @throws RandomException
     */
    #[Route('/items', name: 'add_item', methods: ['POST'])]
    #[AddItemResponse]
    public function addItem(Request $request): JsonResponse
    {
        $raw = $this->tokenResolver->resolveRawToken($request);
        $data = json_decode((string)$request->getContent(), true) ?? [];
        $productId = $data['productId'] ?? null;
        $quantity = (int)($data['quantity'] ?? 1);
        if (!$productId) {
            return $this->json(['error' => 'productId is обязателен'], 400);
        }
        if ($quantity < 1) {
            return $this->json(['error' => 'quantity must лучше >= 1'], 400);
        }

        // Если токена не было — создадим новый и вернём его
        $created = false;
        [$order, $token] = $this->cartService->getOrCreateCartByToken($raw, $created);
        $order = $this->cartService->addItem($order->getId()->toString(), $productId, $quantity);

        return $this->json(
            ['cart' => $order, 'cartToken' => $created ? $token : null],
            200,
            [],
            ['groups' => ['cart:read', 'product:list']]
        );
    }

    /**
     * @throws RandomException
     */
    #[Route('/items/{itemId}', name: 'remove_item', methods: ['DELETE'])]
    #[RemoveItemResponse]
    public function removeItem(string $itemId, Request $request): JsonResponse
    {
        $raw = $this->tokenResolver->resolveRawToken($request);
        $created = false;
        [$order] = $this->cartService->getOrCreateCartByToken($raw, $created);
        if ($created) {
            // У пользователя не было корзины — нечего удалять
            return $this->json(['cart' => $order], 200, [], ['groups' => ['cart:read', 'product:list']]);
        }

        $order = $this->cartService->removeItem($order->getId()->toString(), $itemId);

        return $this->json(
            ['cart' => $order],
            200,
            [],
            ['groups' => ['cart:read', 'product:list']]
        );
    }
}