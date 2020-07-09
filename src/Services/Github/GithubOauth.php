<?php


namespace App\Services\Github;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


class GithubOauth
{
    const GITHUB_OAUTH_BASE_URL = "https://github.com/login/oauth/";
    private $githubAppId;
    private $githubAppSecret;
    private $githubAppState;
    private $githubAppScope;
    private $githubRedirectUrl;

    public function __construct($githubAppId, $githubAppSecret, $githubAppState, $githubAppScope, $githubRedirectUrl)
    {
        $this->githubAppId = $githubAppId;
        $this->githubAppSecret = $githubAppSecret;
        $this->githubAppState = $githubAppState;
        $this->githubAppScope = $githubAppScope;
        $this->githubRedirectUrl = $githubRedirectUrl;
    }

    public function authorize()
    {

        $httpParams = [
            "client_id" => $this->githubAppId,
            "redirect_uri" => $this->githubRedirectUrl,
            "scope" => $this->githubAppScope,
            "state" => $this->githubAppState,
            "allow_signup" => false
        ];
        $parameters = http_build_query($httpParams);
        return self::GITHUB_OAUTH_BASE_URL . "authorize?" . $parameters;
    }

    public function connectAccount($code)
    {
        $httpClient = HttpClient::create();
        $httpOptions = new HttpOptions();
        $httpOptions->setHeaders([
            "Accept" => "application/json"
        ]);
        $httpOptions->setBody([
            'client_id' => $this->githubAppId,
            "client_secret" => $this->githubAppSecret,
            "code" => $code,
            "redirect_uri" => $this->githubRedirectUrl,
            "state" => $this->githubAppState
        ]);
        try {
            $response = $httpClient->request(Request::METHOD_POST, self::GITHUB_OAUTH_BASE_URL . "access_token", $httpOptions->toArray());
        } catch (TransportExceptionInterface $e) {
            /** TODO Handle exception here **/
        }
        try {
            return json_decode($response->getContent())->access_token;
        } catch (ClientExceptionInterface $e) {
        } catch (RedirectionExceptionInterface $e) {
        } catch (ServerExceptionInterface $e) {
        } catch (TransportExceptionInterface $e) {
            /** TODO Handle exception here **/
        }

    }
}