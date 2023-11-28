<?php

declare(strict_types=1);

namespace Samoletik\ReleaseRequirement\Tests;

use Samoletik\ReleaseRequirement\PathTrait;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

/**
 * @coversDefaultClass \Samoletik\ReleaseRequirement\PathTrait
 */
class PathTraitTest extends BaseTestCase
{
    use WithFaker;

    /**
     * @covers \Samoletik\ReleaseRequirement\PathTrait::getRequirementName
     */
    public function testGetRequirementName(): void
    {
        $object = new class () {
            use PathTrait;

            public function test(string $path): string
            {
                return $this->getRequirementName($path);
            }
        };

        $name = $this->faker->word;

        $this->assertTrue(Str::contains($name, $object->test("$name.php")));
    }
}
