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

        // Generate RSS Feed
        $feed = new Feed();
        $channel = new Channel();
        $channel->title("The Guardian: $section")
            ->description("Latest articles from The Guardian's $section section")
            ->url(request()->fullUrl());

        foreach ($data['response']['results'] as $article) {
            $item = new Item();
            $pubDate = new \DateTime($article['webPublicationDate']);
            
            $item->title($article['webTitle'])
                ->description($article['fields']['trailText'] ?? '')
                ->url($article['webUrl'])
                ->pubDate($pubDate->getTimestamp());
            
            $channel->addItem($item);
        }

        $feed->addChannel($channel);
        $rssContent = $feed->render();

        // Cache the RSS feed
        Cache::put($cacheKey, $rssContent, now()->addMinutes(10));

        return response($rssContent, 200, ['Content-Type' => 'application/rss+xml']);
    }

}