<?php


namespace App\Services\Cockpit;

use App\Services\Cockpit\Model\OlmsAuthUser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class SDKClient
 * @package App\CockpitApi
 */
class CockpitApiClient
{

    private $statutCode;

    private $urlNormalizer;

    private $httpOptions;

    private $isClientSecured;

    private $useRawResult;


    private $cockpitApiKey;


    public function __construct($rawResult = false, $maxClientRedirect = 0)
    {
        $this->httpOptions = new HttpOptions();
        $this->httpOptions->setMaxRedirects($maxClientRedirect);
        $this->urlNormalizer = new CockpitApiUrlNormalizer();
        $this->useRawResult = $rawResult;


    }

    /**
     * @return mixed
     */
    public function getStatutCode()
    {
        return $this->statutCode;
    }

    /**
     * @param mixed $statutCode
     */
    public function setStatutCode($statutCode): void
    {
        $this->statutCode = $statutCode;
    }

    /**
     * @return CockpitApiUrlNormalizer
     */
    public function getUrlNormalizer(): CockpitApiUrlNormalizer
    {
        return $this->urlNormalizer;
    }

    /**
     * @param CockpitApiUrlNormalizer $urlNormalizer
     */
    public function setUrlNormalizer(CockpitApiUrlNormalizer $urlNormalizer): void
    {
        $this->urlNormalizer = $urlNormalizer;
    }

    /**
     * @return HttpOptions
     */
    public function getHttpOptions(): HttpOptions
    {
        return $this->httpOptions;
    }

    /**
     * @param HttpOptions $httpOptions
     */
    public function setHttpOptions(HttpOptions $httpOptions): void
    {
        $this->httpOptions = $httpOptions;
    }

    /**
     * @return mixed
     */
    public function getIsClientSecured()
    {
        return $this->isClientSecured;
    }


    /**
     * @return bool
     */
    public function isUseRawResult(): bool
    {
        return $this->useRawResult;
    }

    /**
     * @param bool $useRawResult
     */
    public function setUseRawResult(bool $useRawResult): void
    {
        $this->useRawResult = $useRawResult;
    }

    /**
     *
     * Use cockpitApi  for OLMS user authentification return OlmsAuthUser or raw response from api (json version ) if error return false;
     *
     * @param $login
     * @param $password
     * @return OlmsAuthUser|bool|mixed
     *
     * @see OlmsAuthUser
     */
    public function auth($login, $password)
    {


        $this->httpOptions->setBody([
            "email" => $login,
            "passwd" => $password
        ]);
        $rawResponse = $this->handleRequest(Request::METHOD_POST, CockpitApiConstant::AUTH_URL);
        if ($this->useRawResult) return $rawResponse;
        $response = json_decode($rawResponse);
        return $response == false ? false : new OlmsAuthUser($response);


    }

    /**
     * @return mixed
     */
    private function handleRequest($method, $url, $secured = false)
    {
        try {


            $client = !$secured ? $this->getClient() : $this->getSecuredClient();
            $response = $client->request($method, $this->urlNormalizer->normalizeUrl($url), $this->httpOptions->toArray());
            $this->statutCode = $response->getStatusCode();
            return $response->getContent();

        } catch (TransportExceptionInterface $e) {
        } catch (ClientExceptionInterface $e) {
        } catch (RedirectionExceptionInterface $e) {
        } catch (ServerExceptionInterface $e) {
        }

    }

    private function getClient()
    {
        $this->isClientSecured = false;
        return HttpClient::createForBaseUri(CockpitApiConstant::BASE_URL);
    }

    private function getSecuredClient()
    {
        if ($this->cockpitApiKey === null) {

        }
        $this->isClientSecured = true;
        return HttpClient::createForBaseUri(CockpitApiConstant::BASE_URL, [
            "headers" => [
                "X-AUTH-TOKEN" => $this->cockpitApiKey
            ]
        ]);
    }

    public function checkAuth()
    {
        $rawResponse = $this->handleRequest(Request::METHOD_GET, CockpitApiConstant::CHECK_AUTH_URL, true);
        if ($this->useRawResult) return $rawResponse;
        $olmsUser = new OlmsUser($rawResponse);

    }

