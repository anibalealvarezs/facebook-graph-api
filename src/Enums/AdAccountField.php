<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

/**
 * Enum for fields available per page permission for the /me and /me/accounts endpoints.
 */
enum AdAccountField: string
{
    case ID = 'id';
    case NAME = 'name';
    case ACCOUNT_ID = 'account_id';
    case ACCOUNT_STATUS = 'account_status';
    case CURRENCY = 'currency';
    case TIMEZONE_NAME = 'timezone_name';
    case BUSINESS = 'business';
    case AGE = 'age';
    case AMOUNT_SPENT = 'amount_spent';
    case BALANCE = 'balance';

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