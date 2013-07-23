<?php
namespace Publero\ApiNotificationBundle\Client;

use Buzz\Browser;

class BuzzClient implements Client
{
    /**
     * @var \Buzz\Browser
     */
    private $browser;

    /**
     * @param Browser $browser
     */
    public function __construct(Browser $browser)
    {
        $this->browser = $browser;
    }

    /**
     * @param string $uri
     * @param string $data
     * @return bool
     */
    public function send($uri, $data)
    {
        $response = $this->browser->put($uri, [], $data);
        $statusCode = $response->getStatusCode();

        return $statusCode >= 200 && $statusCode < 300;
    }
}
