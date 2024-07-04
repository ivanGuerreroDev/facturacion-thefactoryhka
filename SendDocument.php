<?php
class BSNFESendDocument
{

    // This plugin utilizes one of our services to receive specific order information, for the purpose of generating and issuing electronic documents.
    public $tokenDev = '';
    public $tokenProd = '';
    public $apiDev = 'https://demoemision.thefactoryhka.com.pa/ws/obj/v1.0/Service.svc';
    public $apiProd = '';

    public function BSNFE_ObtenerToken($BSNFEUsuario, $BSNFEClave)
    {
        $pruebas = get_option('BSNFEambiente');
        $url = ($pruebas == 'on') ? $this->tokenDev : $this->tokenProd;
        $response = wp_remote_post($url, array(
            'method'    => 'POST',
            'timeout'   => 45,
            'headers'   => array(
                'pUsuario' => $BSNFEUsuario,
                'pClave'   => $BSNFEClave
            ),
            'body'      => array()
        ));
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            return array('error' => $error_message);
        } else {
            $body = wp_remote_retrieve_body($response);
            $response_data = json_decode($body, true);
            $token = isset($response_data['Procesamiento']['Token']) ? $response_data['Procesamiento']['Token'] : null;
            return $token;
        }
    }

    public function BSNFE_SendDocument($token, $json)
    {
        $pruebas = get_option('BSNFEambiente');
        $url = ($pruebas == 'on') ? $this->apiDev : $this->apiProd;

        $response = wp_remote_post($url, array(
            'method'    => 'POST',
            'timeout'   => 45,
            'headers'   => array(
                'pMedioEmision' => 'Z6jdSrGuCHuK1165mElN1w==',
                'pRetornaXml'   => '0',
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json'
            ),
            'body'      => $json
        ));

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            return array('error' => $error_message);
        } else {
            $body = wp_remote_retrieve_body($response);
            return json_decode($body, true);
        }
    }

}

?>