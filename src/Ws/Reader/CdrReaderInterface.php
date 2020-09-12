<?php
declare(strict_types=1);

namespace Flogar\Ws\Reader;

use Flogar\Model\Response\CdrResponse;

/**
 * Interface CdrReaderInterface.
 */
interface CdrReaderInterface
{
    /**
     * Get Cdr using DomDocument.
     *
     * @param string $xml
     *
     * @return CdrResponse|null
     */
    public function getCdrResponse(?string $xml): ?CdrResponse;
}
