<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

/**
 * Enum for fields available per page permission for the /me and /me/accounts endpoints.
 */
enum AdField: string
{
    case ID = 'id';
    case NAME = 'name';
    case STATUS = 'status';
    case CAMPAIGN_ID = 'campaign_id';
    case ADSET_ID = 'adset_id';
    case EFFECTIVE_STATUS = 'effective_status';
    case CREATIVE = 'creative';
    case BID_AMOUNT = 'bid_amount';
    case DAILY_BUDGET = 'daily_budget';
    case LIFETIME_BUDGET = 'lifetime_budget';
    case CONFIGURED_STATUS = 'configured_status';
    case CREATED_TIME = 'created_time';
    case CONVERSION_SPEC = 'conversion_spec';
    case TRACKING_SPECS = 'tracking_specs';

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