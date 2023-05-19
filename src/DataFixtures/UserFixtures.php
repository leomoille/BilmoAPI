<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public const USER_REFERENCE = [
        '0',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
    ];

    public function getDependencies(): array
    {
        return [
            ClientFixtures::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < count(self::USER_REFERENCE); ++$i) {
            $user = new User();
            $user
                ->setUsername('utilisateur '.$i)
                ->setEmail('utilisateur'.$i.'@bilmo.com')
                ->setClient($this->getReference(ClientFixtures::CLIENT_REFERENCE));

            $manager->persist($user);
            $this->addReference(self::USER_REFERENCE[$i], $user);
        }

        $manager->flush();
    }
}
