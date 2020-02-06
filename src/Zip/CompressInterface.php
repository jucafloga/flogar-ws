<?php

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
    public function compress($filename, $content);
}
