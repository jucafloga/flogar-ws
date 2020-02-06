<?php

namespace Flogar\Ws\Resolver;

use Flogar\Model\Perception\Perception;
use Flogar\Model\Retention\Retention;
use Flogar\Model\Sale\Invoice;
use Flogar\Model\Sale\Note;
use Flogar\Model\Summary\Summary;
use Flogar\Model\Voided\Reversion;
use Flogar\Model\Voided\Voided;
use Flogar\Ws\Reader\XmlReader;

class XmlTypeResolver implements TypeResolverInterface
{
    /**
     * @var XmlReader
     */
    private $reader;

    /**
     * XmlTypeResolver constructor.
     *
     * @param XmlReader $reader
     */
    public function __construct(XmlReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param \DOMDocument|string $value
     *
     * @return string
     */
    public function getType($value)
    {
        $doc = $this->reader->parseToDocument($value);
        $name = $doc->documentElement->localName;

        switch ($name) {
            case 'Invoice':
                return Invoice::class;
            case 'CreditNote':
            case 'DebitNote':
                return Note::class;
            case 'Perception':
                return Perception::class;
            case 'Retention':
                return Retention::class;
            case 'SummaryDocuments':
                return Summary::class;
            case 'VoidedDocuments':
                $this->reader->loadXpathFromDoc($doc);
                $id = $this->reader->getValue('cbc:ID');

                return 'RA' === substr($id, 0, 2) ? Voided::class : Reversion::class;
        }

        return '';
    }
}
