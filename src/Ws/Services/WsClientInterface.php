<?php

declare(strict_types=1);

namespace Flogar\Ws\Services;

use SoapFault;

/**
 * Interface WsClientInterface.
 */
interface WsClientInterface
{
    /**
     * @param string $function
     * @param mixed $arguments
     *
     * @throws SoapFault
     * @return mixed
     */
    public function call($function, $arguments);
}
