<?php

namespace Anibalealvarezs\FacebookGraphApi\Exceptions;

use Anibalealvarezs\ApiSkeleton\Classes\Exceptions\ApiRequestException;
use Throwable;

class FacebookRateLimitException extends ApiRequestException
{
    protected ?array $usageHeader;

    public function __construct(
        string $message = "",
        int $code = 4,
        ?Throwable $previous = null,
        ?array $usageHeader = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->usageHeader = $usageHeader;
    }

    public function getUsageHeader(): ?array
    {
        return $this->usageHeader;
    }
}
