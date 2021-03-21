<?php

declare(strict_types=1);

namespace Tests\Flogar\Ws\Reader;

use DOMDocument;
use Exception;
use Flogar\Ws\Reader\DomCdrReader;
use Flogar\Ws\Reader\XmlReader;
use Flogar\Ws\Reader\XmlReaderException;
use PHPUnit\Framework\TestCase;

/**
 * Class DomCdrReaderTest.
 */
class DomCdrReaderTest extends TestCase
{
    /**
     * @var DomCdrReader
     */
    private $reader;

    protected function setUp(): void
    {
        $this->reader = new DomCdrReader(new XmlReader());
    }

    public function testCustomNsCdr(): void
    {
        $path = __DIR__.'/../../Resources/efact_cdr.xml';
        $xml = file_get_contents($path);

        $cdr = $this->reader->getCdrResponse($xml);

        $this->assertNotEmpty($cdr);
        $this->assertEquals(0, count($cdr->getNotes()));
        $this->assertEquals('F001-1', $cdr->getId());
        $this->assertEquals('0', $cdr->getCode());
        $this->assertEquals('La Facturaa F001-1 ha sido aceptada.', $cdr->getDescription());
    }
}