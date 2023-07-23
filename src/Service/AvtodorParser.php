<?php

namespace Maris\Symfony\TollRoad\Service;




use Symfony\Contracts\HttpClient\HttpClientInterface;

class AvtodorParser
{
    const URI = "https://mpcalc.russianhighways.ru:8082/route_cost";

    protected HttpClientInterface $client;


    public function __construct( HttpClientInterface $client )
    {
        $this->client = $client->withOptions([
            'base_uri' => self::URI,
            "headers" => [
                "Content-Type"=>"application/json"
            ]
        ]);
    }

    public function test():mixed
    {
        return $this->client->request("POST","",[
            "body" => file_get_contents(__DIR__."/test.json")
        ]);
    }


}