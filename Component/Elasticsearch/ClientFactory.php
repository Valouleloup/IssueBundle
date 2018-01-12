<?php

namespace Valouleloup\IssueBundle\Component\Elasticsearch;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class ClientFactory
{
    /**
     * @var string
     */
    private $hostname;

    /**
     * @var int
     */
    private $port;

    public function __construct($hostname, $port = 9200)
    {
        $this->hostname = $hostname;
        $this->port     = $port;
    }

    /**
     * @return Client
     */
    public function createClient()
    {
        $hosts = [
            [
                'host' => $this->hostname,
                'port' => $this->port,
            ],
        ];

        $builder = ClientBuilder::create();
        $builder->setHosts($hosts);

        return $builder->build();
    }
}