<?php

/**
 * Plugin Name:       Factura Electronica Firmatech
 * Requires Plugins:  woocommerce
 * Plugin URI:        https://www.businet.dev/
 * Description:       Plugin para emitir factura electrónica.
 * Version:           1.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            BUSINET
 * Author URI:        https://businet.dev
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       factura-electronica-firmatech
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly
include_once 'Document/Document.php';
include_once 'Document/DocumentoElectronico.php';
include_once 'SendDocument.php';
include_once 'ObtenerImpuesto.php';
include_once 'ObtenerDescuento.php';
include_once 'Ubicacion/Ubicacion.php';
include_once 'Configuracion/Configuracion.php';
include_once 'Validaciones/Checkout.php';
include_once 'CargaAtributos.php';
include_once 'ObtenerMedioPago.php';
include_once 'Taxes/PanamaTaxes.php';
include_once 'CustomFields/ReceptorFields.php';

ob_start();
// Verificar que WooCommerce está activo
if (!in_array("woocommerce/woocommerce.php", apply_filters("active_plugins", get_option("active_plugins")))) {
    return;
}

add_action('template_redirect', 'BSNFE_EmitirFacturaElectronica');
function BSNFE_EmitirFacturaElectronica()
{
    try {
        if (!is_wc_endpoint_url("order-received")) {
            return;
        }
        global $wp;
        // Si está en inglés
        $order_id = intval(str_replace("checkout/order-received/", "", $wp->request));
        if ($order_id == 0) {
            // Si está en español
            $order_id = intval(str_replace("finalizar-compra/order-received/", "", $wp->request));
        }
        // Obtener datos de compra
        $order = wc_get_order($order_id);

        $already_processed = get_post_meta($order_id, '_send_invoice_pf', true);
        if (!$already_processed) {
            $send = BSNFE_sendFactura($order);
            //var_dump($send);
            $note = "";
            if ($send) {
                if ($send['codigo'] == '200') {
                    update_post_meta($order->get_id(), '_send_invoice_pf', true);
                    $vlcMensaje = 'Factura Electronica enviada a Factura Electrónica con el número: ' . $send['cufe'];
                    $vlcTipoMensaje = 'success';
                    BSNFE_ImprimirMensaje($vlcMensaje, $vlcTipoMensaje);
                    $note = 'Factura Electronica enviada a Factura Electrónica con el número: ' . $send['cufe'];
                } else {
                    $vlcMensaje = 'Ha ocurrido un error al generar su factura electrónica. Por favor, comuníquese con el administrador del sitio.';
                    $vlcTipoMensaje = 'error';
                    BSNFE_ImprimirMensaje($vlcMensaje, $vlcTipoMensaje);
                    update_post_meta($order->get_id(), '_send_invoice_pf', false);
                    $note = $vlcMensaje;
                }
                $order->add_order_note($note, $is_customer_note = 0, $added_by_user = false);
            } else {
                $vlcMensaje = 'Ha ocurrido un error al generar su factura electrónica. Por favor, comuníquese con el administrador del sitio.';
                $vlcTipoMensaje = 'error';
                BSNFE_ImprimirMensaje($vlcMensaje, $vlcTipoMensaje);
                update_post_meta($order->get_id(), '_send_invoice_pf', false);
                $order->add_order_note($vlcMensaje, $is_customer_note = 0, $added_by_user = false);
            }
        } else {
            $vlcTipoMensaje = 'success';
            $vlcMensaje = 'La factura ya ha sido emitida anteriormente.';
            BSNFE_ImprimirMensaje($vlcMensaje, $vlcTipoMensaje);
        }
    } catch (\Throwable $th) {
        add_post_meta($order->get_id(), '_send_invoice_pf', false);
        $order->add_order_note('Error al enviar la factura electrónica a Factura Electrónica, intente reenviarla manualmente', $is_customer_note = 0, $added_by_user = false);
        $vlcMensaje = 'Ha ocurrido un error al generar su factura electrónica, por favor comuníquese con el administrador del sitio.';
        $vlcTipoMensaje = 'error';
        BSNFE_ImprimirMensaje($vlcMensaje, $vlcTipoMensaje);
    }
}
function BSNFE_docData_toFactura($docData, $order_id)
{
    $factura = new DocumentoElectronico();
    $cliente = new Cliente();
    if ($docData['gDGen']['gDatRec']['typefac'] == "02") {
        $cliente->tipoClienteFE = "02";
        $cliente->tipoContribuyente = "1";
    } else {
        $cliente->tipoClienteFE = $docData['gDGen']['gDatRec']['iTipoRec'];
        $cliente->tipoContribuyente = $docData['gDGen']['gDatRec']['gRucRec']['dTipoRuc'];
        $cliente->numeroRUC = $docData['gDGen']['gDatRec']['gRucRec']['dRuc'];
        $cliente->digitoVerificadorRUC = $docData['gDGen']['gDatRec']['gRucRec']['dDV'];
        $cliente->razonSocial = $docData['gDGen']['gDatRec']['dNombRec'];
        $cliente->direccion = $docData['gDGen']['gDatRec']['dDirecRec'];
        $cliente->telefono1 = $docData['gDGen']['gDatRec']['dTfnRec'];
        $cliente->correoElectronico1 = $docData['gDGen']['gDatRec']['dCorElectRec'];
        $cliente->codigoUbicacion = $docData['gDGen']['gDatRec']['gUbiRec']['dCodUbi'];
        $cliente->corregimiento = $docData['gDGen']['gDatRec']['gUbiRec']['dCorreg'];
        $cliente->distrito = $docData['gDGen']['gDatRec']['gUbiRec']['dDistr'];
        $cliente->provincia = $docData['gDGen']['gDatRec']['gUbiRec']['dProv'];
        $cliente->pais = $docData['gDGen']['gDatRec']['cPaisRec'];
    }
    $datos = new DatosTransaccion();
    $datos->puntoFacturacionFiscal = $docData['gDGen']['dPtoFacDF'];
    $datos->fechaEmision = (new \DateTime('America/Panama'))->format('Y-m-d\TH:i:s-05:00');
    $datos->tipoEmision = $docData['gDGen']['iTpEmis'];
    $datos->tipoDocumento = $docData['gDGen']['iDoc'];
    $numeroDocFiscal = $order_id;
    $datos->numeroDocumentoFiscal = $numeroDocFiscal;
    $datos->cliente = $cliente;
    $factura->datosTransaccion = $datos;
    $items = [];
    foreach ($docData['gItem'] as $key => $value) {
        $item = new Item();
        $item->descripcion = $value['dDescProd'];
        $item->cantidad = $value['dCantCodInt'];
        $item->precioUnitario = $value['gPrecios']['dPrUnit'];
        $item->precioUnitarioDescuento = $value['gPrecios']['dPrUnitDesc'];
        $item->precioItem = $value['gPrecios']['dPrItem'];
        $item->valorTotal = $value['gPrecios']['dValTotItem'];
        $item->tasaITBMS = $value['gITBMSItem']['dTasaITBMS'];
        $item->valorITBMS = $value['gITBMSItem']['dValITBMS'];
        $items[] = $item;
    }
    $factura->listaItems = $items;
    $totales = new Totales();
    $totales->totalPrecioNeto = $docData['gTot']['dTotNeto'];
    $totales->totalITBMS = $docData['gTot']['dTotITBMS'];
    $totales->totalMontoGravado = $docData['gTot']['dTotGravado'];
    $totales->totalFactura = $docData['gTot']['dVTot'];
    $totales->totalValorRecibido = $docData['gTot']['dTotRec'];
    $totales->nroItems = $docData['gTot']['dNroItems'];
    $totales->totalTodosItems = $docData['gTot']['dVTotItems'];

    $formaPago = new FormaPago();
    $formaPago->formaPagoFact = $docData['gTot']['gFormaPago'][0]['iFormaPago'];
    $formaPago->valorCuotaPagada = $docData['gTot']['gFormaPago'][0]['dVlrCuota'];
    $totales->listaFormaPago = array($formaPago);
    $factura->totalesSubTotales = $totales;
    return $factura;
}

function BSNFE_sendFactura($order)
{
    $documento = new BSNFEDocumento();
    $sendDocument = new BSNFESendDocument();
    $ubicacion = new BSNFEGetUbicacion();
    // Validar que el pago se realizó correctamente
    if ($order->get_status() != "Failed" && $order->get_status() != "Canceled" && $order->get_status() != "cancelled") {
        $data = $order->get_data();
        $order_id = $order->get_id();
        $items = $order->get_items();
        //$emitir = $order->get_meta('_billing_options_emitir');
        $emitir = "1";
        $receptorBillingData = array(
            'billing' => $data["billing"],
            'typefac' => $order->get_meta('_billing_options_typefac'),
            'tiporuc' => $order->get_meta('_billing_options_tiporuc'),
            'iTipoRec' => $order->get_meta('_billing_options_itiporec'),
            'ruc' => $order->get_meta('_billing_options_ruc'),
            'razonsocial' => $order->get_meta('_billing_options_razonsocial'),
            'dv' => $order->get_meta('_billing_options_dv'),
            'corregimiento' => $order->get_meta('_billing_options_corre'),
        );
        $tmpshipping_method = $order->get_shipping_methods();
        if (!empty($tmpshipping_method)) {
            $shipping_method = array_shift($tmpshipping_method);
            $shippingData = array(
                'total' => $shipping_method['total'],
                'name' => $shipping_method['name']
            );
        } else {
            $shippingData = array(
                'total' => 0,
                'name' => ''
            );
        }
        // Generar factura
        if ($emitir == "1") {
            $emisor = $documento->BSNFE_ObtenerEmisor();
            $receptor = $documento->BSNFE_ObtenerReceptor($receptorBillingData);
            $lineas = $documento->BSNFE_GenerarLineas($items, $shippingData);
            $totales = $documento->BSNFE_ObtenerTotales($lineas, $order);
            $docData = array(
                'gDGen' => array(
                    "iTpEmis" => "01",
                    "iDoc" => "01",
                    "dPtoFacDF" => get_option('BSNFEPunto_Facturacion'),
                    "dFechaEm" => date('Y-m-d\TH:i:sP', current_time('timestamp')),
                    "iNatOp" => "01",
                    "iTipoOp" => 1,
                    "iDest" => 1,
                    "iFormCAFE" => 3,
                    "iEntCAFE" => 3,
                    "dEnvFE" => 1,
                    "iTipoTranVenta" => 1,
                    "iTipoSuc" => 1,
                    "dInfEmFE" => "",
                    'gEmis' => $emisor,
                    'gDatRec' => $receptor,
                ),
                'gItem' => $lineas,
                'gTot' => $totales,
            );
            $factura = BSNFE_docData_toFactura($docData, $order_id);
            $user = get_option('BSNFEToken');
            $pass = get_option('BSNFEClave');
            $send = $sendDocument->BSNFE_SendDocument($user, $pass, $factura);
            return $send;
        } else {
            return false;
        }
    }
}

// Función para formatear cadena en flotante
function BSNFE_tofloat($num)
{
    $dotPos = strrpos($num, '.');
    $commaPos = strrpos($num, ',');
    $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos : ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);
    if (!$sep) {
        return floatval(preg_replace("/[^0-9]/", "", $num));
    }
    return floatval(
        preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . "." .
            preg_replace("/[^0-9]/", "", substr($num, $sep + 1, strlen($num)))
    );
}

// Función para mostrar notificaciones en pantalla
function BSNFE_ImprimirMensaje($pvcMensaje, $pvcTipoMensaje)
{
    wc_add_notice($pvcMensaje, $pvcTipoMensaje);
    wc_print_notices();
}



function BSNFE_admin_enqueue_scripts_callback()
{
    wp_enqueue_style('select2-css2', '/wp-content/plugins/woocommerce-facturacion/css/tagify.css', array());
    // Agregar el archivo CSS de Select2
    wp_enqueue_style('select2-css', '/wp-content/plugins/woocommerce-facturacion/css/select2.min.css', array());
    // Agregar el archivo JavaScript de Select2
    wp_enqueue_script('select2-js', '/wp-content/plugins/woocommerce-facturacion/JS/select2.min.js', array('jquery'));
    wp_enqueue_script('select2-js2', '/wp-content/plugins/woocommerce-facturacion/JS/jQuery.tagify.min.js', array('jquery'));
    wp_enqueue_script('select2-js3', '/wp-content/plugins/woocommerce-facturacion/JS/Tags.js', array('jquery'));
}
add_action('admin_enqueue_scripts', 'BSNFE_admin_enqueue_scripts_callback');

function BSNFE_admin_enqueue_scripts_callback2()
{
    wp_enqueue_style('select2-css', '/wp-content/plugins/woocommerce-facturacion/css/tagify2.css', array());
}
add_action('admin_enqueue_scripts', 'BSNFE_admin_enqueue_scripts_callback2');

function BSNFE_wp_enqueue_scripts_callback()
{
    // Agregar el archivo CSS de Select2
    wp_enqueue_style('select2-css', '/wp-content/plugins/woocommerce-facturacion/css/select22.min.css', array());
    wp_enqueue_script('jquery');
    // Agregar el archivo JavaScript de Select2
    wp_enqueue_script('select2-js', '/wp-content/plugins/woocommerce-facturacion/JS/select22.min.js', array('jquery'));
    // Agregar un archivo JavaScript para inicializar los elementos de Select2
    wp_enqueue_script('select2-init', '/wp-content/plugins/woocommerce-facturacion/JS/Select2.js', array('jquery'));
}
add_action('wp_enqueue_scripts', 'BSNFE_wp_enqueue_scripts_callback');

add_action("wp_ajax_BSNFE_provincias_get", "BSNFE_provincias_get");
add_action("wp_ajax_nopriv_BSNFE_provincias_get", "BSNFE_provincias_get");

function BSNFE_provincias_get()
{
    header('Content-Type: application/json; charset=utf-8');
    $ubicacion = new BSNFEGetUbicacion();
    $provincias = $ubicacion->BSNFE_Provincias();
    echo wp_json_encode($provincias);
    wp_die();
}

add_action("wp_ajax_BSNFE_distritos_get", "BSNFE_distritos_get");
add_action("wp_ajax_nopriv_BSNFE_distritos_get", "BSNFE_distritos_get");

function BSNFE_distritos_get()
{
    header('Content-Type: application/json; charset=utf-8');
    $ubicacion = new BSNFEGetUbicacion();
    $distritos = $ubicacion->BSNFE_Distritos();
    echo wp_json_encode($distritos);
    wp_die();
}

add_action("wp_ajax_BSNFE_corregimiento_get", "BSNFE_corregimiento_get");
add_action("wp_ajax_nopriv_BSNFE_corregimiento_get", "BSNFE_corregimiento_get");

function BSNFE_corregimiento_get()
{
    header('Content-Type: application/json; charset=utf-8');
    $ubicacion = new BSNFEGetUbicacion();
    $distritos = $ubicacion->BSNFE_Corregimientos();
    echo wp_json_encode($distritos);
    wp_die();
}

// Definir la función a ejecutar para los usuarios desconectados
function BSNFE_please_login()
{
    echo "Debe iniciar sesión para realizar esta acción";
    die();
}

add_action('woocommerce_order_actions', 'BSNFE_add_custom_action');

function BSNFE_add_custom_action($actions)
{
    $actions['add_my_custom_action'] = 'Reenviar factura a Factura Electrónica';
    return $actions;
}

add_action('woocommerce_order_action_add_my_custom_action', 'BSNFE_add_my_custom_action_function');

function BSNFE_add_my_custom_action_function($order)
{
    try {
        $order = wc_get_order($order->get_id());
        $enviado = get_post_meta($order->get_id(), '_send_invoice_pf', true);
        if ($enviado == "") {
            add_post_meta($order->get_id(), '_send_invoice_pf', false);
        }
        if ($enviado != 1) {
            $send = BSNFE_sendFactura($order);
            if ($send) {
                if ($send['codigo'] == '200') {
                    update_post_meta($order->get_id(), '_send_invoice_pf', true);
                    $vlcMensaje = 'Factura Electronica enviada a Factura Electrónica con el número: ' . $send['cufe'];
                } else {
                    $vlcMensaje = 'Ha ocurrido un error al generar su factura electrónica. Por favor, comuníquese con el administrador del sitio: ';
                    update_post_meta($order->get_id(), '_send_invoice_pf', false);
                }
            }
            $order->add_order_note($vlcMensaje, $is_customer_note = 0, $added_by_user = false);
        } else {
            $order->add_order_note("Error al enviar la factura electrónica a Factura Electrónica, ya la factura se ha enviado anteriormente.", $is_customer_note = 0, $added_by_user = false);
        }
    } catch (\Throwable $th) {
        // Excribir error en log.

        update_post_meta($order->get_id(), '_send_invoice_pf', false);
        $order->add_order_note('Error al enviar la factura electrónica a Factura Electrónica, intente reenviarla manualmente', true);
    }
}

function BSNFE_writeLog($name, $data)
{
    // Codificar array a JSON
    $json = json_encode(array('data' => $data));
    // Escribir JSON en archivo
    file_put_contents($name . ".json", $json);
}
