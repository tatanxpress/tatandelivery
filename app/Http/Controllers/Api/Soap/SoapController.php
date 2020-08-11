<?php

namespace App\Http\Controllers\Api\Soap;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SoapController extends BaseSoapController
{
    private $service;

    public function BienesServicios(){
        try {

           // ini_set('default_socket_timeout', 600);

            // web service a conectar
            //self::setWsdl('https://buypasstest.redserfinsa.com:8080/BuyPass/BuyPassService.asmx?WSDL');
           
            // inicializar la conexion con ese web service
            //$this->service = InstanceSoapClient::init();
 
            /*$countryCode = 'DK';
            $vatNumber = '47458714';

            $params = [
                'countryCode' => request()->input('countryCode') ? request()->input('countryCode') : $countryCode,
                'vatNumber'   => request()->input('vatNumber') ? request()->input('vatNumber') : $vatNumber
            ];*/

           // self::setWsdl('http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl');

            //$url = "http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl";

            $url = "https://buypasstest.redserfinsa.com:8080/BuyPass/BuyPassService.asmx?WSDL";

            $rtl = "999999999999";
            $info = "9999995286545848";
            $infov = "2012";
            $infos = "123";

            $headerbody = array('Info'=> $info, 'InfoS' => $infos, 'Infov' => $infov, 'Rtl' => $rtl); 

            $header = new SoapHeader($url, 'CreateCliente', $headerbody);       

            //set the Headers of Soap Client.
            $soap_client->__CreateCliente($header);

            return $soap_client;

            $countryCode = 'DK';
            $vatNumber = '47458714';

            $params = [
                'countryCode' => $countryCode,
                'vatNumber'   => $vatNumber
            ];

            $response = $client->CreateCliente($rtl, $info, $infov, $infos);

            //$response = $this->service->CreateCliente($rtl, $info, $infov, $infos);
            //return view ('bienes-servicios-soap', compact('response'));
            return [$response];
        }
        catch(\Exception $e) {
            return $e->getMessage();
        }
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
