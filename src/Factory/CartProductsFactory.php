<?php

namespace App\Factory;

use App\Entity\CartProducts;
use App\Repository\CartProductsRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<CartProducts>
 *
 * @method static CartProducts|Proxy createOne(array $attributes = [])
 * @method static CartProducts[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static CartProducts|Proxy find(object|array|mixed $criteria)
 * @method static CartProducts|Proxy findOrCreate(array $attributes)
 * @method static CartProducts|Proxy first(string $sortedField = 'id')
 * @method static CartProducts|Proxy last(string $sortedField = 'id')
 * @method static CartProducts|Proxy random(array $attributes = [])
 * @method static CartProducts|Proxy randomOrCreate(array $attributes = [])
 * @method static CartProducts[]|Proxy[] all()
 * @method static CartProducts[]|Proxy[] findBy(array $attributes)
 * @method static CartProducts[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static CartProducts[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static CartProductsRepository|RepositoryProxy repository()
 * @method CartProducts|Proxy create(array|callable $attributes = [])
 */
final class CartProductsFactory extends ModelFactory
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
            'amount' => self::faker()->randomNumber(),
            'price' => self::faker()->randomFloat(2, 0, 4),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(CartProducts $cartProducts): void {})
        ;
    }

    protected static function getClass(): string
    {
        return CartProducts::class;
    }
}
