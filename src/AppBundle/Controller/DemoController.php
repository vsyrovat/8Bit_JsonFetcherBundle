<?php

namespace AppBundle\Controller;

use JsonFetcherBundle\Service\JsonFetcher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DemoController extends Controller
{
    /**
     * @Route("/demo/fetch-success", name="demo.success")
     */
    public function fetchSuccessAction(JsonFetcher $jsonFetcher)
    {
        $data = $jsonFetcher->fetch('https://gist.githubusercontent.com/vsyrovat/2892a3c97ad50e24ba4f1c2de6322a55/raw/');

        return $this->render('demo/result.twig', [
            'data' => var_export($data, true),
        ]);
    }

    /**
     * @Route("/demo/error", name="demo.error")
     */
    public function fetchErrorAction(JsonFetcher $jsonFetcher)
    {
        $data = $jsonFetcher->fetch('https://gist.githubusercontent.com/vsyrovat/49625cf399cc3f675eb0ccf685187f29/raw/');

        return $this->render('demo/result.twig', [
            'data' => var_export($data, true),
        ]);
    }

    /**
     * @Route("/demo/mailformed", name="demo.mailformed")
     */
    public function fetchMailformedAction(JsonFetcher $jsonFetcher)
    {
        $data = $jsonFetcher->fetch('https://gist.githubusercontent.com/vsyrovat/f5409f8bb4d3204e9c17e8f262a6e04e/raw/');

        return $this->render('demo/result.twig', [
            'data' => var_export($data, true),
        ]);
    }

    /**
     * @Route("/demo/wrong-url", name="demo.wrong_url")
     */
    public function fetchWrongUrlAction(JsonFetcher $jsonFetcher)
    {
        $data = $jsonFetcher->fetch('mailformed url');

        return $this->render('demo/result.twig', [
            'data' => var_export($data, true),
        ]);
    }

    /**
     * @Route("/demo/success-data", name="demo.success_data")
     */
    public function jsonSuccessAction(Request $request)
    {
        return $this->json([
            'success' => true,
            'data' => [
                'locations' => [
                    [
                        'name' => 'Eiffel Tower',
                        'coordinates' => [
                            'lat' => 21.12,
                            'long' => 19.56
                        ]
                    ],
                    [
                        'name' => 'Lighthouse of Alexandria',
                        'coordinates' => [
                            'lat' => 31.12,
                            'long' => 29.53
                        ]
                    ],
                    [
                        'name' => 'Egyptian Pyramids',
                        'coordinates' => [
                            'lat' => 29.58,
                            'long' => 31.07
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * @Route("/demo/error-data", name="demo.error_data")
     */
    public function jsonErrorAction(Request $request)
    {
        return $this->json([
            'success' => false,
            'data' => [
                'message' => 'Some error message',
                'code' => 'Some error code'
            ]
        ]);
    }
}
