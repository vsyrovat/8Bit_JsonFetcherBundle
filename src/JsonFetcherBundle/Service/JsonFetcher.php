<?php

namespace JsonFetcherBundle\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonFetcherBundle\Entity\Location;
use JsonFetcherBundle\Exception\ClientErrorException;
use JsonFetcherBundle\Exception\ErrorJsonException;
use JsonFetcherBundle\Exception\MailformedJsonException;

class JsonFetcher
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param $url
     * @return array
     * @throws \Exception
     */
    public function fetch($url)
    {
        try {
            $response = $this->client->request('GET', $url);

            $data = json_decode($response->getBody(), true);

            if ($data === null) {
                throw new MailformedJsonException('Response is not valid JSON');
            }

            if (!isset($data['success']) || !$data['success']) {
                throw new ErrorJsonException('Unsuccessfull response received');
            }

            $result = [];

            if (!isset($data['data']['locations']) || !is_array($data['data']['locations'])) {
                throw new MailformedJsonException('Response does not contain "locations" or "locations" is not array');
            }

            foreach($data['data']['locations'] as $location) {
                if (!isset($location['name']) || !is_scalar($location['name'])) {
                    throw new MailformedJsonException('Location does not contain "name"');
                }

                if (
                    !isset($location['coordinates']['lat']) ||
                    !is_numeric($location['coordinates']['lat']) ||
                    !isset($location['coordinates']['long']) ||
                    !is_numeric($location['coordinates']['long'])
                ) {
                    throw new MailformedJsonException('Location have bad coordinates');
                }

                $result[] = new Location($location['name'], $location['coordinates']['lat'], $location['coordinates']['long']);
            }

            return $result;
        } catch (GuzzleException $e) {
            throw new ClientErrorException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
