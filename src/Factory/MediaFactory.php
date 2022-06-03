<?php

namespace App\Factory;

use App\Entity\Media;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Media>
 *
 * @method static Media|Proxy createOne(array $attributes = [])
 * @method static Media[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Media|Proxy find(object|array|mixed $criteria)
 * @method static Media|Proxy findOrCreate(array $attributes)
 * @method static Media|Proxy first(string $sortedField = 'id')
 * @method static Media|Proxy last(string $sortedField = 'id')
 * @method static Media|Proxy random(array $attributes = [])
 * @method static Media|Proxy randomOrCreate(array $attributes = [])
 * @method static Media[]|Proxy[] all()
 * @method static Media[]|Proxy[] findBy(array $attributes)
 * @method static Media[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Media[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method Media|Proxy create(array|callable $attributes = [])
 */
final class MediaFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->text(),
            'filePath' => '-61fd7cbfd30db.jpeg'
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Media $media): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Media::class;
    }
}
