<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\ResponseDTO\CheckoutResponse;
use App\Dto\CheckoutRequest;
use App\Service\CheckoutService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/order')]
final class CheckoutController extends AbstractController
{
    public function __construct(
        private readonly CheckoutService $service
    ) {}

    #[Route('/checkout', name: 'api_order_checkout', methods: ['POST'])]
    #[CheckoutResponse]
    public function checkout(#[MapRequestPayload] CheckoutRequest $dto, Request $request): JsonResponse
    {
        $cartToken = $request->headers->get('X-Cart-Token');
        if (!$cartToken) {
            return $this->json(['message' => 'Отсутствует заголовок X-Cart-Token'], 400);
        }

        try {
            $payload = (array) json_decode($request->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            throw new JsonException('Некорректный JSON body');
        }

        $order = $this->service->checkout($cartToken, $dto);

        return $this->json([
            'id'            => (string)$order->getId(),
            'status'        => $order->getStatus(),
            'customerEmail' => $order->getCustomer()->getEmail(),
        ]);
    }
}