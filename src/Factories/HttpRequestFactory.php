<?php


namespace WordProof\ApiClient\Factories;


class HttpRequestFactory
{
    /**
     * Common options
     * @var array
     */
    protected $options;
    
    /**
     * Headers array
     * @var array
     */
    protected $headers = [];
    
    /**
     * Generate an array of common headers
     * @return string[]
     */
    protected function headers(): array
    {
        $this->headers = array_merge($this->headers, [
            'Authorization' => 'Bearer ' . $this->options['token'],
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);
        
        return $this->headers;
    }
    
    public function withHeaders(array $headers): self
    {
        $this->headers = $headers;
        
        return $this;
    }
}