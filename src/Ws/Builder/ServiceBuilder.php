<?php

declare(strict_types=1);

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
     */
    public function build($type)
    {
        if (!is_subclass_of($type, BaseSunat::class)) {
            throw new DocumentNoSupportException($type.' should be instance of '.BaseSunat::class);
        }

        /** @var BaseSunat $service */
        $service = new $type();
        $service->setClient($this->client);

        return $service;
    }
}
