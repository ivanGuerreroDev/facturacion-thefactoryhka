<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
include_once "Enum/EnumCodigoTarifa.php";
include_once "Enum/EnumTipoMoneda.php";
include_once "Enum/EnumCodigoImpuesto.php";
 
class BSNFEObtenerImpuesto
{
  public function BSNFE_ObtenerCodigoTarifa($pvnCodigoTarifa)
  {
      switch($pvnCodigoTarifa) 
      {
          case "tarifa_Exento" :
              $vlnCodigoTarifa=BSNFEEnumCodigoTarifa::tarifa_Exento;
            break;
          case "tarifa_7" :
              $vlnCodigoTarifa=BSNFEEnumCodigoTarifa::tarifa_7;
            break;
          case "tarifa_10" :
              $vlnCodigoTarifa=BSNFEEnumCodigoTarifa::tarifa_10;
            break;
          case "tarifa_15" :
              $vlnCodigoTarifa=BSNFEEnumCodigoTarifa::tarifa_15;
            break;
          default:
            $vlnCodigoTarifa=BSNFEEnumCodigoTarifa::tarifa_Exento;
      }                
      return $vlnCodigoTarifa;               
  }
        
    public function BSNFE_ObtenerMoneda($pvnMoneda)
    {
        switch($pvnMoneda) 
        {
            case BSNFEEnumTipoMoneda::MonedaUSD:
                $vlnMoneda=BSNFEEnumTipoMoneda::USD;
            break;
            default:
                $vlnMoneda=BSNFEEnumTipoMoneda::MonedaInvalida;
        }
        return $vlnMoneda;
    }
}
?>