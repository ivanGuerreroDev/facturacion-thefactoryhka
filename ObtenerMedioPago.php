<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
include_once 'Enum/EnumMedioPago.php';

class BSNFEObtenerMedioPago
{
  public function BSNFE_ObtenerMedio($pvcMedio)
  {
    switch ($pvcMedio) {
      case BSNFEEnumMedioPago::Greenpay:
        $vlnCodigoTarifa = BSNFEEnumMedioPago::Tarjeta;
        break;
      case BSNFEEnumMedioPago::Transferencias:
        $vlnCodigoTarifa = BSNFEEnumMedioPago::Transferencia;
        break;
      case BSNFEEnumMedioPago::Cheques:
        $vlnCodigoTarifa = BSNFEEnumMedioPago::Cheque;
        break;
      case BSNFEEnumMedioPago::ContraRembolso:
        $vlnCodigoTarifa = BSNFEEnumMedioPago::PuntoDePago;
        break;
      case GTIEnumMedioPago::PaypalEstandar:
        $vlnCodigoTarifa = BSNFEEnumMedioPago::Tarjeta;
        break;
      case BSNFEEnumMedioPago::PagosPei:
        $vlnCodigoTarifa = BSNFEEnumMedioPago::Tarjeta;
        break;
      case BSNFEEnumMedioPago::Stripe:
        $vlnCodigoTarifa = BSNFEEnumMedioPago::Tarjeta;
        break;
      case BSNFEEnumMedioPago::Redsys:
        $vlnCodigoTarifa = BSNFEEnumMedioPago::Tarjeta;
        break;
      case BSNFEEnumMedioPago::Check:
        $vlnCodigoTarifa = BSNFEEnumMedioPago::Tarjeta;
        break;
      case BSNFEEnumMedioPago::Vpos:
        $vlnCodigoTarifa = BSNFEEnumMedioPago::Tarjeta;
        break;
      default:
        $vlnCodigoTarifa = BSNFEEnumMedioPago::Invalido;
    }
    return $vlnCodigoTarifa;
  }
}
?>