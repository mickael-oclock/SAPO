<?php


namespace App\Services\Cockpit\Model;


class OlmsCohort
{

    private $id;
    private $nickName;
    private $linkSlack;
    private $linkReplay;
    private $linkGithub;
    private $childCohort;
    private $speCohort;
    private $endDate;
    private $startDate;
    private $dateCreated;
    private $dateModified;

    public function __construct($data = null)
    {

        if ($data !== null) $this->hydrate($data);
    }

    private function hydrate($data)
    {
        foreach ($data as $key => $value) {
            if (strpos("_", $key) !== false) {
                $method = 'set';
                foreach (str_split("_", $key) as $chunk) {
                    $method . ucfirst($chunk);
                }
            } else {
                $method = "set" . ucfirst($key);
            }
            dump($method);
            //if(method_exists($this,$method)){
            //   $this->$method($value);
            //}
        }

        //$this->hydrateProfile($profile);
    }
}