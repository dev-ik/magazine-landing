<?php
/**
 * Created by PhpStorm.
 * User: DevIK
 * Date: 16.03.2018
 * Time: 15:20
 */

namespace classes;
use config\Configuration;

/**
 * Class ResponseClass
 * @package classes
 */
class Response extends Application
{
    /**
     * @var bool|string
     */
    private $type = 'json';

	/**
	 * ResponseClass constructor.
	 */
    public function __construct()
    {
        parent::__construct();
        if (in_array($this->getDataType(),array_keys(Configuration::RESPONSE_TYPE))){
            $this->type = $this->getDataType();
        }
    }

    /**
     * @param $answer
     * @return string
     */
    public function response($answer){
        $this->setHeader();
        echo ($this->type == 'json') ? json_encode(is_array($answer) ? $answer : [$answer]) : $answer;
    }

    public function soapReturn($answer){
        $this->setHeader();
        return $answer;
    }

    /**
     * Set Header
     */
    private function setHeader(){
        header('Content-type: application/'.Configuration::RESPONSE_TYPE[$this->type].'; charset=utf-8');
    }

    /**
     * @param $code
     * @return int
     */
    public function setResponseHttpCode($code){
        return http_response_code($code);
    }



}