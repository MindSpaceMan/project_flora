<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Enum\CouponDiscountEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $manager->flush();
    }
}
