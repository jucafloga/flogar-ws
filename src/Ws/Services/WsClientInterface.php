<?php
declare(strict_types=1);

namespace Flogar\Ws\Services;

/**
 * Interface WsClientInterface.
 */
interface WsClientInterface
{
    /**
     * @param string $function
     * @param mixed $arguments
     *
     * @return mixed
     */
    public function call($function, $arguments);
}
