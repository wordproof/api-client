<?php


namespace WordProof\ApiClient;


use Exception;
use Http\Client\Exception\HttpException;
use Http\Client\Exception\RequestException;
use Nyholm\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A minimalistic HTTP client.
 *
 * Modified by Yurii Markovych <chivokram@gmail.com>
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @link https://github.com/Nyholm/http-client/blob/master/src/Client.php       Original class
 */
class Client implements ClientInterface
{
    const CURL_DEFAULT_OPTIONS = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 2,
        CURLOPT_FAILONERROR => 0,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        // We fix the timeout to 60 seconds
        CURLOPT_TIMEOUT => 60,
    ];
    
    /**
     * @var resource
     */
    private $curl;
    
    public function __construct()
    {
        if (false === $this->curl = curl_init()) {
            throw new Exception('Unable to create a new cURL handle');
        }
        
        curl_setopt_array($this->curl, self::CURL_DEFAULT_OPTIONS);
    }
    
    /**
     * {@inheritdoc}
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        if (false === $this->setOptionsFromRequest($request)) {
            throw new RequestException('Not a valid request.', $request);
        }
        
        if (false === $data = curl_exec($this->curl)) {
            throw new RequestException(
                sprintf('Error (%d): %s', curl_errno($this->curl), curl_error($this->curl)),
                $request
            );
        }
        
        return $this->createResponse($data, $request);
    }
    
    /**
     * Create a response object.
     *
     * @param string $raw The raw response string
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    private function createResponse(string $raw, RequestInterface $request)
    {
        // fixes bug https://sourceforge.net/p/curl/bugs/1204/
        if (version_compare(curl_version()['version'], '7.30.0', '<')) {
            $pos = strlen($raw) - curl_getinfo($this->curl, CURLINFO_SIZE_DOWNLOAD);
        } else {
            $pos = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
        }
        
        $lastHttpHeadersStartIndex = strrpos($raw, 'HTTP');
        
        list($statusLine, $headers) = $this->parseHeaders(rtrim(substr($raw, $lastHttpHeadersStartIndex, $pos)));
        $body = strlen($raw) > $pos ? substr($raw, $pos) : '';

        if (!preg_match('|^HTTP/([12].[01]) ([1-9][0-9][0-9]) (.*?)$|', $statusLine, $matches)) {
            throw new HttpException('Not a HTTP response', $request, new Response(0, []));
        }
        
        return new Response((int) $matches[2], $headers, $body, $matches[1], $matches[3]);
    }
    
    /**
     * Parse raw data for headers.
     *
     * @param string $raw Raw response byt no body
     *
     * @return array with status line and the headers.
     */
    private function parseHeaders(string $raw): array
    {
        $rawHeaders = preg_split('|(\\r?\\n)|', $raw);
        $statusLine = array_shift($rawHeaders);
        $startBodyIndex = 0;
        foreach ($rawHeaders as $key => $value) {
            if ($value === "") {
                $startBodyIndex = $key;
                break;
            }
        }
        $rawHeaders = array_slice($rawHeaders, 0, $startBodyIndex);
        return array_reduce($rawHeaders, function ($parsedHeaders, $header) {
            list($name, $value) = preg_split('|: |', $header);
            $parsedHeaders[1][$name][] = $value;
            
            return $parsedHeaders;
        }, [$statusLine, []]);
    }
    
    /**
     * Set CURL options from the Request.
     *
     * @param RequestInterface $request
     *
     * @return bool
     */
    private function setOptionsFromRequest(RequestInterface $request): bool
    {
        return curl_setopt_array(
            $this->curl,
            [
                CURLOPT_HTTP_VERSION => $request->getProtocolVersion() === '1.0' ? CURL_HTTP_VERSION_1_0 : CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $request->getMethod(),
                CURLOPT_URL => (string) $request->getUri(),
                CURLOPT_HTTPHEADER => $this->getHeaders($request),
                CURLOPT_POSTFIELDS => (string) $request->getBody(),
            ]
        );
    }
    
    /**
     * Get headers from a PSR-7 Request to Curl format.
     *
     * @param RequestInterface $request
     *
     * @return array
     */
    private function getHeaders(RequestInterface $request): array
    {
        $headers = [];
        foreach ($request->getHeaders() as $name => $array) {
            foreach ($array as $header) {
                $headers[] = sprintf('%s: %s', $name, $header);
            }
        }
        
        return $headers;
    }
    
    /**
     * Make sure to destroy $curl.
     */
    public function __destruct()
    {
        curl_close($this->curl);
    }
}