<?php

declare(strict_types=1);

namespace Samoletik\ReleaseRequirement\Console\Commands;

use Samoletik\ReleaseRequirement\Stage;
use Illuminate\Console\Command;

abstract class AbstractRequirementCommand extends Command
{
    protected function isInvalidStage(string $stage): bool
    {
        return !in_array($stage, Stage::LIST);
    }

    protected function showInvalidStageError(): void
    {
        $this->error("Undefined stage. Available stages: " . implode(', ', Stage::LIST));
    }
}
