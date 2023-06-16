<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use function Zenstruck\Foundry\faker;

class AppFixtures extends Fixture
{
    private const CLIENTS = [
        [
            'email' => 'client1@smart.phone',
            'password' => 'password',
        ],
        [
            'email' => 'client2@smart.phone',
            'password' => 'password',
        ],
        [
            'email' => 'client3@smart.phone',
            'password' => 'password',
        ],
    ];
    private const PRODUCTS = [
        [
            'name' => 'iPhone 14',
            'brand' => 'Apple',
            'price' => 101900,
        ],
        [
            'name' => 'iPhone 14 Plus',
            'brand' => 'Apple',
            'price' => 116900,
        ],
        [
            'name' => 'iPhone 14 Pro',
            'brand' => 'Apple',
            'price' => 132900,
        ],
        [
            'name' => 'iPhone 14 Pro Max',
            'brand' => 'Apple',
            'price' => 147900,
        ],
        [
            'name' => 'iPhone 13',
            'brand' => 'Apple',
            'price' => 90900,
        ],
        [
            'name' => 'iPhone 13 mini',
            'brand' => 'Apple',
            'price' => 80900,
        ],
        [
            'name' => 'iPhone 13 Pro',
            'brand' => 'Apple',
            'price' => 126500,
        ],
        [
            'name' => 'iPhone 13 Pro Max',
            'brand' => 'Apple',
            'price' => 130500,
        ],
        [
            'name' => 'iPhone 12',
            'brand' => 'Apple',
            'price' => 80900,
        ],
        [
            'name' => 'iPhone 12 mini',
            'brand' => 'Apple',
            'price' => 71600,
        ],
        [
            'name' => 'iPhone 12 Pro',
            'brand' => 'Apple',
            'price' => 107500,
        ],
        [
            'name' => 'iPhone 12 Pro Max',
            'brand' => 'Apple',
            'price' => 148900,
        ],
        [
            'name' => 'iPhone SE',
            'brand' => 'Apple',
            'price' => 79800,
        ],
        [
            'name' => 'Galaxy A14',
            'brand' => 'Samsung',
            'price' => 22900,
        ],
        [
            'name' => 'Galaxy A24',
            'brand' => 'Samsung',
            'price' => 34000,
        ],
        [
            'name' => 'Galaxy A34',
            'brand' => 'Samsung',
            'price' => 35000,
        ],
        [
            'name' => 'Galaxy A54',
            'brand' => 'Samsung',
            'price' => 49900,
        ],
        [
            'name' => 'Galaxy S22',
            'brand' => 'Samsung',
            'price' => 68800,
        ],
        [
            'name' => 'Galaxy S22 Ultra',
            'brand' => 'Samsung',
            'price' => 87500,
        ],
        [
            'name' => 'Galaxy S22+',
            'brand' => 'Samsung',
            'price' => 88400,
        ],
        [
            'name' => 'Galaxy Z Flip4',
            'brand' => 'Samsung',
            'price' => 129600,
        ],
        [
            'name' => 'Galaxy Z Fold4',
            'brand' => 'Samsung',
            'price' => 145200,
        ],
        [
            'name' => 'Galaxy S21 FE',
            'brand' => 'Samsung',
            'price' => 75900,
        ],
        [
            'name' => 'Galaxy S20 FE',
            'brand' => 'Samsung',
            'price' => 30000,
        ],
        [
            'name' => 'Galaxy S21',
            'brand' => 'Samsung',
            'price' => 90000,
        ],
        [
            'name' => 'Galaxy S21 Ultra',
            'brand' => 'Samsung',
            'price' => 56300,
        ],
        [
            'name' => 'Galaxy S21+',
            'brand' => 'Samsung',
            'price' => 64000,
        ],
        [
            'name' => 'Galaxy S20',
            'brand' => 'Samsung',
            'price' => 30000,
        ],
        [
            'name' => 'Galaxy S20+',
            'brand' => 'Samsung',
            'price' => 36079800,
        ],
        [
            'name' => 'Galaxy Note 20',
            'brand' => 'Samsung',
            'price' => 65000,
        ],
        [
            'name' => 'Galaxy Note 20 Ultra',
            'brand' => 'Samsung',
            'price' => 84000,
        ],
    ];

    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < count(self::CLIENTS); ++$i) {
            $client = new Client();
            $client
                ->setEmail(self::CLIENTS[$i]['email'])
                ->setPassword($this->passwordHasher->hashPassword($client, self::CLIENTS[$i]['password']));

            $manager->persist($client);

            /** @var User[] $users */
            $users = [];

            for ($j = 0; $j < faker()->numberBetween(10, 15); ++$j) {
                $user = new User();
                $user
                    ->setClient($client)
                    ->setEmail(faker()->email())
                    ->setUsername(faker()->userName());

                $users[] = $user;
                $manager->persist($user);
            }

            /** @var Product[] $products */
            $products = [];
            for ($k = 0; $k < count(self::PRODUCTS); ++$k) {
                $randomProduct = self::PRODUCTS[array_rand(self::PRODUCTS)];

                $product = new Product();

                $product
                    ->setName($randomProduct['name'])
                    ->setBrand($randomProduct['brand'])
                    ->setPrice($randomProduct['price'])
                    ->setClient($client);

                $products[] = $product;
                $manager->persist($product);
            }

            for ($productIndex = 0; $productIndex < count($products); ++$productIndex) {
                if (array_key_exists($productIndex, $users)) {
                    $products[$productIndex]->addUser($users[$productIndex]);
                    $manager->persist($products[$productIndex]);
                }
            }
        }

        $manager->flush();
    }
}
