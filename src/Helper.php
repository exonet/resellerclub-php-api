<?php

namespace Exonet\ResellerClub;

use Exception;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use SimpleXMLElement;

/**
 * Trait Helper
 * @package Exonet\ResellerClub
 */
trait Helper
{
    /**
     * @var Guzzle
     */
    protected $guzzle;

    /**
     * Authentication info needed for every request
     *
     * @var array
     */
    private $authentication = [];

    /**
     * Helper constructor.
     *
     * @param Guzzle $guzzle
     * @param array  $authentication
     */
    public function __construct(Guzzle $guzzle, array $authentication)
    {
        $this->authentication = $authentication;
        $this->guzzle = $guzzle;
    }

    /**
     * @param string $method
     * @param array  $args
     * @param string $prefix
     *
     * @return Exception|array
     * @throws Exception
     */
    protected function get($method, $args = [], $prefix = '')
    {
        try {
            return $this->parse(
                $this->guzzle->get(
                    $this->api.'/'.$prefix.$method.'.json?'.preg_replace(
                        '/%5B[0-9]+%5D/simU',
                        '',
                        http_build_query(
                            array_merge($args, $this->authentication)
                        )
                    )
                )
            );
        } catch (BadResponseException $e) {
            return $this->parseErrorResponse($e);
        } catch (Exception $error) {
            return $error;
        }
    }

    /**
     * @param string $method
     * @param array  $args
     * @param string $prefix
     *
     * @return Exception|SimpleXMLElement
     * @throws Exception
     */
    protected function getXML($method, $args = [], $prefix = '')
    {
        try {
            return $this->parse(
                $this->guzzle->get(
                    $this->api.'/'.$prefix.$method.'.xml?'.preg_replace(
                        '/%5B[0-9]+%5D/simU',
                        '',
                        http_build_query(
                            array_merge($args, $this->authentication)
                        )
                    )
                ),
                'xml'
            );
        } catch (ClientException $e) {
            return $this->parse($e->getResponse(), 'xml');
        } catch (ServerException $e) {
            return $this->parse($e->getResponse(), 'xml');
        } catch (BadResponseException $e) {
            return $this->parse($e->getResponse(), 'xml');
        } catch (Exception $error) {
            return $error;
        }
    }

    /**
     * @param string $method
     * @param array  $args
     * @param string $prefix
     *
     * @return Exception|array
     * @throws Exception
     */
    protected function post($method, $args = [], $prefix = '')
    {
        //Todo use middleware to merge default values in guzzle
        //Merge default args with sent one
        $args = array_merge($args, $this->authentication);

        try {
            return $this->parse(
                $this->guzzle->request(
                    'POST',
                    $this->api.'/'.$prefix.$method.'.json',
                    [
                        RequestOptions::FORM_PARAMS => $args,
                    ]
                )
            );
        } catch (BadResponseException $e) {
            return $this->parseErrorResponse($e);
        } catch (Exception $error) {
            return $error;
        }
    }

    /**
     * @param string $method
     * @param string $args
     * @param string $prefix
     *
     * @return Exception|array
     * @throws Exception
     */
    public function postArgString($method, $args = '', $prefix = '')
    {
        $authenticationString = http_build_query($this->authentication);

        try {
            return $this->parse(
                $this->guzzle->request(
                    'POST',
                    $this->api.'/'.$prefix.$method.'.json?'.preg_replace(
                        '/%5B[0-9]+%5D/simU',
                        '',
                        $args.'&'.$authenticationString
                    )
                )
            );
        } catch (BadResponseException $e) {
            return $this->parseErrorResponse($e);
        } catch (Exception $error) {
            return $error;
        }
    }

    /**
     * The API sends its errors as JSON, which is returned like any other answer. A body that is no JSON, such as the
     * error page of a proxy, did not come from the API, so the exception is returned instead, with its status code.
     *
     * @param BadResponseException $exception
     *
     * @return BadResponseException|array|null
     * @throws Exception
     */
    protected function parseErrorResponse(BadResponseException $exception)
    {
        json_decode((string)$exception->getResponse()->getBody());

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $exception;
        }

        return $this->parse($exception->getResponse());
    }

    /**
     * @param ResponseInterface $response
     * @param string            $type
     *
     * @return array|SimpleXMLElement
     * @throws Exception
     */
    protected function parse(ResponseInterface $response, $type = 'json')
    {
        switch ($type) {
            case 'json':
                return json_decode((string)$response->getBody(), true);
            case 'xml':
                return simplexml_load_file((string)$response->getBody());
            default:
                throw new Exception(
                    "Invalid response
                 type"
                );
        }
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    protected function processAttributes($attributes = [])
    {
        $data = [];

        $i = 0;
        foreach ($attributes as $key => $value) {
            $i++;
            $data["attr-name{$i}"] = $key;
            $data["attr-value{$i}"] = $value;
        }

        return $data;
    }

    /**
     * Fill exist parameters with the same key in the output array
     *
     * @param array $parameters
     *
     * @return array
     */
    protected function fillParameters($parameters)
    {
        $output = [];
        foreach ($parameters as $parameter => $input) {
            if ( ! empty($input)) {
                $output[$parameter] = $input;
            }
        }

        return $output;
    }
}