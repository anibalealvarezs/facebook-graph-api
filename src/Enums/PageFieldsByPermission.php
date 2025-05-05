<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

/**
 * Enum for fields available per page permission for the /me and /me/accounts endpoints.
 */
enum PageFieldsByPermission: string
{
    case PAGES_SHOW_LIST = 'pages_show_list';
    case PAGES_READ_ENGAGEMENT = 'pages_read_engagement';
    case PAGES_READ_USER_CONTENT = 'pages_read_user_content';
    case PAGES_MANAGE_METADATA = 'pages_manage_metadata';
    case BUSINESS_MANAGEMENT = 'business_management';

    public function fields(): string
    {
        return match ($this) {
            self::PAGES_SHOW_LIST => 'id,name,access_token,category,tasks,is_published,username,is_verified',
            self::PAGES_READ_ENGAGEMENT => 'about,description,fan_count,cover,location,phone,website,email,hours,is_permanently_closed,verification_status,business,engagement,followers_count,new_like_count,rating_count,overall_star_rating,affiliation,company_overview,contact_address,founded,general_info,mission,products',
            self::PAGES_READ_USER_CONTENT => '',
            self::PAGES_MANAGE_METADATA => 'access_token',
            self::BUSINESS_MANAGEMENT => 'business,merchant_settings,attribution_details,created_by,created_time,updated_by,updated_time,instagram_business_account',
        };
    }
}