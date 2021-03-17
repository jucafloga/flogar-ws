<?php

declare(strict_types=1);

namespace Flogar\Zip;

/**
 * Interface CompressInterface.
 */
interface CompressInterface
{
    /**
     * Compress File.
     *
     * @param string $filename
     * @param string $content
     *
     * @return string
     */
    public function compress(?string $filename, ?string $content): ?string;
}
