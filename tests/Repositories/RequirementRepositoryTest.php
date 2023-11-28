<?php

declare(strict_types=1);

namespace Samoletik\ReleaseRequirement\Tests\Repositories;

use Samoletik\ReleaseRequirement\Repositories\RequirementRepository;
use Samoletik\ReleaseRequirement\Stage;
use Samoletik\ReleaseRequirement\Tests\BaseTestCase;
use Samoletik\ReleaseRequirement\Tests\Mocks\MockFilesystem;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * @coversDefaultClass \Samoletik\ReleaseRequirement\Repositories\RequirementRepository
 */
final class RequirementRepositoryTest extends BaseTestCase
{
    use WithFaker;
    use MockFilesystem;

    /**
     * @covers ::__construct
     * @covers ::getPendingRequirements
     * @covers ::getRanRequirements
     * @covers ::getRequirementFiles
     */
    public function testGetPendingRequirementsForFile(): void
    {
        $name = $this->faker->word;
        $stage = $this->faker->randomElement(Stage::LIST);
        $path = "$stage/$name.php";

        $repository = $this->getMockBuilder(RequirementRepository::class)
            ->setConstructorArgs(['filesystem' => $this->getFilesystemMock()])
            ->onlyMethods(['getRanRequirements'])
            ->getMock();

        $repository->expects($this->once())
            ->method('getRanRequirements')
            ->willReturn([$this->faker->word]);

        $requirements = $repository->getPendingRequirements($stage, $path);

        $this->assertContains($path, $requirements);
    }

    /**
     * @covers ::__construct
     * @covers ::getPendingRequirements
     * @covers ::getRanRequirements
     * @covers ::getRequirementFiles
     */
    public function testGetPendingRequirementsStage(): void
    {
        $stage = $this->faker->randomElement(Stage::LIST);

        $filesystem = $this->getFilesystemMock();
        $filesystem->expects($this->once())
            ->method('glob')
            ->willReturn([$path = $this->faker->word . '.php']);

        $repository = $this->getMockBuilder(RequirementRepository::class)
            ->setConstructorArgs(['filesystem' => $filesystem])
            ->onlyMethods(['getRanRequirements'])
            ->getMock();

        $repository->expects($this->once())
            ->method('getRanRequirements')
            ->willReturn([]);

        $requirements = $repository->getPendingRequirements($stage, $stage);
        $this->assertContains($path, $requirements);
    }
}
