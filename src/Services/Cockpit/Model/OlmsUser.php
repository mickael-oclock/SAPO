<?php


namespace App\Services\Cockpit\Model;

class OlmsUser
{

    protected $id;
    protected $username;
    protected $email;
    protected $dateCreated;
    protected $dateUpdated;
    protected $profile;

    public function __construct()
    {

    }
    private function hydrate($rawData){
        $data = json_encode($rawData);
        foreach ($data as $key => $value) {
            if(strpos("_",$key) !== false){
                $method = 'set';
                foreach (str_split("_",$key) as $chunk){
                    $method.ucfirst($chunk);
                }
            }else{
                $method = "set".ucfirst($key);
            }
            dump($method);
            //if(method_exists($this,$method)){
            //   $this->$method($value);
            //}
        }

        //$this->hydrateProfile($profile);
    }
    private function hydrateProfile(){




    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

}