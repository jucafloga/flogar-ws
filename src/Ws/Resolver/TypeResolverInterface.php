<?php
declare(strict_types=1);

namespace Flogar\Ws\Resolver;

/**
 * Interface TypeResolverInterface
 */
interface TypeResolverInterface
{
    /**
     * @param \DOMDocument|string $value
     * @return string|null
     */
    public function getType($value): ?string;
}