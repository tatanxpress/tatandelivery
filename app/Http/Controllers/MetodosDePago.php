<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MetodosDePago extends Controller
{
    //
    public function metododepago(){

        //phpinfo();
       try {
        $opts = array(
            'http' => array(
                'user_agent' => 'PHPSoapClient'
            )
        );
        $context = stream_context_create($opts);

        $wsdlUrl = 'https://buypasstest.redserfinsa.com:8080/BuyPass/BuyPassService.asmx?WSDL';
        $soapClientOptions = array(
            'stream_context' => $context,
            'cache_wsdl' => WSDL_CACHE_NONE
        );

        $client = new \SoapClient($wsdlUrl, $soapClientOptions);

        dd($client->__getTypes());

        return;

        $checkVatParameters = array(
            'countryCode' => 'DK',
            'vatNumber' => '47458714'
        );

        $result = $client->checkVat($checkVatParameters);
        print_r($result);
    }
    catch(\Exception $e) {
        echo $e->getMessage();
    }

    }


 
}
