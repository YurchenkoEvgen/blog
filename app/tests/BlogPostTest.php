<?php

declare(strict_types = 1);

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Request;

class BlogPostTest extends AbstractT
{
    #[Test]
    public function test1(): void
    {
        self::createClient()->request(Request::METHOD_GET, '/blog_posts');
        $this->assertResponseStatusCodeSame(200);
    }

    #[Test]
    public function test2(): void
    {
        self::createClient()->request(Request::METHOD_GET, '/blog_posts');
        $this->assertResponseStatusCodeSame(200);
    }
}