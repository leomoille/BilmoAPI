<?php

namespace App\Factory;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Product>
 *
 * @method        Product|Proxy                     create(array|callable $attributes = [])
 * @method static Product|Proxy                     createOne(array $attributes = [])
 * @method static Product|Proxy                     find(object|array|mixed $criteria)
 * @method static Product|Proxy                     findOrCreate(array $attributes)
 * @method static Product|Proxy                     first(string $sortedField = 'id')
 * @method static Product|Proxy                     last(string $sortedField = 'id')
 * @method static Product|Proxy                     random(array $attributes = [])
 * @method static Product|Proxy                     randomOrCreate(array $attributes = [])
 * @method static ProductRepository|RepositoryProxy repository()
 * @method static Product[]|Proxy[]                 all()
 * @method static Product[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Product[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Product[]|Proxy[]                 findBy(array $attributes)
 * @method static Product[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Product[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class ProductFactory extends ModelFactory
{
    private const SMARTPHONES = [
        [
            'name' => 'iPhone 14',
            'brand' => 'Apple',
        ],
        [
            'name' => 'iPhone 14 Plus',
            'brand' => 'Apple',
        ],
        [
            'name' => 'iPhone 14 Pro',
            'brand' => 'Apple',
        ],
        [
            'name' => 'iPhone 14 Pro Max',
            'brand' => 'Apple',
        ],
        [
            'name' => 'iPhone 13',
            'brand' => 'Apple',
        ],
        [
            'name' => 'iPhone 13 mini',
            'brand' => 'Apple',
        ],
        [
            'name' => 'iPhone 13 Pro',
            'brand' => 'Apple',
        ],
        [
            'name' => 'iPhone 13 Pro Max',
            'brand' => 'Apple',
        ],
        [
            'name' => 'iPhone 12',
            'brand' => 'Apple',
        ],
        [
            'name' => 'iPhone 12 mini',
            'brand' => 'Apple',
        ],
        [
            'name' => 'iPhone 12 Pro',
            'brand' => 'Apple',
        ],
        [
            'name' => 'iPhone 12 Pro Max',
            'brand' => 'Apple',
        ],
        [
            'name' => 'iPhone SE',
            'brand' => 'Apple',
        ],
        [
            'name' => 'Galaxy A14',
            'brand' => 'Samsung',
        ],
        [
            'name' => 'Galaxy A24',
            'brand' => 'Samsung',
        ],
        [
            'name' => 'Galaxy A34',
            'brand' => 'Samsung',
        ],
        [
            'name' => 'Galaxy A54',
            'brand' => 'Samsung',
        ],
        [
            'name' => 'Galaxy S22',
            'brand' => 'Samsung',
        ],
        [
            'name' => 'Galaxy S22 Ultra',
            'brand' => 'Samsung',
        ],
        [
            'name' => 'Galaxy S22+',
            'brand' => 'Samsung',
        ],
        [
            'name' => 'Galaxy Z Flip4',
            'brand' => 'Samsung',
        ],
        [
            'name' => 'Galaxy Z Fold4',
            'brand' => 'Samsung',
        ],
        [
            'name' => 'Galaxy S21 FE',
            'brand' => 'Samsung',
        ],
        [
            'name' => 'Galaxy S20 FE',
            'brand' => 'Samsung',
        ],
        [
            'name' => 'Galaxy S21',
            'brand' => 'Samsung',
        ],
        [
            'name' => 'Galaxy S21 Ultra',
            'brand' => 'Samsung',
        ],
        [
            'name' => 'Galaxy S21+',
            'brand' => 'Samsung',
        ],
        [
            'name' => 'Galaxy S20',
            'brand' => 'Samsung',
        ],
        [
            'name' => 'Galaxy S20+',
            'brand' => 'Samsung',
        ],
        [
            'name' => 'Galaxy Note 20',
            'brand' => 'Samsung',
        ],
        [
            'name' => 'Galaxy Note 20 Ultra',
            'brand' => 'Samsung',
        ],
    ];

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function getDefaults(): array
    {
        $smartphone = self::faker()->randomElement(self::SMARTPHONES);

        return [
            'brand' => $smartphone['brand'],
            'name' => $smartphone['name'],
            'price' => self::faker()->numberBetween(45000, 200000),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this; // ->afterInstantiate(function(Product $product): void {})
    }

    protected static function getClass(): string
    {
        return Product::class;
    }
}
