<?php

declare(strict_types=1);

namespace Flogar\Ws\Services;

use Flogar\Model\Response\BaseResult;
use Flogar\Model\Response\BillResult;
use Flogar\Model\Response\Error;
use Flogar\Services\SenderInterface;
use SoapFault;

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
            if (empty($cdrZip)) {
                $result->setError(new Error(
                        CustomErrorCodes::CDR_NOTFOUND_CODE,
                        CustomErrorCodes::CDR_NOTFOUND_BILL_MSG)
                );

                return $result;
            }

            $result
                ->setCdrResponse($this->extractResponse((string)$cdrZip))
                ->setCdrZip($cdrZip)
                ->setSuccess(true);
        } catch (SoapFault $e) {
            $result->setError($this->getErrorFromFault($e));
        }

        return $result;
    }
}
