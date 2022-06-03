<?php


namespace App\ApiPlatform;


use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\CartItems;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

class CartIsPaidExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $this->addWhere($resourceClass, $queryBuilder);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = [])
    {
        $this->addWhere($resourceClass, $queryBuilder);
    }

    public function addWhere(string $resourceClass, QueryBuilder $queryBuilder): void
    {
        if ($resourceClass !== CartItems::class) {
            return;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.paid = :paid', $rootAlias))
            ->setParameter('paid', true);
    }
    //Tip
    // CheeseListingIsPublishedExtension::addWhere()
    //if (!$this->security->getUser()) {
    //    // existing code to check for isPublished=true
    //} else {
    //    $queryBuilder->andWhere(sprintf('
    //            %s.isPublished = :isPublished
    //            OR %s.owner = :owner',
    //        $rootAlias, $rootAlias
    //    ))
    //        ->setParameter('isPublished', true)
    //        ->setParameter('owner', $this->security->getUser());
    //}
}