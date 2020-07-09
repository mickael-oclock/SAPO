<?php


namespace App\Services\Cockpit;


use DateTime;

class CockpitApiUrlNormalizer
{

    private $arrayParams = [];


    public function __construct()
    {

    }

    public function addUserID($id)
    {
        $this->arrayParams['{user_id}'] = intval($id);
    }

    public function addCohortId($id)
    {
        $this->arrayParams['{cohort_id}'] = intval($id);
    }

    public function addSurveyId($id)
    {
        $this->arrayParams['{survey_id}'] = intval($id);
    }

    public function addDate(DateTime $dateTime)
    {

        $this->arrayParams['{start_date}'] = $this->formatDate($dateTime);
    }

    private function formatDate(DateTime $dateTime)
    {

        return $dateTime->format('Y-m-d');
    }

    public function addStartDate(DateTime $dateTime)
    {
        $this->arrayParams['{end_date}'] = $this->formatDate($dateTime);
    }

    public function addendDate(DateTime $dateTime)
    {
        $this->arrayParams['{date}'] = $this->formatDate($dateTime);
    }

    public function addType($type)
    {
        $this->arrayParams['{type}'] = $type;
    }

    public function normalizeUrl($apiUrl)
    {
        foreach ($this->arrayParams as $key => $value) {
            $apiUrl = str_ireplace($key, $value, $apiUrl);
        }
        return rtrim($apiUrl, '/');
    }
}