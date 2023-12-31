<?php

declare(strict_types=1);

namespace Samoletik\ReleaseRequirement\Console\Commands;

use Samoletik\ReleaseRequirement\Stage;
use Illuminate\Database\Console\Seeds\SeedCommand;

/**
 * @see \CompleteSolar\ReleaseRequirement\Tests\Console\Commands\SeedWithRequirementCommandTest
 */
class SeedWithRequirementCommand extends SeedCommand
{
    use RunnerTrait;

    public function handle(): int
    {
        $runner = $this->getRequirementRunner();

        if ($runner->run(Stage::BEFORE_SEED) === self::FAILURE) {
            return self::FAILURE;
        }

        if ($this->runSeeds() === self::FAILURE) {
            return self::FAILURE;
        }

        return $runner->run(Stage::AFTER_SEED);
    }

    /** @codeCoverageIgnore */
    protected function runSeeds(): int
    {
        return parent::handle();
    }
}
