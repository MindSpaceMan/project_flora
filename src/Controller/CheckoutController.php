<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\ResponseDTO\CheckoutResponse;
use App\DTO\CheckoutRequest;
use App\Security\CartTokenResolver;
use App\Service\CheckoutService;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/order')]
final class CheckoutController extends AbstractController
{
    public function __construct(
        private readonly CartTokenResolver $tokenResolver,
        private readonly CheckoutService $service,
    ) {}

    /**
     * @throws OptimisticLockException
     */
    #[Route('/checkout', name: 'api_order_checkout', methods: ['POST'])]
    #[CheckoutResponse]
    public function checkout(#[MapRequestPayload] CheckoutRequest $dto, Request $request): JsonResponse
    {
        $token = $this->tokenResolver->resolveRawToken($request);
        if (!$token) {
            return $this->json(['message' => 'Отсутствует заголовок X-Cart-Token'], 400);
        }

        $order = $this->service->checkout($token, $dto);

        return $this->json([
            'id'            => (string)$order->getId(),
            'status'        => $order->getStatus(),
            'customerEmail' => $order->getCustomer()->getEmail(),
        ]);
    }
}