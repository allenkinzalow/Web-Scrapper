<?php
/**
 * Created by PhpStorm.
 * User: allen_000
 * Date: 2/16/2016
 * Time: 8:35 PM
 */

abstract class FacebookResponse {

    private $response;
    private $analyzer;

    /**
     * Construct a response based on an analyzer.
     * @param FacebookAnalyzer $analyzer
     */
    function __construct(FacebookAnalyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    /**
     * Construct a response based on the analyzer given.
     * @return mixed
     */
    abstract protected function construct();

    /**
     * Return the formatted response.
     * @return mixed
     */
    function getResponse() {
        return $this->response;
    }

}