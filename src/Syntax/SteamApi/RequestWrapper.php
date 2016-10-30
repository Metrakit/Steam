<?php

namespace Syntax\SteamApi;

use GuzzleHttp\Client as GuzzleClient;
use stdClass;
use GuzzleHttp\Psr7\Request;
use Exception;
use GuzzleHttp\Exception\ClientErrorResponseException;
use GuzzleHttp\Exception\ServerErrorResponseException;
use Syntax\SteamApi\Exceptions\ApiCallFailedException;
use Syntax\SteamApi\Exceptions\ClassNotFoundException;

trait RequestWrapper {

    protected $client;

    public function __construct() {
        $this->client = new GuzzleClient();
    }

    /**
     * @param \Guzzle\Http\Message\RequestInterface $request
     *
     * @throws ApiCallFailedException
     * @return stdClass
     */
    protected function sendRequest($request)
    {
        // Try to get the result.  Handle the possible exceptions that can arise
        try {
            $response = $this->client->send($request);

            $result       = new stdClass();
            $result->code = $response->getStatusCode();
            $result->body = json_decode($response->getBody(true));
        } catch (ClientErrorResponseException $e) {
            throw new ApiCallFailedException($e->getMessage(), $e->getResponse()->getStatusCode(), $e);
        } catch (ServerErrorResponseException $e) {
            throw new ApiCallFailedException("Appel à l'API impossible à cause d'une erreur du serveur.", $e->getResponse()->getStatusCode(), $e);
        } catch (Exception $e) {
            throw new ApiCallFailedException($e->getMessage(), $e->getCode(), $e);
        }

        // If all worked out, return the result
        return $result;
    }

}
