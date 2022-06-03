<?php

namespace App\Factory;

use App\Entity\CartItems;
use App\Repository\CartItemsRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<CartItems>
 *
 * @method static CartItems|Proxy createOne(array $attributes = [])
 * @method static CartItems[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static CartItems|Proxy find(object|array|mixed $criteria)
 * @method static CartItems|Proxy findOrCreate(array $attributes)
 * @method static CartItems|Proxy first(string $sortedField = 'id')
 * @method static CartItems|Proxy last(string $sortedField = 'id')
 * @method static CartItems|Proxy random(array $attributes = [])
 * @method static CartItems|Proxy randomOrCreate(array $attributes = [])
 * @method static CartItems[]|Proxy[] all()
 * @method static CartItems[]|Proxy[] findBy(array $attributes)
 * @method static CartItems[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static CartItems[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static CartItemsRepository|RepositoryProxy repository()
 * @method CartItems|Proxy create(array|callable $attributes = [])
 */
final class CartItemsFactory extends ModelFactory
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
            // TODO add DATETIME ORM type manually
            // TODO add DATETIME ORM type manually
            'cartOwner' => UserFactory::new()->create(),
            'paid' => self::faker()->boolean(),
            'shipped' => self::faker()->boolean(),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(CartItems $cartItems): void {})
        ;
    }

    protected static function getClass(): string
    {
        return CartItems::class;
    }
}
