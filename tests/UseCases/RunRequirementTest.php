<?php

declare(strict_types=1);

namespace Samoletik\ReleaseRequirement\Tests\UseCases;

use Samoletik\ReleaseRequirement\AbstractRequirement;
use Samoletik\ReleaseRequirement\Exceptions\UndefinedRequirementTypeException;
use Samoletik\ReleaseRequirement\Stage;
use Samoletik\ReleaseRequirement\Tests\BaseTestCase;
use Samoletik\ReleaseRequirement\Tests\Mocks\MockApplication;
use Samoletik\ReleaseRequirement\Tests\Mocks\MockConfig;
use Samoletik\ReleaseRequirement\Tests\Mocks\MockConsoleOutput;
use Samoletik\ReleaseRequirement\Tests\Mocks\MockFilesystem;
use Samoletik\ReleaseRequirement\Tests\Mocks\MockRequirementRepository;
use Samoletik\ReleaseRequirement\UseCases\RunRequirement;
use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @coversDefaultClass \Samoletik\ReleaseRequirement\UseCases\RunRequirement
 */
final class RunRequirementTest extends BaseTestCase
{
    use WithFaker;
    use MockApplication;
    use MockConfig;
    use MockConsoleOutput;
    use MockFilesystem;
    use MockRequirementRepository;

    public static function successDataForRun(): array
    {
        return [
            [
                'name' => 'requirement_name',
            ],

            [
                'name' => null,
            ],
        ];
    }

    /**
     * @dataProvider successDataForRun
     * @covers ::run
     * @covers ::__construct
     * @covers ::setStage
     * @covers ::runPending
     */
    public function testRunByName(?string $name): void
    {
        $withName = $name !== null;
        $stage = $this->getRandomStage();
        $name = $name ?? $this->faker->word;

        $output = $this->getOutputMock();

        $output->expects($this->once())
            ->method('info')
            ->with("Running $name requirement:");

        $output->expects($this->once())
            ->method('success')
            ->with("$name - done.");

        $filesystem = $this->getFilesystemMock();

        $filesystem->expects($this->once())
            ->method('exists')
            ->withAnyParameters()
            ->willReturn(true);

        $filesystem->expects($this->once())
            ->method('getRequire')
            ->withAnyParameters()
            ->willReturn($requirementObject = new class ($output) extends AbstractRequirement {
                public function run(): void
                {
                }
            });

        $repository = $this->getRepositoryMock();

        $repository->expects($this->once())
            ->method('getPendingRequirements')
            ->willReturn(["$name.php"]);

        $repository->expects($this->once())
            ->method('addRequirementToRan')
            ->with($stage, $name);

        $app = $this->getApp();

        $app->expects($this->once())
            ->method('call')
            ->with([$requirementObject, 'run']);

        $useCase = new RunRequirement($app, $filesystem, $output, $repository);

        $this->assertSame(Command::SUCCESS, $useCase->run($stage, $withName ? $name : null));
    }

    /**
     * @covers ::run
     * @covers ::__construct
     */
    public function testFileWithNameDoesntExist(): void
    {
        $stage = $this->getRandomStage();
        $name = $this->faker->word;
        $output = $this->getOutputMock();

        $output->expects($this->once())
            ->method('error')
            ->with("No $stage/$name requirement found!");

        $useCase = new RunRequirement($this->getApp(), $this->getFilesystemMock(), $output, $this->getRepositoryMock());

        $this->assertSame(Command::FAILURE, $useCase->run($stage, $name));
    }

    /**
     * @covers ::run
     */
    public function testNoRequirementDirectoryFound(): void
    {
        $stage = $this->getRandomStage();

        $output = $this->getOutputMock();
        $output->expects($this->once())
            ->method('info')
            ->with("No $stage requirements found. Skipped.");

        $filesystem = $this->getFilesystemMock();
        $filesystem->expects($this->once())
            ->method('exists')
            ->with("/$stage")
            ->willReturn(false);

        $useCase = new RunRequirement($this->getApp(), $filesystem, $output, $this->getRepositoryMock());

        $this->assertSame(Command::SUCCESS, $useCase->run($stage));
    }

    /**
     * @covers ::run
     */
    public function testNoRequirementsInDirectory(): void
    {
        $stage = $this->getRandomStage();
        $path = "/$stage";
        $output = $this->getOutputMock();

        $output->expects($this->once())
            ->method('info')
            ->with("No $stage requirements found. Skipped.");

        $filesystem = $this->getFilesystemMock();
        $filesystem->expects($this->once())
            ->method('exists')
            ->with($path)
            ->willReturn(true);
        $filesystem->expects($this->once())
            ->method('isEmptyDirectory')
            ->with($path)
            ->willReturn(true);

        $useCase = new RunRequirement($this->getApp(), $filesystem, $output, $this->getRepositoryMock());

        $this->assertSame(Command::SUCCESS, $useCase->run($stage));
    }

    /**
     * @covers ::run
     */
    public function testAllRequirementsAreUpToDate(): void
    {
        $stage = $this->getRandomStage();
        $path = "/$stage";
        $output = $this->getOutputMock();

        $output->expects($this->once())
            ->method('info')
            ->with("All $stage requirements are up to date. Skipped.");

        $filesystem = $this->getFilesystemMock();
        $filesystem->expects($this->once())
            ->method('exists')
            ->with($path)
            ->willReturn(true);
        $filesystem->expects($this->once())
            ->method('isEmptyDirectory')
            ->with($path)
            ->willReturn(false);

        $repository = $this->getRepositoryMock();
        $repository->expects($this->once())
            ->method('getPendingRequirements')
            ->with($stage, $path)
            ->willReturn([]);

        $useCase = new RunRequirement($this->getApp(), $filesystem, $output, $repository);

        $this->assertSame(Command::SUCCESS, $useCase->run($stage));
    }

    /**
     * @covers ::run
     * @covers ::runPending
     * @covers \Samoletik\ReleaseRequirement\Exceptions\UndefinedRequirementTypeException::__construct
     */
    public function testWrongRequirementFileReturnType(): void
    {
        $name = $this->faker->word;
        $stage = $this->getRandomStage();
        $path = "/$stage";
        $output = $this->getOutputMock();

        $filesystem = $this->getFilesystemMock();
        $filesystem->expects($this->once())
            ->method('exists')
            ->with($path)
            ->willReturn(true);
        $filesystem->expects($this->once())
            ->method('isEmptyDirectory')
            ->with($path)
            ->willReturn(false);

        $repository = $this->getRepositoryMock();
        $repository->expects($this->once())
            ->method('getPendingRequirements')
            ->with($stage, $path)
            ->willReturn([$name]);

        $this->expectException(UndefinedRequirementTypeException::class);

        $useCase = new RunRequirement($this->getApp(), $filesystem, $output, $repository);

        $useCase->run($stage);
    }

    private function getApp(): MockObject|Application
    {
        $app = $this->getApplicationMock();

        $app->expects($this->once())
            ->method('get')
            ->willReturnMap([
                ['config', $this->getConfigMock()],
            ]);

        return $app;
    }

    private function getRandomStage(): string
    {
        return $this->faker->randomElement(Stage::LIST);
    }
}
