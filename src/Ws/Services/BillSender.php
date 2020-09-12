<?php
declare(strict_types=1);

namespace Flogar\Ws\Services;

use Flogar\Model\Response\BaseResult;
use Flogar\Model\Response\BillResult;
use Flogar\Services\SenderInterface;

/**
 * Class BillSender.
 */
class BillSender extends BaseSunat implements SenderInterface
{
    /**
     * @param string $filename
     * @param string $content
     *
     * @return BaseResult|null
     */
    public function send(?string $filename, ?string $content): ?BaseResult
    {
        $client = $this->getClient();
        $result = new BillResult();

        try {
            $zipContent = $this->compress($filename.'.xml', $content);
            $params = [
                'fileName' => $filename.'.zip',
                'contentFile' => $zipContent,
            ];
            $response = $client->call('sendBill', ['parameters' => $params]);
            $cdrZip = $response->applicationResponse;
            $result
                ->setCdrResponse($this->extractResponse($cdrZip))
                ->setCdrZip($cdrZip)
                ->setSuccess(true);
        } catch (\SoapFault $e) {
            $result->setError($this->getErrorFromFault($e));
        }

        return $result;
    }
}
