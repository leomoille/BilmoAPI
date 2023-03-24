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
            "price" => 101900,
        ],
        [
            "name"  => "iPhone 14 Plus",
            "brand" => "Apple",
            "price" => 116900,
        ],
        [
            "name"  => "iPhone 14 Pro",
            "brand" => "Apple",
            "price" => 132900,
        ],
        [
            "name"  => "iPhone 14 Pro Max",
            "brand" => "Apple",
            "price" => 147900,
        ],
        [
            "name"  => "iPhone 14 Pro Max",
            "brand" => "Apple",
            "price" => 147900,
        ],
        [
            "name"  => "iPhone 13",
            "brand" => "Apple",
            "price" => 80900,
        ],
        [
            "name"  => "iPhone 13 mini",
            "brand" => "Apple",
            "price" => 90900,
        ],
        [
            "name"  => "iPhone 12",
            "brand" => "Apple",
            "price" => 80900,
        ],
        [
            "name"  => "iPhone SE",
            "brand" => "Apple",
            "price" => 55900,
        ],
        [
            "name"  => "Galaxy A54",
            "brand" => "Samsung",
            "price" => 49900,
        ],
        [
            "name"  => "Galaxy A53",
            "brand" => "Samsung",
            "price" => 40900,
        ],
        [
            "name"  => "Galaxy A34",
            "brand" => "Samsung",
            "price" => 39900,
        ],
        [
            "name"  => "Galaxy A33",
            "brand" => "Samsung",
            "price" => 33900,
        ],
        [
            "name"  => "Galaxy A14",
            "brand" => "Samsung",
            "price" => 24900,
        ],
        [
            "name"  => "Galaxy S23 Ultra",
            "brand" => "Samsung",
            "price" => 159900,
        ],
        [
            "name"  => "Galaxy S23",
            "brand" => "Samsung",
            "price" => 95900,
        ],
        [
            "name"  => "Galaxy Z Fold 4",
            "brand" => "Samsung",
            "price" => 159900,
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
