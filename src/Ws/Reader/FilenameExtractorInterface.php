<?php

declare(strict_types=1);

namespace Flogar\Ws\Reader;

/**
 * Interface FilenameExtractorInterface.
 */
interface FilenameExtractorInterface
{
    /**
     * @param \DOMDocument|string $content
     *
     * @return string|null
     */
    public function getFilename($content): ?string;
}
