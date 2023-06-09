<?php

namespace App\Factory;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Client>
 *
 * @method        Client|Proxy                     create(array|callable $attributes = [])
 * @method static Client|Proxy                     createOne(array $attributes = [])
 * @method static Client|Proxy                     find(object|array|mixed $criteria)
 * @method static Client|Proxy                     findOrCreate(array $attributes)
 * @method static Client|Proxy                     first(string $sortedField = 'id')
 * @method static Client|Proxy                     last(string $sortedField = 'id')
 * @method static Client|Proxy                     random(array $attributes = [])
 * @method static Client|Proxy                     randomOrCreate(array $attributes = [])
 * @method static ClientRepository|RepositoryProxy repository()
 * @method static Client[]|Proxy[]                 all()
 * @method static Client[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Client[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Client[]|Proxy[]                 findBy(array $attributes)
 * @method static Client[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Client[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class ClientFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function getDefaults(): array
    {
        return [
            'email' => self::faker()->email(),
            'password' => 'password',
            'roles' => [],
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this->afterInstantiate(function (Client $client): void {
            $client->setPassword($this->passwordHasher->hashPassword($client, $client->getPassword()));
        });
    }

    protected static function getClass(): string
    {
        return Client::class;
    }
}
