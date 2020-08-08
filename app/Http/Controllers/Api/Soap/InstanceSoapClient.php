<?php

namespace App\Http\Controllers\Api\Soap;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use SoapClient;

class InstanceSoapClient extends BaseSoapController implements InterfaceInstanceSoap
{
    // obtiene el web service
    // parametros para el servicio
    public static function init(){
        $wsdlUrl = self::getWsdl();
        $soapClientOptions = [
            'trace' => true, 
            'keep_alive' => true,
            'connection_timeout' => 5000,
            'cache_wsdl'     => WSDL_CACHE_NONE,
            'compression'   => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
        ];
        return new SoapClient($wsdlUrl, $soapClientOptions);
    }
}