<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Suin\RSSWriter\Channel;
use Suin\RSSWriter\Feed;
use Symfony\Component\HttpFoundation\Response;
use Suin\RSSWriter\Item;

class RSSController extends Controller
{
    public function fetchSectionRSS($section)
    {

        // Validate section format
        if (!preg_match('/^[a-z0-9\-]+$/', $section)) {
            return response()->json(['error' => 'Invalid section format'], 400);
        }

        // Check cache
        $cacheKey = "rss_feed_{$section}";
       //dd($cacheKey);

        if (Cache::has($cacheKey)) {
            return response(Cache::get($cacheKey), 200, ['Content-Type' => 'application/rss+xml']);
        }

        // Fetch data from the Guardian API
        $client = new Client();
         $apiKey = 'test';
         $response = $client->get("https://content.guardianapis.com/search", [
            'query' => [
                'api-key' => $apiKey,
                $section => '',
                'show-fields' => 'trailText',
                'page-size' => 20
            ]
        ]);
    //    // dd($response);
        $data = json_decode($response->getBody(), true);

        if (!isset($data['response']['results'])) {
            return response()->json(['error' => 'Unable to fetch data'], 500);
        }
        return response($data, 200);

          }

}