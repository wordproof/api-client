<?php

namespace WordProof\ApiClient\Tests;

use Laminas\Diactoros\Request;
use WordProof\ApiClient\WordProofApi;

class ConnectTest extends TestCase
{
    public function testConnectionSuccessful()
    {
        $wordproof = new WordProofApi("test_token_template");
        $request = new Request("http://my.wordproof.com/api", "GET");
        $statusCode = $wordproof->sendRequest($request)->getStatusCode();
        
        $wordproof->timestamp()->get(1);
        
        $this->assertEquals(200, $statusCode);
    }
}
