<?php

namespace App\Http\Controllers\Api\Soap;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


  class CreateCliente {
    public function __construct($rtl, $info, $infov, $infos) 
    {
        $this->Rtl = $rtl;
        $this->Info = $info;
        $this->Infov = $infov;
        $this->InfoS = $infos;
    }
}

class SoapController extends BaseSoapController
{
   

    public function BienesServicios(){


        $wsdl = 'https://buypasstest.redserfinsa.com:8080/BuyPass/BuyPassService.asmx?WSDL';

        $params = array(
            'CreateCliente' => [
                "Info"    => "9999995286545848",
                "InfoS"   => "123",
                "InfoV"   => "2012",
                "Rtl"     => "999999999999",
            ]
        );
        $parameters2 = array(
            'trace' => true,
            'exceptions' => true,  
            "location" => $wsdl,
            'cache_wsdl' => WSDL_CACHE_NONE,        
            'connection_timeout' => 50
        );

       

        $client = new \SoapClient($wsdl, array(
            "trace" => true,
            'cache_wsdl' => WSDL_CACHE_NONE));
        #$client->__setSoapHeaders(self::soapClientWSSecurityHeader());

        $response = $client->__soapCall("CreateCliente", array($params));


        echo "====== REQUEST HEADERS =====" . PHP_EOL;
        var_dump($client->__getLastRequestHeaders());
        echo "========= REQUEST ==========" . PHP_EOL;
        var_dump($client->__getLastRequest());
        echo "========= RESPONSE =========" . PHP_EOL;
        var_dump($response);


    }
           


    /*public function BienesServicios(){
        try {
            // web service a conectar
            self::setWsdl('http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl');
           
            // inicializar la conexion con ese web service
            $this->service = InstanceSoapClient::init();

            $countryCode = 'DK';
            $vatNumber = '47458714';

            $params = [
                'countryCode' => request()->input('countryCode') ? request()->input('countryCode') : $countryCode,
                'vatNumber'   => request()->input('vatNumber') ? request()->input('vatNumber') : $vatNumber
            ];
            $response = $this->service->checkVat($params);
            //return view ('bienes-servicios-soap', compact('response'));
            return [$response];
        }
        catch(\Exception $e) {
            return $e->getMessage();
        }
    }*/
    
    public function clima(){
        try {
            self::setWsdl('http://www.webservicex.net/globalweather.asmx?WSDL');
            $this->service = InstanceSoapClient::init();

            $cities = $this->service->GetCitiesByCountry(['CountryName' => 'Peru']);
            $ciudades = $this->loadXmlStringAsArray($cities->GetCitiesByCountryResult);
            //dd($ciudades['Table'][1]);
            return [$ciudades];
        }
        catch(\Exception $e) {
            return $e->getMessage();
        }
    }
}
