<?php

namespace Flogar\Ws\Builder;

use Flogar\Ws\Services\BaseSunat;
use Flogar\Ws\Services\WsClientInterface;

/**
 * Class ServiceBuilder.
 */
class ServiceBuilder
{
    /**
     * @var WsClientInterface
     */
    private $client;

    /**
     * @param WsClientInterface $client
     *
     * @return $this
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @param string $type Service Class
     *
     * @return object
     *
     * @throws \Exception
     */
    public function build($type)
    {
        if (!is_subclass_of($type, BaseSunat::class)) {
            throw new \Exception($type.' should be instance of '.BaseSunat::class);
        }

        /** @var $service BaseSunat */
        $service = new $type();
        $service->setClient($this->client);

        return $service;
    }
}
