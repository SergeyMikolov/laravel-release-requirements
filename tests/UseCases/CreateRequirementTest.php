<?php

declare(strict_types=1);

namespace Samoletik\ReleaseRequirement\Tests\UseCases;

use Samoletik\ReleaseRequirement\Stage;
use Samoletik\ReleaseRequirement\Tests\BaseTestCase;
use Samoletik\ReleaseRequirement\UseCases\CreateRequirement;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Filesystem\Filesystem;

/**
 * @coversDefaultClass \Samoletik\ReleaseRequirement\UseCases\CreateRequirement
 */
final class CreateRequirementTest extends BaseTestCase
{
    use WithFaker;

    /**
     * @covers ::__construct
     * @covers ::run
     * @covers ::getStub
     * @covers ::stubPath
     * @covers ::getFullFilePath
     */
    public function testRunSuccess(): void
    {
        $fileSystem = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'ensureDirectoryExists',
                'put'
            ])
            ->getMock();

        $fileSystem->expects($this->once())
            ->method('ensureDirectoryExists')
            ->willReturn(true);

        $fileSystem->expects($this->once())
            ->method('put')
            ->willReturn(true);

        $config = $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();

        $config->expects($this->once())
            ->method('get')
            ->willReturn($dir = 'requirements');

        $useCase = new CreateRequirement($fileSystem, $config);

        $path = $useCase->run(
            $stage = $this->faker->randomElement(Stage::LIST),
            $name = $this->faker->word
        );
        $date = today()->format('Y_m_d');

        $this->assertTrue((bool) preg_match("/$dir\/$stage\/$date" . "_\d+_$name\.php/", $path));
    }
}
