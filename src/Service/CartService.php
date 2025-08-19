<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Security\CartTokenResolver;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Random\RandomException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class CartService
{
    public function __construct(
        private OrderRepository        $orderRepository,
        private CartTokenResolver      $cartTokenResolver,
        private ProductRepository      $productRepository,
        private EntityManagerInterface $em,
    )
    {
    }

    private function getProductOrThrow(string $productId): Product
    {
        $uuid = Uuid::fromString($productId);
        $product = $this->productRepository->find($uuid);
        if (!$product) {
            throw new NotFoundHttpException('Продукт не найден');
        }
        return $product;
    }

    /**
     */
    public function getCartByToken(string $rawToken): Order
    {
        $hash = hash('sha256', $rawToken);
        $order = $this->orderRepository->findCartBeforeCheckoutByToken($hash);
        if (!$order) {
            throw new NotFoundHttpException('Корзина не найдена');
        }

        return $order;
    }

    /**
     * @throws RandomException
     */
    public function addItemByToken(string $token, string $productId, int $quantity = 1): Order
    {
        $cart = $this->getCartByToken($token);
        if ($quantity < 1) {
            throw new BadRequestHttpException('Количество должно быть >= 1');
        }

        $product = $this->getProductOrThrow($productId);

        foreach ($cart->getItems() as $item) {
            if ($item->getProduct()?->getId()?->equals($productId)) {
                $item->setQuantity($item->getQuantity() + $quantity);
                $this->em->flush();
                return $cart;
            }
        }

        $item = new OrderItem();
        $item->setOrder($cart)
            ->setProduct($product)
            ->setQuantity($quantity);

        $cart->addItem($item);
        $this->em->persist($item);
        $this->em->flush();

        return $cart;

    }

    /**
     */
    public function removeItemByToken(string $rawToken, string $productId, int $quantity): Order
    {
        $cart = $this->getCartByToken($rawToken);
        $productUuid = Uuid::fromString($productId);

        /** @var OrderItem|null $item */
        $item = $cart->getItems()->findFirst(
            static fn($k, OrderItem $i) => ($i->getProduct()?->getId()?->equals($productUuid)) ?? false
        );

        if ($item) {
            $newQuantity = $item->getQuantity() - $quantity;

            if ($newQuantity <= 0) {
                $cart->removeItem($item);
            } else {
                $item->setQuantity($newQuantity);
            }

            $this->em->flush();
            return $cart;
        }

        return $cart;
    }

    /**
     * @throws RandomException
     */
    public function createCart(): array
    {
        $token = $this->cartTokenResolver->generateCartToken();
        $cart = new Order();
        $cart->setStatus('cart');
        $cart->setCartTokenHash($this->cartTokenResolver->hash($token));
        $this->em->persist($cart);
        $this->em->flush();

        return [
            'token' => $token,
            'cart' => $cart,
        ];
    }
}