<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {

        $rows = [
            ['name' => 'Тюльпаны',   'slug' => 'tyulpany',   'isActive' => true, 'imagePath' => '/assets/tulip.jpg'],
            ['name' => 'Эустомы',    'slug' => 'eustomy',    'isActive' => true, 'imagePath' => '/assets/eustoma.jpg'],
            ['name' => 'Розы',       'slug' => 'rozy',       'isActive' => false, 'imagePath' => '/assets/rose_popular.jpg'],
            ['name' => 'Орхидеи',    'slug' => 'orkhidei',   'isActive' => false, 'imagePath' => '/assets/orchideas.jpg'],
            ['name' => 'Хризантемы', 'slug' => 'khrizantemy','isActive' => false, 'imagePath' => '/assets/chrysanthemum.jpg'],
        ];

        foreach ($rows as $row) {
            $c = (new Category())
                ->setName($row['name'])
                ->setSlug($row['slug'])
                ->setIsActive($row['isActive']) // sortOrder/createdAt/updatedAt не трогаем
                ->setImagePath($row['imagePath']); // sortOrder/createdAt/updatedAt не трогаем
            $manager->persist($c);
        }

        $admin = new User();
        $admin->setEmail('admin@floramix.ru');
        $admin->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));

        $manager->persist($admin);

        $manager->flush();
    }
}
