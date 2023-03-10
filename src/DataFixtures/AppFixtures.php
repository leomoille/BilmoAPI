<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private const USERS_AMOUNT = 10;
    private const PRODUCTS_AMOUNT = 20;

    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $userList = [];

        // Create client
        $client = new Client();
        $client
            ->setEmail('client@smart.phone')
            ->setPassword($this->userPasswordHasher->hashPassword($client, 'client'));

        $manager->persist($client);

        // Create users
        for ($i = 0; $i < self::USERS_AMOUNT; $i++) {
            $user = new User();
            $user
                ->setUsername('utilisateur '.$i)
                ->setEmail('utilisateur'.$i.'@bilmo.com')
                ->setClient($client);

            if (mt_rand(0, 4)) {
                $manager->persist($user);
            }
            $userList[] = $user;
        }
        // Create products
        for ($i = 0; $i < self::PRODUCTS_AMOUNT; $i++) {
            $product = new Product();
            $product
                ->setName('Produit '.$i)
                ->setPrice(mt_rand(100, 1500))
                ->addUser($userList[array_rand($userList)])
                ->setClient($client);

            $manager->persist($product);
        }

        $manager->flush();
    }
}
