<?php

namespace JsonFetcherBundle\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use JsonFetcherBundle\Entity\Location;
use JsonFetcherBundle\Exception\ClientErrorException;
use JsonFetcherBundle\Exception\ErrorJsonException;
use JsonFetcherBundle\Exception\MailformedJsonException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JsonFetcherTest extends WebTestCase
{
    public function testSuccess()
    {
        $mock = new MockHandler([
            new Response(200, [], '{"success":true,"data":{"locations":[{"name":"Eiffel Tower","coordinates":{"lat":21.12,"long":19.56}},{"name":"Lighthouse of Alexandria","coordinates":{"lat":31.12,"long":29.53}},{"name":"Egyptian Pyramids","coordinates":{"lat":29.58,"long":31.07}}]}}'),
        ]);

        $handler = HandlerStack::create($mock);
        $guzzleClient = new Client(['handler' => $handler]);

        $jsonFetcher = new JsonFetcher($guzzleClient);
        $data = $jsonFetcher->fetch('/');

        $this->assertEquals([
            new Location('Eiffel Tower', 21.12, 19.56),
            new Location('Lighthouse of Alexandria', 31.12, 29.53),
            new Location('Egyptian Pyramids', 29.58, 31.07),
        ], $data);
    }

    public function testErrorData()
    {
        $mock = new MockHandler([
            new Response(200, [], '{"success":false,"data":{"message":"Some error message","code":"Some error code"}}'),
        ]);

        $handler = HandlerStack::create($mock);
        $guzzleClient = new Client(['handler' => $handler]);

        $jsonFetcher = new JsonFetcher($guzzleClient);

        $this->expectException(ErrorJsonException::class);
        $jsonFetcher->fetch('/');
    }

    public function testMailformedJsonResponse()
    {
        $mock = new MockHandler([
            new Response(200, [], '{"success":true,"data":{"locations":[{"name":"Eiffel Tower","coordinates":{"lat":21.12,"long":19.56}}'),
        ]);

        $handler = HandlerStack::create($mock);
        $guzzleClient = new Client(['handler' => $handler]);

        $jsonFetcher = new JsonFetcher($guzzleClient);

        $this->expectException(MailformedJsonException::class);
        $jsonFetcher->fetch('/');
    }

    public function testBadLocationName()
    {
        $mock = new MockHandler([
            new Response(200, [], '{"success":true,"data":{"locations":[{"coordinates":{"lat":21.12,"long":19.56}}]}}'),
        ]);

        $handler = HandlerStack::create($mock);
        $guzzleClient = new Client(['handler' => $handler]);

        $jsonFetcher = new JsonFetcher($guzzleClient);

        $this->expectException(MailformedJsonException::class);
        $jsonFetcher->fetch('/');
    }

    public function testBadCoordinates()
    {
        $mock = new MockHandler([
            new Response(200, [], '{"success":true,"data":{"locations":[{"name":"Eiffel Tower"}]}}'),
        ]);

        $handler = HandlerStack::create($mock);
        $guzzleClient = new Client(['handler' => $handler]);

        $jsonFetcher = new JsonFetcher($guzzleClient);

        $this->expectException(MailformedJsonException::class);
        $jsonFetcher->fetch('/');
    }

    public function testClientError()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $jsonFetcher = $container->get(JsonFetcher::class);

        $this->expectException(ClientErrorException::class);
        $jsonFetcher->fetch('mailformed url');
    }
}
