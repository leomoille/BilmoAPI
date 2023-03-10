<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $userList = [];

        // Create users
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user
                ->setUsername('utilisateur '.$i)
                ->setEmail('utilisateur'.$i.'@bilmo.com');

            $manager->persist($user);
            $userList[] = $user;
        }
        // Create products
        for ($i = 0; $i < 20; $i++) {
            $product = new Product();
            $product
                ->setName('Produit '.$i)
                ->setPrice(mt_rand(100, 1500))
                ->addUser($userList[array_rand($userList)]);

            $manager->persist($product);
        }

        $manager->flush();
    }
}
