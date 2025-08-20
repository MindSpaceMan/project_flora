<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\ResponseDTO\AddItemResponse;
use App\Controller\ResponseDTO\CreateCartResponse;
use App\Controller\ResponseDTO\GetCartResponse;
use App\Controller\ResponseDTO\OrderColResponse;
use App\Controller\ResponseDTO\RemoveItemResponse;
use App\Entity\Order;
use App\Security\CartTokenResolver;
use App\Service\CartService;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
    #[Route('', name: 'create', methods: ['POST'])]
    #[CreateCartResponse]
    public function createCart(): JsonResponse
    {
        return $this->json(
            $this->cartService->createCart(),
            200,
            [],
            ['groups' => ['cart:read']]
        );

    }

    /**
     */
    #[Route('/{id}/single', name: 'single', methods: ['GET'])]
    #[GetCartResponse]
    public function getCart(Request $request): JsonResponse
    {
        $raw = $this->tokenResolver->resolveRawToken($request);
        $order = $this->cartService->getCartByToken($raw);

        return $this->json(
            [
                'cart' => $order,
                'cartToken' => $raw
            ],
            200,
            [],
            ['groups' => ['cart:read']]
        );
    }

    /**
     */
    #[Route('/items', name: 'add_item', methods: ['POST'])]
    #[AddItemResponse]
    public function addItem(
        #[MapQueryParameter] string $productId,
        Request                     $request,
        #[MapQueryParameter] int    $quantity = 1,
    ): JsonResponse
    {
        $token = $this->tokenResolver->resolveRawToken($request);
        if (!$productId) {
            return $this->json(['error' => 'productId is обязателен'], 400);
        }
        if ($quantity < 1) {
            return $this->json(['error' => 'quantity must лучше >= 1'], 400);
        }


        return $this->json(
            [
                'cart' => $this->cartService->addItemByToken($token, $productId, $quantity),
                'cartToken' => $token
            ],
            200,
            [],
            ['groups' => ['cart:read']]
        );
    }

    /**
     */
    #[Route('/items', name: 'remove_item', methods: ['DELETE'])]
    #[RemoveItemResponse]
    public function removeItem(
        #[MapQueryParameter] string $productId,
        Request                     $request,
        #[MapQueryParameter] int    $quantity = 1,
    ): JsonResponse
    {
        $token = $this->tokenResolver->resolveRawToken($request);
        if (!$productId) {
            return $this->json(['error' => 'productId is обязателен'], 400);
        }
        if ($quantity < 1) {
            return $this->json(['error' => 'quantity must лучше >= 1'], 400);
        }

        return $this->json(
            [
                'cart' => $this->cartService->removeItemByToken($token, $productId, $quantity),
                'cartToken' => $token
            ],
            200,
            [],
            ['groups' => ['cart:read']]
        );
    }

//    #[IsGranted('IS_AUTHENTICATED_FULLY')]
//    #[IsGranted('ROLE_ADMIN')]
    #[Route('', name: 'get_all', methods: ['GET'])]
    #[OrderColResponse]
    public function getAll(): JsonResponse
    {

        $orders = $this->cartService->getAll();

        return $this->json(
            $orders,
            200,
            [],
            ['groups' => ['admin:cart','product:detail' ]]
        );
    }
}