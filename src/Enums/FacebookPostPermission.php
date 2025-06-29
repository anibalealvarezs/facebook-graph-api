<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

/**
 * Enum for fields available per page permission for the /me and /me/accounts endpoints.
 */
enum FacebookPostPermission: string
{
    case DEFAULT = 'default';

    public function fields(): string
    {
        return match ($this) {
            self::DEFAULT => FacebookPostField::toCommaSeparatedList(),
        };
    }

    public function insightsFields(): string
    {
        return 'post_impressions,post_impressions_unique';
    }
}