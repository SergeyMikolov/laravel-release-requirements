<?php

declare(strict_types=1);

namespace Samoletik\ReleaseRequirement\Tests\Console\Commands;

use Samoletik\ReleaseRequirement\Console\Commands\RunnerTrait;
use Samoletik\ReleaseRequirement\Tests\BaseTestCase;
use Samoletik\ReleaseRequirement\Tests\Mocks\MockApplication;
use Samoletik\ReleaseRequirement\Tests\Mocks\MockConfig;
use Samoletik\ReleaseRequirement\Tests\Mocks\MockConsoleOutput;
use Samoletik\ReleaseRequirement\Tests\Mocks\MockRunRequirement;
use Samoletik\ReleaseRequirement\UseCases\RunRequirement;
use Illuminate\Console\OutputStyle;
use Illuminate\Foundation\Application;

/**
 * @coversDefaultClass \Samoletik\ReleaseRequirement\Console\Commands\RunnerTrait
 */
class RunnerTraitTest extends BaseTestCase
{
    use MockConsoleOutput;
    use MockApplication;
    use MockRunRequirement;
    use MockConfig;

    /**
     * @covers \Samoletik\ReleaseRequirement\Console\Commands\RunnerTrait::getRequirementRunner
     */
    public function testGetRequirementRunner(): void
    {
        $output = $this->getOutputMock();

        $app = $this->getApplicationMock();
        $app->expects($this->once())
            ->method('make')
            ->with(RunRequirement::class, ['output' => $output])
            ->willReturn($this->getRunRequirementMock());

        $command = new class ($app, $output) {
            use RunnerTrait;

            public function __construct(
                private readonly Application $laravel,
                private readonly OutputStyle $output,
            ) {
            }

            public function getRunner(): RunRequirement
            {
                return $this->getRequirementRunner();
            }
        };

        $this->assertInstanceOf(RunRequirement::class, $command->getRunner());
    }
}
