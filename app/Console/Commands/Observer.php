<?php

namespace App\Console\Commands;

use DOMDocument;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use \Spatie\Crawler\CrawlObserver;

class Observer extends CrawlObserver
{
    /**
     * Contains the data retrieved from disk
     */
    private $ftorNodes;

    /**
     * Contains the request response
     */
    private $response;

    public function willCrawl(UriInterface $uri) {
        //Retrieve stored data or initialize with empty array
        try {
            $this->ftorNodes = \json_decode( \Storage::disk('local')->get('torNodes.json'), true );
        } catch (\Exception $e) {
            $this->ftorNodes = [];
        }

        echo "Now crawling: " . (string) $uri . PHP_EOL;
    }

    /**
     * Called when the crawler has crawled the given url successfully.
     *
     * @param \Psr\Http\Message\UriInterface $url
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Psr\Http\Message\UriInterface|null $foundOnUrl
     */
    public function crawled(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null)
    {
        $this->response = (string) $response->getBody();

        // fresh nodes fetched from URL
        $nodes = $this->parseResponse();


        // Build the master node list
        foreach( $nodes as $node )
        {
            $in_fnode = false;

            // Search for existing record
            foreach( $this->ftorNodes as $k => $fnode )
            {
               if( $fnode['ip'] === $node['ip'] )
               {
                   $in_fnode = true;
                   break;
               }
            }

            // Update if found, add if not
            if( $in_fnode )
            {
                $this->ftorNodes[$k] = $node;

            } else {
                $this->ftorNodes[] = $node;

            }
        }

        // Rebuild the exit node list
        foreach( $this->ftorNodes as $node )
        {
            if( $node['exit-node'] )
            {
                $exitNodes[] = $node;
                $exitNodeIPs[] = $node['ip'];
            }
        }

        echo "Writing data to files..." . PHP_EOL;
        \Storage::disk('local')->put('torNodes.json', \json_encode($this->ftorNodes));
        \Storage::disk('local')->put('torExitNodes.json', \json_encode($exitNodes));
        \Storage::disk('local')->put('torExitNodeIPs.json', \json_encode($exitNodeIPs));
    }

    /**
     * Called when the crawler had a problem crawling the given url.
     *
     * @param \Psr\Http\Message\UriInterface $url
     * @param \GuzzleHttp\Exception\RequestException $requestException
     * @param \Psr\Http\Message\UriInterface|null $foundOnUrl
     */
    public function crawlFailed(UriInterface $url, RequestException $requestException, ?UriInterface $foundOnUrl = null)
    {
        echo 'Crawl Failed:' . PHP_EOL;
        echo "\t" . $requestException->getMessage() . PHP_EOL;
        exit;

    }

    public function finishedCrawling()
    {
        echo "Update complete!" . PHP_EOL;
    }


    protected function multiExplode( $delimiters = [], $string )
    {
        $normlized_str = str_replace($delimiters, $delimiters[0], $string);
        return explode($delimiters[0], $normlized_str);
    }

    protected function parseResponse()
    {
        $content = $this->multiExplode( ["<!-- __BEGIN_TOR_NODE_LIST__ //-->\n", "<!-- __END_TOR_NODE_LIST__ //-->\n"], $this->response );

        if( empty( $content[1] ) )
        {
            echo "Nothing to parse." . PHP_EOL;
            exit;
        }
        
        $content = str_replace( "<br />", "", $content[1]);
        $_nodes = explode( "\n", $content );

        foreach( $_nodes as $node )
        {
            if( empty($node)  )
               continue;

            $_node = explode('|', $node );
            $nodes[] = [
                'ip' => $_node[0],
                'name' => $_node[1],
                'router-port' => $_node[2],
                'directory-port' => $_node[3],
                'flags' => $_node[4],
                'uptime' => $_node[5],
                'version' => $_node[6],
                'contactinfo' => $_node[7],
                'exit-node' => false !== strpos( $_node[4], 'E' ) || false !== strpos( $_node[4], 'X' ) ? true : false,
            ];
        }

        return $nodes;
    }
}
