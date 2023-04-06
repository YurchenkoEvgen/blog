<?php

declare(strict_types = 1);

namespace App\ApiFilter;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\BlogPost;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

class BlogPostFilters implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private bool $isAdmin;

    public function __construct(private readonly Security $security)
    {
        $user = $this->security->getUser();
        $this->isAdmin = $user && in_array('ROLE_ADMIN', $user->getRoles());
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if (BlogPost::class == $resourceClass && !$this->isAdmin) {
            $queryBuilder->andWhere(sprintf("%s.active = 1", $queryBuilder->getRootAliases()[0]));
        }
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void
    {
        if (BlogPost::class == $resourceClass && !$this->isAdmin) {
            $queryBuilder->andWhere(sprintf("%s.active = 1", $queryBuilder->getRootAliases()[0]));
        }
    }
}