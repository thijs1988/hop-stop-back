<?php

namespace App\Factory;

use App\Entity\Brands;
use App\Repository\BrandsRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Brands>
 *
 * @method static Brands|Proxy createOne(array $attributes = [])
 * @method static Brands[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Brands|Proxy find(object|array|mixed $criteria)
 * @method static Brands|Proxy findOrCreate(array $attributes)
 * @method static Brands|Proxy first(string $sortedField = 'id')
 * @method static Brands|Proxy last(string $sortedField = 'id')
 * @method static Brands|Proxy random(array $attributes = [])
 * @method static Brands|Proxy randomOrCreate(array $attributes = [])
 * @method static Brands[]|Proxy[] all()
 * @method static Brands[]|Proxy[] findBy(array $attributes)
 * @method static Brands[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Brands[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static BrandsRepository|RepositoryProxy repository()
 * @method Brands|Proxy create(array|callable $attributes = [])
 */
final class BrandsFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'name' => self::faker()->text(),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Brands $brands): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Brands::class;
    }
}
