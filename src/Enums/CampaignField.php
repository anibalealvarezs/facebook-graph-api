<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

/**
 * Enum for fields available per page permission for the /me and /me/accounts endpoints.
 */
enum CampaignField: string
{
    case ID = 'id';
    case NAME = 'name';
    case STATUS = 'status';
    case OBJECTIVE = 'objective';
    case BUYING_TYPE = 'buying_type';
    case DAILY_BUDGET = 'daily_budget';
    case LIFETIME_BUDGET = 'lifetime_budget';
    case EFFECTIVE_STATUS = 'effective_status';
    case START_TIME = 'start_time';
    case STOP_TIME = 'stop_time';

    /**
     * Returns a comma-separated list of all enum values.
     *
     * @return string
     */
    public static function toCommaSeparatedList(): string
    {
        $enumValues = array_map(fn (self $field) => $field->value, self::cases());
        return implode(',', $enumValues);
    }
}