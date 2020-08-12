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

        $rtl = "999999999999";
        $info = "9999995286545848";
        $infov = "2012";
        $infos = "123";
        $url = "https://buypasstest.redserfinsa.com:8080/BuyPass/BuyPassService.asmx?WSDL";
        try {

            $options = Array(
                "uri" => $url,
                "style" => SOAP_RPC,
                "use"=> SOAP_ENCODED,
                "soap_version"=> SOAP_1_1,
                "cache_wsdl"=> WSDL_CACHE_BOTH,
                "connection_timeout" => 15,
                "trace" => false,
                "encoding" => "UTF-8",
                "exceptions" => false,
            );

            $client = new \SoapClient($url, $options);

            // Create Contact obj

            $contact = new CreateCliente($rtl, $info, $infov, $infos);
         
            $params = array(
                "CreateCliente" => $contact
            );
        
            return $client->__soapCall("CreateCliente", array($params));
            
        } catch(SoapFault $fault){

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
