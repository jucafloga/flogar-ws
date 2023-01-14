<?php

declare(strict_types=1);

namespace Flogar\Api;

use DateInterval;
use DateTime;
use Exception;
use Flogar\Services\Api\BasicToken;
use Flogar\Services\Api\TokenStoreInterface;
use Flogar\Sunat\GRE\Api\AuthApiInterface;
use Flogar\Sunat\GRE\Api\CpeApi;
use Flogar\Sunat\GRE\Api\CpeApiInterface;
use Flogar\Sunat\GRE\ApiException;
use Flogar\Sunat\GRE\Configuration;
use GuzzleHttp\ClientInterface;

class ApiFactory
{
    private AuthApiInterface $api;
    private ClientInterface $client;
    private TokenStoreInterface $store;
    private ?string $cpeEndpoint;

    /**
     * @param AuthApiInterface $api
     * @param ClientInterface $client
     * @param TokenStoreInterface $store
     * @param string|null $endpoint
     */
    public function __construct(
        AuthApiInterface $api,
        ClientInterface $client,
        TokenStoreInterface $store,
        ?string $endpoint
    )
    {
        $this->api = $api;
        $this->client = $client;
        $this->store = $store;
        $this->cpeEndpoint = $endpoint;
    }

    /**
     * @throws ApiException
     * @throws Exception
     */
    public function create(?string $clientId, ?string $secret, ?string $user, ?string $password): CpeApiInterface
    {
        $token = $this->getToken($clientId, $secret, $user, $password);

        $config = (new Configuration())
            ->setAccessToken($token);

        return new CpeApi(
            $this->client,
            $config->setHost($this->cpeEndpoint ?? $config->getHostFromSettings(1))
        );
    }

    /**
     * @throws Exception
     */
    private function getToken(?string $clientId, ?string $secret, ?string $user, ?string $password): ?string
    {
        $tokenData = $this->store->get($clientId);
        if ($tokenData && $tokenData->getExpire() > $this->addSeconds(new DateTime(), 600)) {
            return $tokenData->getValue();
        }

        $result = $this->api->getToken(
            'password',
            'https://api-cpe.sunat.gob.pe',
            $clientId,
            $secret,
            $user,
            $password
        );

        $token = $result->getAccessToken();
        $expire =  $this->addSeconds(new DateTime(), $result->getExpiresIn());
        $this->store->set($clientId, new BasicToken($token, $expire));

        return $token;
    }

    /**
     * @throws Exception
     */
    private function addSeconds(DateTime $time, int $seconds): DateTime
    {
        return $time->add(new DateInterval('PT'.$seconds.'S'));
    }
}
