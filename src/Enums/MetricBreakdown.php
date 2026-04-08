<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

enum MetricBreakdown: string
{
    case AGE = 'age';
    case GENDER = 'gender';
    case COUNTRY = 'country';
    case CITY = 'city';
    case REGION = 'region';
    case DMA = 'dma';
    case FOLLOW_TYPE = 'follow_type';
    case MEDIA_PRODUCT_TYPE = 'media_product_type';
    case CONTACT_BUTTON_TYPE = 'contact_button_type';
    case DEVICE_PLATFORM = 'device_platform';
    case PUBLISHER_PLATFORM = 'publisher_platform';
    case PLATFORM_CUSTOM_BREAKDOWN = 'platform_custom_breakdown';
    case IMPRESSION_DEVICE = 'impression_device';
    case PLACE_PAGE_ID = 'place_page_id';
    case PRODUCT_ID = 'product_id';
    case ACTION_TYPE = 'action_type';
    case ACTION_TARGET_ID = 'action_target_id';
    case ACTION_DESTINATION = 'action_destination';
}
