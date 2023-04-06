<?php

declare(strict_types = 1);

namespace App\Validator;

use App\Entity\BlogPost;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class BlogPostCustomValidator
{
    public static function validate(mixed $object, ExecutionContextInterface $context, $payload)
    {
        if ($object instanceof BlogPost &&
            $object->getCategory()->getId() == 1 &&
            strlen($object->getTitle()) > 10
        ) {
            $context->buildViolation("Title can't be longer then 10 symbols")->atPath('title')->addViolation();
        }
    }
}