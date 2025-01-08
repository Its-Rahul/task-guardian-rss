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

        // Define cache key
        $cacheKey = "rss_feed_{$section}";

        // Try to get from cache using remember() instead of manual check and put
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($section) {
            // Fetch data from the Guardian API
            try {
                $client = new Client();
                $response = $client->get("https://content.guardianapis.com/search", [
                    'query' => [
                        'api-key' => 'test',
                        $section => '',
                        'show-fields' => 'trailText',
                        'page-size' => 20
                    ]
                ]);

                $data = json_decode($response->getBody(), true);

                if (!isset($data['response']['results'])) {
                    throw new \Exception('Invalid API response');
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

                return response($rssContent, 200, ['Content-Type' => 'application/rss+xml']);
            } catch (\Exception $e) {
                // Log the error
                \Log::error('RSS Feed Generation Error: ' . $e->getMessage());
                throw $e;
            }
        });
    }
}