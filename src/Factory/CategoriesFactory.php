<?php

namespace App\Factory;

use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Categories>
 *
 * @method static Categories|Proxy createOne(array $attributes = [])
 * @method static Categories[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Categories|Proxy find(object|array|mixed $criteria)
 * @method static Categories|Proxy findOrCreate(array $attributes)
 * @method static Categories|Proxy first(string $sortedField = 'id')
 * @method static Categories|Proxy last(string $sortedField = 'id')
 * @method static Categories|Proxy random(array $attributes = [])
 * @method static Categories|Proxy randomOrCreate(array $attributes = [])
 * @method static Categories[]|Proxy[] all()
 * @method static Categories[]|Proxy[] findBy(array $attributes)
 * @method static Categories[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Categories[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static CategoriesRepository|RepositoryProxy repository()
 * @method Categories|Proxy create(array|callable $attributes = [])
 */
final class CategoriesFactory extends ModelFactory
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
            'name' => self::faker()->text(10),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Categories $categories): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Categories::class;
    }
}
