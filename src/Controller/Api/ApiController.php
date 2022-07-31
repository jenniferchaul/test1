<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class ApiController extends AbstractController
{
    private $client;
    private $httpclient;

    public function __construct(HttpClientInterface $client )
    {
        $this->client = $client;
    }

    /**
     * @Route("/api/{commune_id}/{rome_code}", name="app_index")
     */
    public function api($commune_id, $rome_code, Request $request, RateLimiterFactory $anonymousApiLimiter): Response
    {

        // create a limiter based on a unique identifier of the client
        $limiter = $anonymousApiLimiter->create($request->getClientIp());

        // the argument of consume() is the number of tokens to consume
        // and returns an object of type Limit
        if (false === $limiter->consume(1)->isAccepted()) {
            throw new TooManyRequestsHttpException();
        }


        //$limiter = $anonymousApiLimiter->create($request->getClientIp());
        //$limit = $limiter->consume();
        //$headers = [
        //    'X-RateLimit-Remaining' => $limit->getRemainingTokens(),
        //    'X-RateLimit-Retry-After' => $limit->getRetryAfter()->getTimestamp(),
        //    'X-RateLimit-Limit' => $limit->getLimit(),
        //];
//
        //if (false === $limit->isAccepted()) {
        //    return new Response(null, Response::HTTP_TOO_MANY_REQUESTS, $headers);
        //}
//
        //// ...
//
        //$response = new Response('...');
        //$response->headers->add($headers);
//
        //return $response;


        //we get the token back with curl

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://entreprise.pole-emploi.fr/connexion/oauth2/access_token?realm=%2Fpartenaire",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "grant_type=client_credentials&client_id=PAR_testlabonneboite_4d1189710d7f63a7ba0c62453fde8073ed2928151da1e8884b8026a1ea792ee2&client_secret=56de5da3058b6d12d3e1ccd9c6b119907d444368af5c9c67962d4461402ece24&scope=api_labonneboitev1",
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/x-www-form-urlencoded"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $result = json_decode($response, true);

        $access_token = $result['access_token'];


        //Create a new client
        $httpClient = HttpClient::create();


        // we call the road in get. To use the api we need 2 arguments
        $response = $httpClient->request('GET', 'https://api.emploi-store.fr/partenaire/labonneboite/v1/company/?commune_id=' . $commune_id . '&rome_codes=' . $rome_code, [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token
            ],

        ]);

return new JsonResponse($response->getContent(), $response->getStatusCode(), [], true);
    }
}