    public function getUser($userId)
    {
        $this->urlNormalizer->addUserID($userId);
        $rawResponse = $this->handleRequest(Request::METHOD_GET, CockpitApiConstant::USER_URL, true);

        //$url = $this->urlNormalizer->normalizeUrl(SdkConstant::USER_URL);

    }

    public function getUserRoles($userId)
    {
        $this->urlNormalizer->addUserID($userId);
        $url = $this->urlNormalizer->normalizeUrl(CockpitApiConstant::USER_ROLES_URL);
    }

    public function getUserCohort($userId)
    {
        $this->urlNormalizer->addUserID($userId);
        $url = $this->urlNormalizer->normalizeUrl(CockpitApiConstant::USER_COHORTS_URL);
    }

    public function getUserActivity($userId, $startDate, $endDate)
    {
        $this->urlNormalizer->addUserID($userId);
        $this->urlNormalizer->addStartDate($startDate);
        $this->urlNormalizer->addStartDate($endDate);
        $url = $this->urlNormalizer->normalizeUrl(CockpitApiConstant::USER_ACTIVITY_URL);
    }

    public function getUserMessages($userId, $date)
    {
        $this->urlNormalizer->addUserID($userId);
        $this->urlNormalizer->addStartDate($date);
        $url = $this->urlNormalizer->normalizeUrl(CockpitApiConstant::USER_MESSAGE_URL);
    }

    public function getUserSurvey($userId, $date)
    {
        $this->urlNormalizer->addUserID($userId);
        $this->urlNormalizer->addDate($date);
        $url = $this->urlNormalizer->normalizeUrl(CockpitApiConstant::USER_SURVEY_URL);
    }

    public function getCohorts($type = null)
    {
        $this->urlNormalizer->addType($type);
        $url = $this->urlNormalizer->normalizeUrl(CockpitApiConstant::COHORTS_URL);
        $rawResponse = $this->handleRequest(Request::METHOD_GET, $url, true);
        if ($this->useRawResult) return $rawResponse;
        return json_decode($rawResponse)->data;
    }

    public function getCohort($id)
    {
        $this->urlNormalizer->addCohortId($id);
        $url = $this->urlNormalizer->normalizeUrl(CockpitApiConstant::COHORT_URL);
        $rawResponse = $this->handleRequest(Request::METHOD_GET, $url, true);
        if ($this->useRawResult) return $rawResponse;
        return json_decode($rawResponse)->data;

    }

    public function getCohortActivity($cohortId, $startDate, $endDate)
    {
        $this->urlNormalizer->addCohortId($cohortId);
        $this->urlNormalizer->addStartDate($startDate);
        $this->urlNormalizer->addendDate($endDate);
        $url = $this->urlNormalizer->normalizeUrl(CockpitApiConstant::COHORT_ACTIVITY_URL);

    }

    public function getCohortMessages($cohortId, $date)
    {
        $this->urlNormalizer->addCohortId($cohortId);
        $this->urlNormalizer->addDate($date);
        $url = $this->urlNormalizer->normalizeUrl(CockpitApiConstant::COHORT_MESSAGES_URL);
    }

    public function getCohortSurvey($cohortId, $date)
    {
        $this->urlNormalizer->addCohortId($cohortId);
        $this->urlNormalizer->addDate($date);
        $url = $this->urlNormalizer->normalizeUrl(CockpitApiConstant::COHORT_SURVEY_URL);

    }

    public function getSurvey($surveyId)
    {
        $this->urlNormalizer->addSurveyId($surveyId);
        $url = $this->urlNormalizer->normalizeUrl(CockpitApiConstant::SURVEY_URL);
    }

    public function CheckEmail($email)
    {
        $url = $this->urlNormalizer->normalizeUrl(CockpitApiConstant::CHECK_EMAIL_URL);
    }

    public function setMaxRedirect($numberOfTry)
    {
        $this->httpOptions->setMaxRedirects($numberOfTry);
    }

    public function setCockpitApiKey($key = null)
    {
        // $key = "3528c238dfb05bad3e82925dde9d62333121b0d0196370ffc50bc9e10ccbffc6";

        $this->cockpitApiKey = $key;
    }


}