<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HomeController extends AbstractController
{
    private $client;
    private $httpclient;

    public function __construct(HttpClientInterface $client )
    {
        $this->client = $client;
    }


    /**
     * @Route("/home", name="app_home")
     */
    public function api(HttpClientInterface $httpClient): Response
    {
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

        ($access_token);

    

        //Create a new client
        $httpClient = HttpClient::create();

        //Get response
        $response = $httpClient->request('GET', 'https://api.emploi-store.fr/partenaire/labonneboite/v1/company/?commune_id=71270&rome_codes=M1607', [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token
            ],

        ]);


        //Return the body of the response object as an array
        $data = $response->toArray();


        json_encode($data);

        return new JsonResponse($data);
    }
}

