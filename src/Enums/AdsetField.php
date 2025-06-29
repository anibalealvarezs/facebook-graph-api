<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

/**
 * Enum for fields available per page permission for the /me and /me/accounts endpoints.
 */
enum AdsetField: string
{
    case ID = 'id';
    case NAME = 'name';
    case CAMPAIGN_ID = 'campaign_id';
    case STATUS = 'status';
    case EFFECTIVE_STATUS = 'effective_status';
    case BID_AMOUNT = 'bid_amount';
    case DAILY_BUDGET = 'daily_budget';
    case LIFETIME_BUDGET = 'lifetime_budget';
    case OPTIMIZATION_GOAL = 'optimization_goal';
    case TARGETING = 'targeting';
    case START_TIME = 'start_time';
    case END_TIME = 'end_time';
    case BILLING_EVENT = 'billing_event';
    case CREATED_TIME = 'created_time';
    case UPDATED_TIME = 'updated_time';

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