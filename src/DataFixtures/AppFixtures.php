<?php

namespace App\DataFixtures;

use App\Factory\ClientFactory;
use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        ClientFactory::createOne([
            'email' => 'client1@smart.phone',
        ]);
        ClientFactory::createOne([
            'email' => 'client2@smart.phone',
        ]);
        ClientFactory::createOne([
            'email' => 'client3@smart.phone',
        ]);

        ProductFactory::createMany(14, function () {
            return [
                'client' => ClientFactory::random(),
            ];
        });

        UserFactory::createMany(20, function () {
            return [
                'products' => ProductFactory::randomRange(1, 1),
                'client' => ClientFactory::random(),
            ];
        });
    }
}
