<?php

namespace Flogar\Ws\Reader;

/**
 * Interface FilenameExtractorInterface.
 */
interface FilenameExtractorInterface
{
    /**
     * @param \DOMDocument|string $content
     *
     * @return string
     */
    public function getFilename($content);
}
