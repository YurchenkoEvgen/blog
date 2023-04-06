<?php

namespace App\Validator;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class BlogPostTitleValidator extends ConstraintValidator
{
    public function __construct(private readonly Security $security)
    {}

    public function validate($value, Constraint $constraint)
    {
        if (is_null($value)) {
            return;
        }

        if ($this->security->getUser()->getDisplayName() == 'user1'
            && mb_strtolower(mb_substr($value, 0, 1)) == 'a') {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
