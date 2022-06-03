<?php

namespace App\Factory;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Products>
 *
 * @method static Products|Proxy createOne(array $attributes = [])
 * @method static Products[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Products|Proxy find(object|array|mixed $criteria)
 * @method static Products|Proxy findOrCreate(array $attributes)
 * @method static Products|Proxy first(string $sortedField = 'id')
 * @method static Products|Proxy last(string $sortedField = 'id')
 * @method static Products|Proxy random(array $attributes = [])
 * @method static Products|Proxy randomOrCreate(array $attributes = [])
 * @method static Products[]|Proxy[] all()
 * @method static Products[]|Proxy[] findBy(array $attributes)
 * @method static Products[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Products[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static ProductsRepository|RepositoryProxy repository()
 * @method Products|Proxy create(array|callable $attributes = [])
 */
final class ProductsFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
//        $category = CategoriesFactory::new()->create();
        $brand = BrandsFactory::new()->create();
        $inv = random_int(1, 60);
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'name' => self::faker()->text(),
            'type' => self::faker()->text(),
            'price' => self::faker()->randomFloat(2, 2, 100),
            'brands' => $brand,
//            'categories' => [$category],
            'description' => self::faker()->text(),
            'inventory' => $inv,
            'abv' => '8.5',
            'ibu' => '50',
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Products $products): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Products::class;
    }
}
