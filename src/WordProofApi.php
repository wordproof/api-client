<?php

namespace WordProof\ApiClient;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use WordProof\ApiClient\Factories\TimestampFactory;

class WordProofApi implements ClientInterface
{
    private $options = [
        
        'endpoint' => "http://my.wordproof.com/api",
        
        'token'    => null
        
    ];
    
    private $client;
    
    public function __construct(string $token = null, ClientInterface $client = null)
    {
        $this->client = $client ?? new Client();
        
        $this->options['token'] = $token;
    }
    
    public function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
    }
    
    public function getOptions(string $key = null)
    {
        if (!$key) return $this->options;
        
        return isset($this->options[$key]) ? $this->options[$key] : null;
    }
    
    public function withOptions(array $options)
    {
        $this->setOptions($options);
        return $this;
    }
    
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->client->sendRequest($request);
    }
    
    public function timestamp()
    {
        return new TimestampFactory($this->client, $this->options);
    }
    
}