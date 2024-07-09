<?php
class DocumentoElectronico
{
    public $codigoSucursalEmisor = "0000";
    public $tipoSucursal = "1";
    public $datosTransaccion;
    public $listaItems;
    public $totalesSubTotales;
}
class Cliente
{
    public $tipoClienteFE;
    public $tipoContribuyente ;
    public $numeroRUC;
    public $digitoVerificadorRUC;
    public $razonSocial;
    public $direccion;
    public $codigoUbicacion;
    public $corregimiento;
    public $distrito;
    public $provincia;
    public $telefono1;
    public $telefono2;
    public $telefono3;
    public $correoElectronico1;
    public $pais = "PA";
    public $paisOtro;
}
class DatosTransaccion
{
    public $tipoEmision = "01";
    public $tipoDocumento = "01";
    public $numeroDocumentoFiscal;
    public $puntoFacturacionFiscal = "001";
    public $fechaEmision;
    public $naturalezaOperacion = "01";
    public $tipoOperacion = "1";
    public $destinoOperacion = "1";
    public $formatoCAFE = "1";
    public $entregaCAFE = "1";
    public $envioContenedor = "1";
    public $procesoGeneracion = "1";
    public $tipoVenta = "1";
    public $informacionInteres = "";
    public $cliente;
}
class Item
{
    public $descripcion;
    public $cantidad;
    public $precioUnitario;
    public $precioUnitarioDescuento;
    public $precioAcarreo;
    public $precioSeguro;
    public $precioItem;
    public $valorTotal;
    public $tasaITBMS;
    public $valorITBMS;
}
class Totales
{
    public $totalPrecioNeto = "0.00";
    public $totalITBMS = "0.00";
    public $totalMontoGravado = "0.00";
    public $totalDescuento = "";
    public $totalAcarreoCobrado = "";
    public $valorSeguroCobrado = "";
    public $totalFactura = "0.00";
    public $totalValorRecibido = "0.00";
    public $vuelto = "0.00";
    public $tiempoPago = "1";
    public $nroItems = "1";
    public $totalTodosItems = "0.00";
    public $listaFormaPago;
}
class FormaPago
{
    public $formaPagoFact = "02";
    public $valorCuotaPagada = "0.00";
    public $descFormaPago = "";
}