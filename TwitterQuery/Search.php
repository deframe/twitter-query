<?php
namespace Dagrfr\TwitterQuery;

class Search
{
    protected $consumerKey;
    protected $consumerSecret;

    protected $requestTokenUrl = 'https://api.twitter.com/oauth2/token';
    protected $searchUrl       = 'https://api.twitter.com/1.1/search/tweets.json';

    public function __construct($consumerKey, $consumerSecret)
    {
        $this->consumerKey    = $consumerKey;
        $this->consumerSecret = $consumerSecret;
    }

    public function setRequestTokenUrl($requestTokenUrl)
    {
        $this->requestTokenUrl = $requestTokenUrl;
    }

    public function setSearchUrl($searchUrl)
    {
        $this->searchUrl = $searchUrl;
    }

    public function runQuery(Query $query)
    {
        $tweets = array();

        $bearerTokenCredentials        = $this->consumerKey . ":" . $this->consumerSecret;
        $encodedBearerTokenCredentials = base64_encode($bearerTokenCredentials);

        $data = http_build_query(array('grant_type' => 'client_credentials'));

        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Authorization: Basic ' . $encodedBearerTokenCredentials . "\r\n"
                          . 'Content-Type: application/x-www-form-urlencoded;charset=UTF-8' . "\r\n"
                          . 'Content-Length: ' . strlen($data) . "\r\n",
                'content'=> $data
            )
        );

        $context = stream_context_create($opts);
        $contents = file_get_contents($this->requestTokenUrl, null, $context);
        $response = json_decode($contents);

        $accessToken = $response->access_token;

        $opts = array(
            'http' => array(
                'method' => 'GET',
                'header' => 'Authorization: Bearer ' . $accessToken . "\r\n"
            )
        );

        $context = stream_context_create($opts);
        $apiJson = file_get_contents($this->searchUrl . '?' . $query->__toString(), null, $context);

        $jsonTweets = json_decode($apiJson);

        if (is_array($jsonTweets->statuses)) {
            foreach ($jsonTweets->statuses as $tweet) {
                $parsedText = $tweet->text;
                $parsedText = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@i', '<a href="\1">\1</a>', $parsedText);
                $parsedText = preg_replace('/@([a-z0-9]+)/i', '<a href="http://www.twitter.com/#!/\1">@\1</a>', $parsedText);
                $tweet->parsed_text = $parsedText;
                $tweets[] = $tweet;
            }
        }

        return $tweets;
    }
}