<?php

declare(strict_types = 1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Comment;
use App\Repository\BlogPostRepository;
use Symfony\Bundle\SecurityBundle\Security;

class CommentCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ProcessorInterface $processor,
        private readonly Security $security,
        private readonly BlogPostRepository $blogPostRepository
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        /** @var Comment $data */
        if (is_null($data->getAuthor())) {
            $data->setAuthor($this->security->getUser());
        }
        if (is_null($data->getBlogPost())) {
            $data->setBlogPost($this->blogPostRepository->find($uriVariables['post_id']));
        }

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
