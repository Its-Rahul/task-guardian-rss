<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Guardian RSS Feed API</title>
    <style>
    body {
        font-family: system-ui, -apple-system, sans-serif;
        line-height: 1.6;
        max-width: 800px;
        margin: 40px auto;
        padding: 0 20px;
        color: #333;
    }

    .example {
        background: #f5f5f5;
        padding: 15px;
        border-radius: 4px;
        font-family: monospace;
    }

    .url {
        color: #2563eb;
    }
    </style>
</head>

<body>
    <h1>Guardian RSS Feed API</h1>

    <p>
        This is a server-side application in PHP exposing RSS feeds corresponding to the categories
        of The Guardian, a leading UK newspaper. Users can request URLs in the format
        <code>/[section-name]</code> to receive an RSS feed with the latest articles from that section.
    </p>

    <h2>Example URLs:</h2>
    <div class="example">
        <p>Politics news:<br>
            <span class="url">{{ url('api/politics') }}</span>
        </p>
        <p>Business updates:<br>
            <span class="url">{{ url('api/business') }}</span>
        </p>
        <p>Entertainment news:<br>
            <span class="url">{{ url('api/culture') }}</span>
        </p>
        <p>Technology articles:<br>
            <span class="url">{{ url('api/technology') }}</span>
        </p>
        <p>Lifestyle content:<br>
            <span class="url">{{ url('api/lifeandstyle') }}</span>
        </p>
    </div>

    <h2>Available Sections:</h2>
    <ul>
        <li>politics</li>
        <li>business</li>
        <li>technology</li>
        <li>culture</li>
        <li>sport</li>
        <li>education</li>
        <li>environment</li>
        <li>lifeandstyle</li>
        <li>media</li>
        <li>science</li>
        <li>society</li>
        <li>world</li>
    </ul>

    <p>
        <strong>Usage:</strong> Simply append the desired section name to the base URL to get
        the RSS feed for that section. The API will return the latest articles in RSS format.
    </p>


</body>

</html>