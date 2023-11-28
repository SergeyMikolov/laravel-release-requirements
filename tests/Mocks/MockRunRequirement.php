<?php

declare(strict_types=1);

namespace Samoletik\ReleaseRequirement\Tests\Mocks;

use Samoletik\ReleaseRequirement\UseCases\RunRequirement;
use PHPUnit\Framework\MockObject\MockObject;

trait MockRunRequirement
{
    private function getRunRequirementMock(): MockObject|RunRequirement
    {
        return $this->getMockBuilder(RunRequirement::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
