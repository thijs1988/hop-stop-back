<?php
// api/src/Filter/RegexpFilter.php

namespace App\Filters;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

final class ProductFilter extends AbstractContextAwareFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if($property !== 'search'){
            return;
        }
        $alias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->innerJoin(sprintf('%s.brands', $alias), 'b')
            ->innerJoin(sprintf('%s.categories', $alias), 'c')
            ->andWhere(sprintf('%s.name LIKE :search OR %s.description LIKE :search OR %s.type LIKE :search OR b.name LIKE :search OR c.name LIKE :search', $alias, $alias, $alias))
            ->setParameter('search', '%'.$value.'%');
    }

    // This function is only used to hook in documentation generators (supported by Swagger and Hydra)
    public function getDescription(string $resourceClass): array
    {
        return [
            'search' => [
                'property' => null,
                'type' => 'string',
                'required' => false,
                'openapi' => [
                    'description' => 'Search across multiple fields',
                ],
            ]
        ];
    }
}