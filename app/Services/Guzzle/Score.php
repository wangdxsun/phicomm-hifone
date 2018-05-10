<?php
/**
 * Created by PhpStorm.
 * User: meng.dai
 * Date: 2018/5/10
 * Time: 14:08
 */

namespace Hifone\Services\Guzzle;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7;
use Hifone\Exceptions\HifoneException;

class Score
{
    private $client;

    private $headers;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => env('SCORE_DOMAIN')]);
        $this->headers = [
            'Authorization' => env('SCORE_SECRET'),
            'Content-Type'  => 'application/json'
        ];
    }

    public function get($url, $data)
    {
        if (empty($data['userId'])) {
            return ['score' => 0];
        }
        try {
            $response = $this->client->get($url, [
                'headers' => $this->headers,
                'query' => $data
            ]);
            $content = json_decode($response->getBody()->getContents(), true);
            return $content['data'];
        } catch (ClientException $e) {
            throw new HifoneException(Psr7\str($e->getResponse()));
        }
    }

    public function post($url, $data)
    {
        if (empty($data['userId'])) {
            return ['score' => 0];
        }
        try {
            $response = $this->client->post($url, [
                'headers' => $this->headers,
                'json' => $data
            ]);
            $content = json_decode($response->getBody()->getContents(), true);
            if ($content['data']['errorCode'] == 10003) {
                throw new HifoneException('智慧果不足');
            }
            return $content;
        } catch (ClientException $e) {
            throw new HifoneException(Psr7\str($e->getResponse()));
        }
    }
}