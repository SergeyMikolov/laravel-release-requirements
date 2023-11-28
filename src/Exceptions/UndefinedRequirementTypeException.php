<?php

declare(strict_types=1);

namespace Samoletik\ReleaseRequirement\Exceptions;

use Samoletik\ReleaseRequirement\AbstractRequirement;
use Exception;
use Illuminate\Http\Response;

class UndefinedRequirementTypeException extends Exception
{
    public function __construct(string $file)
    {
        parent::__construct(
            "File($file) must return instance of " . AbstractRequirement::class,
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
