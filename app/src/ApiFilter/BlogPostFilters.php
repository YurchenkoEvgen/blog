<?php

declare(strict_types = 1);

namespace App\ApiFilter;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\BlogPost;
use Doctrine\ORM\QueryBuilder;

class BlogPostFilters implements QueryCollectionExtensionInterface
{
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if (BlogPost::class == $resourceClass ) {
            $queryBuilder->andWhere(sprintf("%s.active = 1", $queryBuilder->getRootAliases()[0]));
        }
    }
}