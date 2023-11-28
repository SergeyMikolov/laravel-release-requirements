<?php

declare(strict_types=1);

namespace Samoletik\ReleaseRequirement\Tests\Mocks;

use Samoletik\ReleaseRequirement\Repositories\RequirementRepository;
use PHPUnit\Framework\MockObject\MockObject;

trait MockRequirementRepository
{
    private function getRepositoryMock(): MockObject|RequirementRepository
    {
        return $this->getMockBuilder(RequirementRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
