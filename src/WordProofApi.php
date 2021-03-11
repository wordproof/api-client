<?php

namespace WordProof\ApiClient;

use Http\Client\Curl\Client;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use WordProof\ApiClient\Factories\TimestampFactory;

class WordProofApi implements ClientInterface
{
    private array $options = [
        
        'endpoint' => "http://my.wordproof.com/api",
        
        'token'    => null
        
    ];
    
    private ClientInterface $client;
    
    public function __construct(string $token, ClientInterface $client = null)
    {
        $this->client = $client ?? new Client(null, null, [CURLOPT_FOLLOWLOCATION => true]);
        
        $this->options['token'] = $token;
    }
    
    public function setOptions(array $options)
    {
        $this->options = array_merge($options, $this->options);
    }
    
    public function getOptions(string $key = null)
    {
        if (!$key) return $this->options;
        
        return isset($this->options[$key]) ? $this->options[$key] : null;
    }
    
    public function timestamp()
    {
        return new TimestampFactory($this->client, $this->options);
    }
    
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->client->sendRequest($request);
    }
    
}