<?php

namespace WordProof\ApiClient;


use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class WordProofClient implements ClientInterface
{
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        // TODO: Implement sendRequest() method.
    }
}