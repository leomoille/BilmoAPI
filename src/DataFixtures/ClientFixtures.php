<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientFixtures extends Fixture
{
    public const CLIENT_REFERENCE = 'client';
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $client = new Client();
        $client
            ->setEmail('client@smart.phone')
            ->setPassword($this->userPasswordHasher->hashPassword($client, 'client'));

        $manager->persist($client);

        $this->addReference(self::CLIENT_REFERENCE, $client);

        $manager->flush();
    }
}
