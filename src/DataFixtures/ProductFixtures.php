<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    private const PRODUCTS = [
        [
            "name"  => "iPhone 14",
            "brand" => "Apple",
            "price" => 1019,
        ],
        [
            "name"  => "iPhone 14 Plus",
            "brand" => "Apple",
            "price" => 1169,
        ],
        [
            "name"  => "iPhone 14 Pro",
            "brand" => "Apple",
            "price" => 1329,
        ],
        [
            "name"  => "iPhone 14 Pro Max",
            "brand" => "Apple",
            "price" => 1479,
        ],
        [
            "name"  => "iPhone 14 Pro Max",
            "brand" => "Apple",
            "price" => 1479,
        ],
        [
            "name"  => "iPhone 13",
            "brand" => "Apple",
            "price" => 809,
        ],
        [
            "name"  => "iPhone 13 mini",
            "brand" => "Apple",
            "price" => 909,
        ],
        [
            "name"  => "iPhone 12",
            "brand" => "Apple",
            "price" => 809,
        ],
        [
            "name"  => "iPhone SE",
            "brand" => "Apple",
            "price" => 559,
        ],
        [
            "name"  => "Galaxy A54",
            "brand" => "Samsung",
            "price" => 499,
        ],
        [
            "name"  => "Galaxy A53",
            "brand" => "Samsung",
            "price" => 409,
        ],
        [
            "name"  => "Galaxy A34",
            "brand" => "Samsung",
            "price" => 399,
        ],
        [
            "name"  => "Galaxy A33",
            "brand" => "Samsung",
            "price" => 339,
        ],
        [
            "name"  => "Galaxy A14",
            "brand" => "Samsung",
            "price" => 249,
        ],
        [
            "name"  => "Galaxy S23 Ultra",
            "brand" => "Samsung",
            "price" => 1599,
        ],
        [
            "name"  => "Galaxy S23",
            "brand" => "Samsung",
            "price" => 959,
        ],
        [
            "name"  => "Galaxy Z Fold 4",
            "brand" => "Samsung",
            "price" => 1599,
        ],
    ];

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ClientFixtures::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < count(self::PRODUCTS); $i++) {
            $product = new Product();
            $product
                ->setName(self::PRODUCTS[$i]['name'])
                ->setBrand(self::PRODUCTS[$i]['brand'])
                ->setPrice(self::PRODUCTS[$i]['price'] * 100)
                ->addUser($this->getReference(UserFixtures::USER_REFERENCE[array_rand(UserFixtures::USER_REFERENCE)]))
                ->setClient($this->getReference(ClientFixtures::CLIENT_REFERENCE));

            $manager->persist($product);
        }

        $manager->flush();
    }
}
