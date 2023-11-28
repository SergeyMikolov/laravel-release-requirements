<?php

declare(strict_types=1);

namespace Samoletik\ReleaseRequirement\Tests;

use Samoletik\ReleaseRequirement\AbstractRequirement;
use Samoletik\ReleaseRequirement\Exceptions\NoRequirementRunMethodException;
use Samoletik\ReleaseRequirement\Tests\Mocks\MockConsoleOutput;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \Samoletik\ReleaseRequirement\AbstractRequirement
 */
class AbstractRequirementTest extends BaseTestCase
{
    use MockConsoleOutput;

    /**
     * @covers ::__construct
     * @covers \Samoletik\ReleaseRequirement\Exceptions\NoRequirementRunMethodException::__construct
     */
    public function testRunMethodDoesNotExists(): void
    {
        $exception = null;

        try {
            new class ($this->getOutputMock()) extends AbstractRequirement {
            };
        } catch (NoRequirementRunMethodException $e) {
            $exception = $e;
        }

        $this->assertInstanceOf(NoRequirementRunMethodException::class, $exception);
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getCode());
    }

    /**
     * @covers ::__construct
     */
    public function testRunMethodExists(): void
    {
        $requirement = new class ($this->getOutputMock()) extends AbstractRequirement {
            public function run(): void
            {
            }
        };

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $this->assertNull($requirement->run());
    }
}
