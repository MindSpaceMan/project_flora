<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Random\RandomException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class CartService
{
    public function __construct(
        private OrderRepository $orderRepository,
        private ProductRepository $productRepository,
        private OrderItemRepository $orderItemRepository,
        private EntityManagerInterface $em,
    ) {}

    public function getCart(string $orderId): Order
    {
        $uuid = Uuid::fromString($orderId);
        $order = $this->orderRepository->createQueryBuilder('o')
            ->leftJoin('o.items', 'i')->addSelect('i')
            ->leftJoin('i.product', 'p')->addSelect('p')
            ->andWhere('o.id = :id')->setParameter('id', $uuid)
            ->getQuery()->getOneOrNullResult();

        if (!$order) {
            throw new NotFoundHttpException('Корзина (заказ) не найдена()');
        }
        return $order;
    }

    public function addItem(string $orderId, string $productId, int $quantity = 1): Order
    {
        if ($quantity < 1) {
            throw new BadRequestHttpException('Количество должно быть >= 1');
        }

        $order = $this->getCart($orderId);
        $product = $this->getProductOrThrow($productId);

        // Ищем существующую позицию по product
        foreach ($order->getItems() as $item) {
            if ($item->getProduct()?->getId()?->equals($product->getId())) {
                $item->setQuantity($item->getQuantity() + $quantity);
                $this->em->flush();
                return $this->getCart($orderId);
            }
        }

        // Создаём новую
        $item = new OrderItem();
        $item->setOrder($order)
            ->setProduct($product)
            ->setQuantity($quantity);

        $order->addItem($item);
        $this->em->persist($item);
        $this->em->flush();

        return $this->getCart($orderId);
    }

    public function removeItem(string $orderId, string $orderItemId): Order
    {
        $order = $this->getCart($orderId);
        $itemUuid = Uuid::fromString($orderItemId);

        /** @var OrderItem|null $item */
        $item = $this->orderItemRepository->find($itemUuid);
        if (!$item) {
            throw new NotFoundHttpException('Элемент заказа не найден');
        }
        if ($item->getOrder()?->getId()?->toString() !== $order->getId()?->toString()) {
            throw new BadRequestHttpException('Элемент заказа не относится к этой корзине');
        }

        $order->removeItem($item);
        $this->em->remove($item);
        $this->em->flush();

        return $this->getCart($orderId);
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
     * @throws RandomException
     */
    public function getOrCreateCartByToken(?string $rawToken, bool &$wasCreated = false): array
    {
        // если токена нет — создаём новый
        if (!$rawToken) {
            $rawToken = bin2hex(random_bytes(32)); // 64 hex
            $wasCreated = true;
        }

        $hash = hash('sha256', $rawToken);

        $order = $this->orderRepository->createQueryBuilder('o')
            ->leftJoin('o.items', 'i')->addSelect('i')
            ->leftJoin('i.product', 'p')->addSelect('p')
            ->andWhere('o.cartTokenHash = :h')->setParameter('h', $hash)
            ->andWhere('o.status = :status')->setParameter('status', 'cart')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();

        if (!$order) {
            $order = new Order();
            $order->setStatus('cart');
            $order->setCartTokenHash($hash);
            $this->em->persist($order);
            $this->em->flush();
            $wasCreated = true;
        }

        return [$order, $rawToken];
    }

    /**
     * @throws RandomException
     */
    public function addItemByToken(string $rawToken, string $productId, int $quantity = 1): Order
    {
        $wasCreated = false;
        [$order] = $this->getOrCreateCartByToken($rawToken, $wasCreated);
        return $this->addItem($order->getId()->toString(), $productId, $quantity);
    }

    public function removeItemByToken(string $rawToken, string $orderItemId): Order
    {
        $wasCreated = false;
        [$order] = $this->getOrCreateCartByToken($rawToken, $wasCreated);
        return $this->removeItem($order->getId()->toString(), $orderItemId);
    }
}