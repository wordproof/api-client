<?php

namespace WordProof\ApiClient\Tests;

use Nyholm\Psr7\Request;
use WordProof\ApiClient\WordProofApi;

class ConnectTest extends TestCase
{
    public function testConnectionSuccessful()
    {
        $wordproof = new WordProofApi("test_token_template");
        $request = new Request("GET", "http://my.wordproof.com/api");
        $statusCode = $wordproof->sendRequest($request)->getStatusCode();
        
        $this->assertEquals(200, $statusCode);
    }
}
