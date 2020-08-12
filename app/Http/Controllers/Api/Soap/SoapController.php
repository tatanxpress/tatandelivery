<?php

namespace App\Http\Controllers\Api\Soap;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CreateCliente 
  {
    public $Rtl;
    public $Info;
    public $InfoV;
    public $InfoS;
  }

class SoapController extends BaseSoapController
{
   

    public function BienesServicios(){

        $rtl = "999999999999";
        $info = "9999995286545848";
        $infov = "2012";
        $infos = "123";

        try {

            $charge = new CreateCliente();
            $charge->Rtl = $rtl;
            $charge->Info = $info;
            $charge->InfoV = $infov;
            $charge->InfoS = $infos;

            $url = "https://buypasstest.redserfinsa.com:8080/BuyPass/BuyPassService.asmx?WSDL";
           
            //$res = $client->__call('charge',array('parametrs'=>$params)); 
           
            $client = new \SoapClient($url);

            return $client->CreateCliente($charge);
            
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
