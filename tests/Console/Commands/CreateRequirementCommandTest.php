<?php

declare(strict_types=1);

namespace Samoletik\ReleaseRequirement\Tests\Console\Commands;

use Samoletik\ReleaseRequirement\Console\Commands\CreateRequirementCommand;
use Samoletik\ReleaseRequirement\Stage;
use Samoletik\ReleaseRequirement\Tests\BaseTestCase;
use Samoletik\ReleaseRequirement\Tests\Mocks\MockCreateRequirement;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\Console\Command\Command;

/**
 * @coversDefaultClass \Samoletik\ReleaseRequirement\Console\Commands\CreateRequirementCommand
 */
final class CreateRequirementCommandTest extends BaseTestCase
{
    use MockCreateRequirement;
    use WithFaker;

    /**
     * @covers ::handle
     * @covers \Samoletik\ReleaseRequirement\Console\Commands\AbstractRequirementCommand::isInvalidStage
     * @covers \Samoletik\ReleaseRequirement\Console\Commands\AbstractRequirementCommand::showInvalidStageError
     */
    public function testWithWrongStages(): void
    {
        $command = $this->getMockBuilder(CreateRequirementCommand::class)
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
            $command->handle($this->getCreateRequirementMock())
        );
    }

    /**
     * @covers ::handle
     * @covers \Samoletik\ReleaseRequirement\Console\Commands\AbstractRequirementCommand::isInvalidStage
     */
    public function testSuccess(): void
    {
        $command = $this->getMockBuilder(CreateRequirementCommand::class)
            ->onlyMethods(['argument', 'info'])
            ->getMock();

        $command->expects($this->exactly(2))
            ->method('argument')
            ->willReturnMap([
                ['stage', $stage = $this->faker->randomElement(Stage::LIST)],
                ['name', $name = $this->faker->word],
            ]);

        $filePath = "requirements/$stage/$name";

        $command->expects($this->once())
            ->method('info')
            ->with("Requirement($stage) $filePath is created successfully.");

        $useCase = $this->getCreateRequirementMock();
        $useCase->expects($this->once())
            ->method('run')
            ->with($stage, $name)
            ->willReturn($filePath);

        $this->assertSame(Command::SUCCESS, $command->handle($useCase));
    }
}
