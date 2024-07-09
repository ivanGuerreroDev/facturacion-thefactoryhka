<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_filter('woocommerce_billing_fields', 'BSNFE_custom_woocommerce_billing_fields');

function BSNFE_custom_woocommerce_billing_fields($fields)
{

    /*
    $fields['billing_options_emitir'] = array(
        'label' => 'Emitir documento', 'factura-electronica',
        // Add custom field label
        'placeholder' => _x('Emitir documento', 'placeholder', 'factura-electronica'),
        // Add custom field placeholder
        'required' => false,
        // if field is required or not
        'clear' => false,
        // add clear or not
        'type' => 'select',
        'priority' => 80,
        // add field type
        'options' => array(
            '' => 'Por favor elija una opción',
            '1' => 'Si',
            '2' => 'No'
        )
    );*/
    $fields['billing_options_typefac'] = array(
        'label' => 'Factura', 'factura-electronica',
        // Add custom field label
        'placeholder' => _x('Por favor elija una opción', 'placeholder', 'factura-electronica'),
        // Add custom field placeholder
        'required' => true,
        // if field is required or not
        'clear' => false,
        // add clear or not
        'type' => 'select',
        'priority' => 120,
        // add field type
        'options' => array(
            '' => 'Por favor elija una opción',
            '01' => 'Factura con nombre',
            '02' => 'Factura sin nombre'
        )
    );
    $fields['billing_options_tiporuc'] = array(
        'label' => 'Tipo de RUC', 'factura-electronica',
        'priority' => 130,
        // Add custom field placeholder
        'required' => false,
        // if field is required or not
        'clear' => false,
        // add clear or not
        'type' => 'select',
        // add field type
        'options' => array(
            '' => 'Por favor elija una opción',
            '1' => 'Natural',
            '2' => 'Jurídico'
        )
    );
    $fields['billing_options_itiporec'] = array(
        'label' => 'Tipo de receptor', 'factura-electronica',
        // Add custom field label
        'placeholder' => _x('Por favor elija una opción', 'placeholder', 'factura-electronica'),
        // Add custom field placeholder
        'required' => false,
        // if field is required or not
        'clear' => false,
        // add clear or not
        'type' => 'select',
        'priority' => 130,
        // add field type
        'options' => array(
            '' => 'Por favor elija una opción',
            '01' => 'Contribuyente',
            '02' => 'Consumidor final',
            '03' => 'Gobierno',
            '04' => 'Extranjero',
        )
    );
    $fields['billing_options_razonsocial'] = array(
        'label' => 'Razon social', 'factura-electronica',
        // Add custom field placeholder
        'required' => false,
        // if field is required or not
        'clear' => false,
        // add clear or not
        'type' => 'text',
        // add field type
        'priority' => 140,
    );
    $fields['billing_options_ruc'] = array(
        'label' => '<span id="rucLabel">Número de cédula o pasaporte</span>', 'factura-electronica',
        // Add custom field placeholder
        'required' => false,
        // if field is required or not
        'clear' => false,
        // add clear or not
        'type' => 'text',
        // add field type
        'priority' => 140,
    );

    $fields['billing_options_dv'] = array(
        'label' => 'Dígito Verificador', 'factura-electronica',
        // Add custom field label
        'placeholder' => _x('Dígito Verificador....', 'placeholder', 'factura-electronica'),
        // Add custom field placeholder
        'required' => false,
        // if field is required or not
        'clear' => false,
        // add clear or not
        'type' => 'text',
        // add field type
        'priority' => 150,
    );

    $ubicacion = new BSNFEGetUbicacion();
    $corregimientos = $ubicacion->BSNFE_AllCorregimientos();
    $correList = array();
    $correList[" "] = 'Elija un valor';
    foreach ($corregimientos as $corregimiento) {
        $key = $corregimiento['IBSNFEProvincia'] . '-' . $corregimiento['IBSNFEDistrito'] . '-' . $corregimiento['IBSNFECorregimientos'];
        $correList[$key] = $corregimiento['Descripcion'];
    }
    $fields['billing_options_corre'] = array(
        'label' => 'Corregimiento', 'factura-electronica',
        // Add custom field label
        'placeholder' => _x('Corregimiento', 'placeholder', 'factura-electronica'),
        // Add custom field placeholder
        'required' => false,
        // if field is required or not
        'clear' => false,
        // add clear or not
        'type' => 'select',
        'priority' => 160,
        'id' => 'BSNFECorreg2',
        // add field type
        'options' => $correList
    );

    return $fields;
}


?>