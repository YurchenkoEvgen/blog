<?php

declare(strict_types = 1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\BlogPost;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class BlogPostCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ProcessorInterface $processor,
        private readonly Security $security
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        /** @var BlogPost $data */
        if (is_null($data->getAuthor())) {
            $data->setAuthor($this->security->getUser());
            $data->setCreatedAt(new \DateTimeImmutable());
        } else {
            throw new AuthenticationException('Not auth user');
        }

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
