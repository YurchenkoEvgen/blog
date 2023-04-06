<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class CategoryPostCountMax extends Constraint
{

    public function __construct(
        public ?int $max = 5,
        mixed       $options = null,
        array       $groups = null,
        mixed       $payload = null
    )
    {
        parent::__construct($options, $groups, $payload);
    }

    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'Max posts in category is {{ value }}';
}
