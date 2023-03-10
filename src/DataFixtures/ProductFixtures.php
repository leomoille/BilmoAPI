<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    private const PRODUCTS_AMOUNT = 20;

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ClientFixtures::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < self::PRODUCTS_AMOUNT; $i++) {
            $product = new Product();
            $product
                ->setName('Produit '.$i)
                ->setPrice(mt_rand(100, 1500))
                ->addUser($this->getReference(UserFixtures::USER_REFERENCE[array_rand(UserFixtures::USER_REFERENCE)]))
                ->setClient($this->getReference(ClientFixtures::CLIENT_REFERENCE));

            $manager->persist($product);
        }

        $manager->flush();
    }
}
