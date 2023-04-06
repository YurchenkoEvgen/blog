<?php

namespace App\Validator;

use App\Entity\Category;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class CategoryPostCountMaxValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var CategoryPostCountMax $constraint */
        if (!($value instanceof Category) || is_null($constraint->max)) {
            return;
        }
        if ($value->getBlogPosts()->count() > $constraint->max) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $constraint->max)
                ->addViolation();
        }
    }
}
