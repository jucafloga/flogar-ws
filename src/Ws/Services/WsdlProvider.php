<?php

namespace Flogar\Ws\Services;

/**
 * Class WsdlProvider.
 */
final class WsdlProvider
{
    /**
     * Get billService WSDL Local Path.
     *
     * @return string
     */
    public static function getBillPath()
    {
        return __DIR__.'/../../Resources/wsdl/billService.wsdl';
    }

    /**
     * Get billConsultService WSDL Local Path.
     *
     * @return string
     */
    public static function getConsultPath()
    {
        return __DIR__.'/../../Resources/wsdl/billConsultService.wsdl';
    }
}
