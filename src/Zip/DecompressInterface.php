<?php

declare(strict_types=1);

namespace Flogar\Zip;

/**
 * Interface DecompressInterface.
 */
interface DecompressInterface
{
    /**
     * Extract files.
     *
     * @param string        $content
     * @param callable|null $filter
     *
     * @return array
     */
    public function decompress(?string $content, callable $filter = null): ?array;
}
