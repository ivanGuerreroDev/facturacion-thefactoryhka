<?php
class BSNFESendDocument
{
    public $apiDev = 'https://demoemision.thefactoryhka.com.pa/ws/obj/v1.0/Service.svc?singleWsdl';
    public $apiProd = '';

    public function BSNFE_SendDocument($BSNFEToken, $BSNFEClave, $factura)
    {
        try {
            $pruebas = get_option('BSNFEambiente');
            $url = ($pruebas == 'on') ? $this->apiDev : $this->apiProd;
            $wsPa = new SoapClient($url);
            $parametros = array(
                'tokenEmpresa' => $BSNFEToken,
                'tokenPassword' => $BSNFEClave,
                'documento' => $factura,
            );
            $respWsPa = json_decode(json_encode($wsPa->__soapCall('Enviar', array($parametros))), true);
            return $respWsPa['EnviarResult'];
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            return array('error' => $error_message);
        }
    }
}
