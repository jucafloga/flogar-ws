<?php
declare(strict_types=1);

namespace Flogar\Ws\Services;

use Flogar\Model\Response\BaseResult;
use Flogar\Model\Response\SummaryResult;
use Flogar\Services\SenderInterface;

/**
 * Class SummarySender.
 */
class SummarySender extends BaseSunat implements SenderInterface
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
        $result = new SummaryResult();

        try {
            $zipContent = $this->compress($filename.'.xml', $content);
            $params = [
                'fileName' => $filename.'.zip',
                'contentFile' => $zipContent,
            ];
            $response = $client->call('sendSummary', ['parameters' => $params]);
            $result
                ->setTicket($response->ticket)
                ->setSuccess(true);
        } catch (\SoapFault $e) {
            $result->setError($this->getErrorFromFault($e));
        }

        return $result;
    }
}
