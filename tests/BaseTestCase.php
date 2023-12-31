<?php

declare(strict_types=1);

namespace Samoletik\ReleaseRequirement\Tests;

use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        $this->setUpTraits();
    }

    protected function setUpTraits(): void
    {
        $uses = array_flip(class_uses_recursive(static::class));

        if (isset($uses[WithFaker::class])) {
            $this->setUpFaker();
        }
    }
}
