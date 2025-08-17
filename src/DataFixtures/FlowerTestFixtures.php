<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;

final class FlowerTestFixtures extends Fixture
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
    }

    public function load(ObjectManager $manager): void
    {
        /** @var EntityManagerInterface $em */
        $em = $this->registry->getManager();

        // Найдём нужные категории (меняй слаги, если у тебя другие)
        $categorySlugs = [
            'tyulpany' => null, // Тюльпаны
            'eustomy'  => null, // Эустомы (Лизиантусы)
        ];

        foreach (array_keys($categorySlugs) as $slug) {
            $category = $em->getRepository(Category::class)->findOneBy(['slug' => $slug]);
            if (!$category instanceof Category) {
                // Мягкий фейл — чтобы было понятно в консоли, почему продукты не создаются
                echo sprintf("[ProductFixtures] Внимание: категория со слагом '%s' не найдена. Пропускаю её продукты.\n", $slug);
            }
            $categorySlugs[$slug] = $category;
        }

        // ---------- ДАННЫЕ ДЛЯ СОЗДАНИЯ ПРОДУКТОВ ----------
        // Высоты ориентировочные; если высота 0 или неизвестна — ставим null
        $items = [
            // --- ТЮЛЬПАНЫ ---
            [
                'category'     => 'tyulpany',
                'titleRu'      => 'Тюльпан лилиецветный «Kim de Boer»',
                'latinName'    => 'Tulipa (Lily-flowered) “Kim de Boer”',
                'description'  => 'Лилиецветный тюльпан с ярко-жёлтыми заострёнными лепестками и стройными высокими цветоносами; эффектный для срезки и миксбордеров.',
                'heightCm'     => 50,
                'slug'         => null, // сгенерим автоматически
                'metaTitle'    => 'Тюльпан Kim de Boer — лилиецветный | Флора Микс',
                'metaDesc'     => 'Лилиецветный тюльпан Kim de Boer с ярко-жёлтыми цветками. Купить посадочный материал с бережной доставкой по РФ.',
            ],
            [
                'category'     => 'tyulpany',
                'titleRu'      => 'Тюльпан махровый ранний «Double Princess»',
                'latinName'    => 'Tulipa (Double Early) “Double Princess”',
                'description'  => 'Пионовидные пурпурные цветки с плотными лепестками. Идеален для контейнеров и ранних весенних композиций.',
                'heightCm'     => 35,
                'slug'         => null,
                'metaTitle'    => 'Тюльпан Double Princess — махровый ранний | Флора Микс',
                'metaDesc'     => 'Пионовидный тюльпан Double Princess: насыщённый пурпур, раннее цветение, подходит для срезки.',
            ],
            [
                'category'     => 'tyulpany',
                'titleRu'      => 'Тюльпан Дарвинов гибрид «World Legendary»',
                'latinName'    => 'Tulipa (Darwin Hybrid) “World Legendary”',
                'description'  => 'Крупные двуцветные цветки: огненно-красные лепестки с золотисто-жёлтой каймой. Высокие прочные стебли.',
                'heightCm'     => 55,
                'slug'         => null,
                'metaTitle'    => 'Тюльпан World Legendary — Дарвинов гибрид | Флора Микс',
                'metaDesc'     => 'Яркий двуцветный тюльпан World Legendary. Крупный бутон, крепкий стебель — для сада и срезки.',
            ],
            [
                'category'     => 'tyulpany',
                'titleRu'      => 'Тюльпан лилиецветный «Ballade Dream»',
                'latinName'    => 'Tulipa (Lily-flowered) “Ballade Dream”',
                'description'  => 'Изящные заострённые лепестки с мягким переходом тонов. Эффектен в групповых посадках.',
                'heightCm'     => 45,
                'slug'         => null,
                'metaTitle'    => 'Тюльпан Ballade Dream — лилиецветный | Флора Микс',
                'metaDesc'     => 'Нежные оттенки и изящная форма лилиецветного тюльпана Ballade Dream. Доставка по РФ.',
            ],

            // --- ЭУСТОМЫ (ЛИЗИАНТУСЫ) ---
            [
                'category'     => 'eustomy',
                'titleRu'      => 'Эустома махровая «Rosita White»',
                'latinName'    => 'Eustoma grandiflorum “Rosita White”',
                'description'  => 'Классические махровые, розовидные белые цветки с толстыми лепестками. Отличная транспортабельность и стойкость в срезке.',
                'heightCm'     => 70,
                'slug'         => null,
                'metaTitle'    => 'Эустома Rosita White — махровая белая | Флора Микс',
                'metaDesc'     => 'Серия Rosita: белая махровая эустома для срезки и букета. Качественный посадочный материал.',
            ],
            [
                'category'     => 'eustomy',
                'titleRu'      => 'Эустома махровая «Arena Champagne»',
                'latinName'    => 'Eustoma grandiflorum “Arena Champagne”',
                'description'  => 'Крупные густомахровые цветки шампань-персикового оттенка, крепкий цветонос. Группа III (летнее цветение).',
                'heightCm'     => 80,
                'slug'         => null,
                'metaTitle'    => 'Эустома Arena Champagne — крупноцветковая | Флора Микс',
                'metaDesc'     => 'Махровая эустома Arena Champagne: персиковые оттенки, прочные стебли, отлична для срезки.',
            ],
            [
                'category'     => 'eustomy',
                'titleRu'      => 'Эустома махровая «Rosanne Green»',
                'latinName'    => 'Eustoma grandiflorum “Rosanne Green”',
                'description'  => 'Необычная зелёная окраска махровых цветков. Плотные лепестки, высокая декоративность в букетах.',
                'heightCm'     => 75,
                'slug'         => null,
                'metaTitle'    => 'Эустома Rosanne Green — махровая зелёная | Флора Микс',
                'metaDesc'     => 'Редкий зелёный оттенок серии Rosanne. Доставка с соблюдением терморежима.',
            ],
            [
                'category'     => 'eustomy',
                'titleRu'      => 'Эустома махровая «Corelli Light Pink»',
                'latinName'    => 'Eustoma grandiflorum “Corelli Light Pink”',
                'description'  => 'Воздушные, сильно гофрированные лепестки нежно-розового цвета. Популярна для свадебной флористики.',
                'heightCm'     => 70,
                'slug'         => null,
                'metaTitle'    => 'Эустома Corelli Light Pink — гофрированная | Флора Микс',
                'metaDesc'     => 'Нежно-розовая гофрированная эустома Corelli. Посадочный материал для профессионалов и любителей.',
            ],
        ];

        foreach ($items as $i => $row) {
            /** @var Category|null $cat */
            $cat = $categorySlugs[$row['category']] ?? null;
            if (!$cat instanceof Category) {
                // Категория не найдена — пропускаем продукт
                continue;
            }

            $product = new Product();
            $product->setCategory($cat);
            $product->setTitleRu($row['titleRu']);
            $product->setLatinName($row['latinName']);

            $desc = $row['description'] ?? null;
            $product->setDescription($desc);

            // null вместо 0
            $height = \is_int($row['heightCm'] ?? null) ? (int)$row['heightCm'] : null;
            if ($height === 0) {
                $height = null;
            }
            $product->setHeightCm($height);

            // Генерация слага: если не задан — по русскому названию
            $slug = $row['slug'] ?? null;
            if (!$slug) {
                $slug = $this->slugify($row['titleRu']);
            }
            // Немного уникальности на случай дубликатов названий
            $slug = $this->ensureUniqueSlug($em, $slug);
            $product->setSlug($slug);

            // Мета-теги
            $product->setMetaTitle($row['metaTitle'] ?? null);
            $product->setMetaDescription($row['metaDesc'] ?? null);

            $em->persist($product);
        }

        $em->flush();
        echo "[ProductFixtures] Созданы продукты для категорий tyulpany/eustomy.\n";
    }

    /**
     * Простой слагифайер: транслит RU -> латиница + нормализация.
     */
    private function slugify(string $value): string
    {
        $ru = [
            'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
            'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'
        ];
        $en = [
            'a','b','v','g','d','e','e','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','sch','','y','','e','yu','ya',
            'a','b','v','g','d','e','e','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','sch','','y','','e','yu','ya'
        ];

        $value = str_replace($ru, $en, $value);
        $value = \iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;
        $value = \preg_replace('~[^a-zA-Z0-9]+~', '-', $value);
        $value = \trim($value ?? '', '-');
        $value = \mb_strtolower($value);

        return $value ?: 'item';
    }

    /**
     * Делает слаг уникальным (добавляет индекс, если занят).
     */
    private function ensureUniqueSlug(ObjectManager $em, string $base): string
    {
        $slug = $base;
        $i = 2;

        while ($em->getRepository(Product::class)->findOneBy(['slug' => $slug]) instanceof Product) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}