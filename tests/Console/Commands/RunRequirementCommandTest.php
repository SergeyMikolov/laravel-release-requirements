<?php

declare(strict_types=1);

namespace Samoletik\ReleaseRequirement\Tests\Console\Commands;

use Samoletik\ReleaseRequirement\Console\Commands\RunRequirementsCommand;
use Samoletik\ReleaseRequirement\Stage;
use Samoletik\ReleaseRequirement\Tests\BaseTestCase;
use Samoletik\ReleaseRequirement\Tests\Mocks\MockRunRequirement;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\Console\Command\Command;

/**
 * @coversDefaultClass \Samoletik\ReleaseRequirement\Console\Commands\RunRequirementsCommand
 */
final class RunRequirementCommandTest extends BaseTestCase
{
    use MockRunRequirement;
    use WithFaker;

    /**
     * @covers ::handle
     * @covers \Samoletik\ReleaseRequirement\Console\Commands\AbstractRequirementCommand::isInvalidStage
     * @covers \Samoletik\ReleaseRequirement\Console\Commands\AbstractRequirementCommand::showInvalidStageError
     */
    public function testWithWrongStages(): void
    {
        $command = $this->getMockBuilder(RunRequirementsCommand::class)
            ->onlyMethods(['argument', 'error'])
            ->getMock();

        $command->expects($this->once())
            ->method('argument')
            ->with('stage')
            ->willReturn($this->faker->word);

        $command->expects($this->once())
            ->method('error')
            ->with("Undefined stage. Available stages: " . implode(', ', Stage::LIST));

        $this->assertSame(
            Command::FAILURE,
            $command->handle()
        );
    }

    /**
     * @covers ::handle
     * @covers \Samoletik\ReleaseRequirement\Console\Commands\AbstractRequirementCommand::isInvalidStage
     */
    public function testSuccess(): void
    {
        $command = $this->getMockBuilder(RunRequirementsCommand::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['argument', 'option', 'getRequirementRunner'])
            ->getMock();

        $command->expects($this->once())
            ->method('argument')
            ->with('stage')
            ->willReturn($stage = $this->faker->randomElement(Stage::LIST));

        $command->expects($this->once())
            ->method('option')
            ->with('name')
            ->willReturn($name = $this->faker->word);

        $useCase = $this->getRunRequirementMock();
        $useCase->expects($this->once())
            ->method('run')
            ->with($stage, $name)
            ->willReturn(Command::SUCCESS);

        $command->expects($this->once())
            ->method('getRequirementRunner')
            ->willReturn($useCase);

        $this->assertSame(Command::SUCCESS, $command->handle());
    }
}
