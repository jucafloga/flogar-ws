<?php

namespace Flogar\Ws\Services;

use Flogar\Model\Response\BillResult;
use Flogar\Model\Response\Error;
use Flogar\Validator\ErrorCodeProviderInterface;
use Flogar\Ws\Reader\CdrReaderInterface;
use Flogar\Ws\Reader\DomCdrReader;
use Flogar\Ws\Reader\XmlReader;
use Flogar\Zip\CompressInterface;
use Flogar\Zip\DecompressInterface;
use Flogar\Zip\ZipFileDecompress;
use Flogar\Zip\ZipFly;

/**
 * Class BaseSunat.
 */
class BaseSunat
{
    const NUMBER_PATTERN = '/[^0-9]+/';

    /**
     * @var WsClientInterface
     */
    private $client;

    /**
     * @var ErrorCodeProviderInterface
     */
    private $codeProvider;

    /**
     * @var CompressInterface
     */
    public $compressor;

    /**
     * @var DecompressInterface
     */
    public $decompressor;

    /**
     * @var CdrReaderInterface
     */
    public $cdrReader;

    /**
     * @param ErrorCodeProviderInterface $codeProvider
     */
    public function setCodeProvider(ErrorCodeProviderInterface $codeProvider)
    {
        $this->codeProvider = $codeProvider;
    }

    /**
     * @return WsClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param WsClientInterface $client
     *
     * @return BaseSunat
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get error from Fault Exception.
     *
     * @param \SoapFault $fault
     *
     * @return Error
     */
    protected function getErrorFromFault(\SoapFault $fault)
    {
        $error = $this->getErrorByCode($fault->faultcode, $fault->faultstring);

        if (empty($error->getMessage())) {
            $error->setMessage(isset($fault->detail) ? $fault->detail->message : $fault->faultstring);
        }

        return $error;
    }

    /**
     * @param string $code
     * @param string $optional intenta obtener el codigo de este parametro sino $codigo no es válido
     *
     * @return Error
     */
    protected function getErrorByCode($code, $optional = '')
    {
        $error = new Error($code);
        $code = preg_replace(self::NUMBER_PATTERN, '', $code);
        $message = '';

        if (empty($code) && $optional) {
            $code = preg_replace(self::NUMBER_PATTERN, '', $optional);
        }

        if ($code) {
            $message = $this->getMessageError($code);
            $error->setCode($code);
        }

        return $error->setMessage($message);
    }

    /**
     * @param string $filename
     * @param string $xml
     *
     * @return string
     */
    protected function compress($filename, $xml)
    {
        if (!$this->compressor) {
            $this->compressor = new ZipFly();
        }

        return $this->compressor->compress($filename, $xml);
    }

    /**
     * @param $zipContent
     *
     * @return \Flogar\Model\Response\CdrResponse
     */
    protected function extractResponse($zipContent)
    {
        if (!$this->cdrReader) {
            $this->cdrReader = new DomCdrReader(new XmlReader());
        }

        $xml = $this->getXmlResponse($zipContent);

        return $this->cdrReader->getCdrResponse($xml);
    }

    /**
     * @param $code
     *
     * @return string
     */
    protected function getMessageError($code)
    {
        if (empty($this->codeProvider)) {
            return '';
        }

        return $this->codeProvider->getValue($code);
    }

    protected function isExceptionCode($code)
    {
        $value = intval($code);

        return $value >= 100 && $value <= 1999;
    }

    protected function loadErrorByCode(BillResult $result, $code)
    {
        $error = $this->getErrorByCode($code);

        if (empty($error->getMessage()) && $result->getCdrResponse()) {
            $error->setMessage($result->getCdrResponse()->getDescription());
        }

        $result
            ->setSuccess(false)
            ->setError($error);
    }

    private function getXmlResponse($content)
    {
        if (!$this->decompressor) {
            $this->decompressor = new ZipFileDecompress();
        }

        $filter = function ($filename) {
            return 'xml' === strtolower($this->getFileExtension($filename));
        };
        $files = $this->decompressor->decompress($content, $filter);

        return 0 === count($files) ? '' : $files[0]['content'];
    }

    private function getFileExtension($filename)
    {
        $lastDotPos = strrpos($filename, '.');
        if (!$lastDotPos) {
            return '';
        }

        return substr($filename, $lastDotPos + 1);
    }
}
