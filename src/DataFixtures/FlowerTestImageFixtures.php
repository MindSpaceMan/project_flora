<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\ProductImage;
use App\Repository\ProductRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\ConsoleOutput;

final class FlowerTestImageFixtures extends Fixture
{
    public function __construct(
        private readonly ProductRepository $products
    ) {}

    public function load(ObjectManager $manager): void
    {
        $io = new SymfonyStyle(new \Symfony\Component\Console\Input\ArrayInput([]), new ConsoleOutput());

        /**
         * Карта: slug продукта => массив картинок.
         * Можно класть несколько изображений на один товар.
         * Источники:
         *  - John Scheepers (официальный ритейлер луковичных)
         *  - Holland Bulb Farms (ритейлер)
         *  - NetherlandBulb (дистрибьютор)
         *  - Ball Horticultural / Ball Seed (орнаментальные культуры)
         */

        $map = [

            // ---------- TULIPA ----------
            // Tulip 'Spring Green' (viridiflora)
            'tyulpan-liliecvetnyy-kim-de-boer' => [
                [
                    // John Scheepers (прямая картинка со страницы сорта)
                    'url'       => 'https://www.bulbi.nl/media/catalog/product/cache/fee55e627bb23b05e67e630ae68979e2/T/u/Tulipa_Acuminata_1_1.webp',
                    'alt'       => 'Tulip “Spring Green” (Viridiflora)',
                    'primary'   => true,
                    'sortOrder' => 0,
                ],
            ],

            // Tulip 'Queen of Night'
            'tyulpan-mahrovyy-ranniy-double-princess' => [
                [
                    // Holland Bulb Farms — карточка товара
                    'url'       => 'https://upload.wikimedia.org/wikipedia/commons/4/4d/Tulip_-_closeup.jpg',
                    'alt'       => 'Tulip “Queen of Night” (Single Late)',
                    'primary'   => true,
                    'sortOrder' => 0,
                ],
            ],

            // Tulip 'Ballerina' (lily-flowered)
            'tyulpan-darvinov-gibrid-world-legendary' => [
                [
                    // NetherlandBulb — карточка товара
                    'url'       => 'https://www.bulbi.nl/media/catalog/product/cache/fee55e627bb23b05e67e630ae68979e2/T/u/Tulipa_Aafke_1_1_3.webp',
                    'alt'       => 'Tulip “Ballerina” (Lily-flowered)',
                    'primary'   => true,
                    'sortOrder' => 0,
                ],
            ],

            // Если в товарах был Ballade / Ballade Dream — положим картинку обычного Ballade
            'tyulpan-liliecvetnyy-ballade-dream' => [
                [
                    // Альтернативный поставщик — если используешь slug tulip-ballade-dream, просто переименуй ключ
                    'url'       => 'https://www.hartsnursery.co.uk/media/catalog/product/cache/9e1f1b8d3f3c7b93c4a19a67b7a4f1d2/t/u/tulip_ballade_1.jpg',
                    'alt'       => 'Tulip “Ballade” (Lily-flowered)',
                    'primary'   => true,
                    'sortOrder' => 0,
                ],
            ],
            // или, если у тебя slug именно для “Ballade Dream”:
            // 'tulip-ballade-dream' => [ ...тот же массив ... ],

            // ---------- EUSTOMA / LISANTHUS ----------
            // Lisianthus grandiflorum ‘Rosanne Brown’
            'eustoma-mahrovaya-rosita-white' => [
                [
                    // Ball Horticultural / Ball Seed — страница сорта (миниатюра, но с официального источника)
                    'url'       => 'https://ballseed.com/plant_info_images/dispthumb.aspx?filename=Lisianthus%20Rosanne%20Brown%20(R)',
                    'alt'       => 'Lisianthus “Rosanne Brown” (double)',
                    'primary'   => true,
                    'sortOrder' => 0,
                ],
            ],

//             Пример для Alissa White (если такой товар есть) — при желании добавишь свой URL:
             'eustoma-mahrovaya-arena-champagne' => [
                 [
                     'url'       => 'https://upload.wikimedia.org/wikipedia/commons/3/36/Lisianthus_grandiflorum_Florida_Silver_1zz.jpg',
                     'alt'       => 'Lisianthus “Alissa White” (double)',
                     'primary'   => true,
                     'sortOrder' => 0,
                 ],
             ],

            // Пример для Arena III White — добавь, когда определишь подходящий официальный источник:
             'eustoma-mahrovaya-rosanne-green' => [
                 [
                     'url'       => 'https://upload.wikimedia.org/wikipedia/commons/7/74/Lisianthus_grandiflorum_Florida_Silver_2zz.jpg',
                     'alt'       => 'Lisianthus “Arena III White”',
                     'primary'   => true,
                     'sortOrder' => 0,
                 ],
             ],
            'eustoma-mahrovaya-corelli-light-pink' => [
                [
                    'url'       => 'https://upload.wikimedia.org/wikipedia/commons/c/cb/Lisianthus_Lisa_Pink_5zz.jpg',
                    'alt'       => 'Lisianthus “Arena III White”',
                    'primary'   => true,
                    'sortOrder' => 0,
                ],
            ],
        ];

        $added = 0;

        foreach ($map as $slug => $images) {
            /** @var Product|null $product */
            $product = $this->products->findOneBy(['slug' => $slug]);

            if (!$product) {
                $io->warning(sprintf('Пропускаю "%s": товар не найден по slug.', $slug));
                continue;
            }

            $order = 0;
            foreach ($images as $img) {
                $image = (new ProductImage())
                    ->setProduct($product)
                    ->setUrl($img['url'])
                    ->setAlt($img['alt'] ?? null)
                    ->setSortOrder($img['sortOrder'] ?? $order)
                    ->setIsPrimary((bool)($img['primary'] ?? ($order === 0)));

                $manager->persist($image);
                $added++;
                $order++;
            }
        }

        $manager->flush();

        $io->success(sprintf('Готово. Добавлено изображений: %d', $added));
    }

    public function getDependencies(): array
    {
        // Фикстуры продуктов должны быть загружены раньше
        return [
            FlowerTestFixtures::class,
        ];
    }
}