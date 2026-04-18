<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums\Metrics;

use Anibalealvarezs\FacebookGraphApi\Enums\MetricBreakdown;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricPeriod;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricTimeframe;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricType;

/**
 * @see https://developers.facebook.com/docs/marketing-api/insights/
 */
enum MarketingMetric: string
{
    case SPEND = 'spend';
    case IMPRESSIONS = 'impressions';
    case REACH = 'reach';
    case CLICKS = 'clicks';
    case CTR = 'ctr';
    case CPC = 'cpc';
    case CPM = 'cpm';
    case FREQUENCY = 'frequency';
    case ACTIONS = 'actions';
    case ACTION_VALUES = 'action_values';
    case COST_PER_ACTION_TYPE = 'cost_per_action_type';
    case COST_PER_RESULT = 'cost_per_result';
    case RESULTS = 'results';
    case RESULT_RATE = 'result_rate';
    case PURCHASE_ROAS = 'purchase_roas';
    case WEBSITE_PURCHASE_ROAS = 'website_purchase_roas';
    case MOBILE_APP_PURCHASE_ROAS = 'mobile_app_purchase_roas';
    case OBJECTIVE_RESULTS = 'objective_results';
    case COST_PER_OBJECTIVE_RESULT = 'cost_per_objective_result';
    case ESTIMATED_AD_RECALLERS = 'estimated_ad_recallers';
    case ESTIMATED_AD_RECALL_RATE = 'estimated_ad_recall_rate';
    case OPTIMIZATION_GOAL = 'optimization_goal';
    case DATE_START = 'date_start';
    case DATE_STOP = 'date_stop';

    public function allowedMetricTypes(): array
    {
        return [];
    }

    public function allowedPeriods(): array
    {
        return []; // Marketing API uses date ranges
    }

    public function allowedBreakdowns(): array
    {
        return [
            [MetricBreakdown::AGE, MetricBreakdown::GENDER],
            [MetricBreakdown::AGE],
            [MetricBreakdown::GENDER],
            [MetricBreakdown::COUNTRY],
            [MetricBreakdown::CITY]
        ];
    }

    public function allowedTimeframes(): array
    {
        return [];
    }

    public function group(): MetricGroup
    {
        return MetricGroup::OTHERS;
    }
}
