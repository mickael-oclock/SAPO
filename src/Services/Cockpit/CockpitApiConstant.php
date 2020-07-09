<?php


namespace App\Services\Cockpit;


class CockpitApiConstant
{
    public const BASE_URL = "https://cockpit.oclock.io/";
    public const AUTH_URL = "api/check_auth";
    public const CHECK_AUTH_URL = "api/me";
    public const USER_URL = "api/user/{user_id}";
    public const USER_ROLES_URL = "api/user/{user_id}/role";
    public const USER_COHORTS_URL = "/api/user/{user_id}/cohorts";
    public const USER_ACTIVITY_URL = "/api/user/{user_id}/activity/{start_date}/{end_date}";
    public const USER_MESSAGE_URL = "/api/user/{user_id}/messages/{date}";
    public const USER_SURVEY_URL = "/api/user/{user_id}/surveys/{date}";

    public const COHORTS_URL = "/api/cohorts/{type}";

    public const COHORT_TYPE_ALL = "";
    public const COHORT_TYPE_CURRENT = "current";
    public const COHORT_TYPE_UNDETERMINATED = "unterminated";


    public const COHORT_URL = "/api/cohort/{cohort_id}";
    public const COHORT_ACTIVITY_URL = "/api/cohort/{cohort_id}/activity/{start_date}/{end_date}";
    public const COHORT_MESSAGES_URL = "/api/cohort/{cohort_id}/messages/{date}";
    public const COHORT_SURVEY_URL = "/api/cohort/{cohort_id}/surveys/{date}";

    public const SURVEY_URL = "/api/survey/{survey_id}";

    public const CHECK_EMAIL_URL = "/api/check_email";

    public const GET_CURRENT_LESSONS_NUMBER = "/api/today_lessons";

}