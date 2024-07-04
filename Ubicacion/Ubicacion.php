<?php

class BSNFEGetUbicacion
{

    public $codigoUbicacion = "1-1-1";
    public $Corregimiento = "BOCAS DEL TORO (CABECERA)";
    public $Distrito = "BOCAS DEL TORO";
    public $Provincia = "BOCAS DEL TORO";

    public function BSNFE_GenerarUbicacion($iBSNFECorregimiento, $iBSNFEProvincia)
    {

        $corregimiento = $this->BSNFE_ObtenerCorregimiento($iBSNFECorregimiento, $iBSNFEProvincia);
		
		if ($corregimiento !== null && isset($corregimiento['IBSNFEProvincia'], $corregimiento['IBSNFEDistrito'], $corregimiento['IBSNFECorregimientos'])) {
			$provincia = $this->BSNFE_ObtenerProvincia($corregimiento['IBSNFEProvincia']);
			$distrito = $this->BSNFE_ObtenerDistrito($corregimiento['IBSNFEDistrito'], $corregimiento['IBSNFEProvincia']);
			$codigoUbicacion = "{$corregimiento['IBSNFEProvincia']}-{$corregimiento['IBSNFEDistrito']}-{$corregimiento['IBSNFECorregimientos']}";
			$Corregimiento = esc_attr($corregimiento['Descripcion']);
			$Distrito = esc_attr($distrito['Descripcion']);
			$Provincia = esc_attr($provincia['Descripcion']);
			$codigoUbicacion = esc_attr($codigoUbicacion);
			
			$data = array(
				'CodigoUbicacion' => $codigoUbicacion,
				'Corregimiento' => $Corregimiento,
				'Distrito' => $Distrito,
				'Provincia' => $Provincia
			);
			
			return $data;
			
		}else{
			
			$data = array(
				'CodigoUbicacion' => "",
				'Corregimiento' => "",
				'Distrito' => "",
				'Provincia' => ""
			);
			
			return $data;
		}

    }


        function BSNFE_Provincias(){
            $url=  WP_PLUGIN_DIR ."/WooCommerce-PA/Files/Provincias.json";
            $provinciasContents = file_get_contents( $url,true);
            $provinciasList = json_decode($provinciasContents, true);
            return $provinciasList;
        }
        function BSNFE_Distritos(){
            // Sanear y escapar el valor de $_GET["provincia"]
            $iBSNFEProvincia = isset($_GET["provincia"]) ? intval($_GET["provincia"]) : 0;
            $url = WP_PLUGIN_DIR . "/WooCommerce-PA/Files/Distritos.json";
        
            // Obtener el contenido del archivo JSON y sanitizarlo
            $distritosContents = file_get_contents($url);
            $distritosContents = wp_kses_post($distritosContents);
        
            // Decodificar el JSON en un array asociativo
            $distritosList = json_decode($distritosContents, true);
            
            // Validar que $distritosList sea un array
            if (is_array($distritosList)) {
                $filtered_arr = [];
        
                // Filtrar los distritos por provincia
                foreach($distritosList as $distrito) {
                    if(isset($distrito['IBSNFEProvincia']) && intval($distrito['IBSNFEProvincia']) === $iBSNFEProvincia) {
                        $filtered_arr[] = $distrito;
                    }
                }
        
                return $filtered_arr;
            } else {
                return [];
            }
        }
        function BSNFE_Corregimientos() {
            // Obtener y sanear los parámetros de provincia y distrito
            $iBSNFEProvincia = isset($_GET["provincia"]) ? absint($_GET["provincia"]) : 0;
            $iBSNFEDistrito = isset($_GET["distrito"]) ? absint($_GET["distrito"]) : 0;
        
            // Construir la ruta del archivo JSON de manera segura
            $url = WP_PLUGIN_DIR . "/WooCommerce-PA/Files/Corregimientos.json";
        
            // Verificar si el archivo existe antes de intentar abrirlo
            if (file_exists($url)) {
                // Obtener el contenido del archivo JSON
                $corregimientosContents = file_get_contents($url);
        
                // Decodificar el JSON en un array asociativo
                $corregimientosList = json_decode($corregimientosContents, true);
        
                // Verificar si el JSON se decodificó correctamente
                if ($corregimientosList !== null) {
                    // Filtrar los corregimientos por provincia y distrito
                    $filtered_arr = array_filter($corregimientosList, function($corregimiento) use ($iBSNFEProvincia, $iBSNFEDistrito) {
                        return $corregimiento['IBSNFEProvincia'] == $iBSNFEProvincia && $corregimiento['IBSNFEDistrito'] == $iBSNFEDistrito;
                    });
        
                    return $filtered_arr;
                } else {
                    // Manejar el caso en que la decodificación del JSON falle
                    return array();
                }
            } else {
                // Manejar el caso en que el archivo no exista
                return array();
            }
        }
        

        function BSNFE_AllCorregimientos(){
            $url=  WP_PLUGIN_DIR ."/WooCommerce-PA/Files/Corregimientos.json";
            $corregimientosContents = file_get_contents( $url,true);
            $corregimientosList = json_decode($corregimientosContents, true);
            return $corregimientosList;
        }


        function BSNFE_ObtenerCorregimiento($iBSNFECorregimiento,$iBSNFEProvincia){
            $url=  WP_PLUGIN_DIR ."/WooCommerce-PA/Files/Corregimientos.json";
            $corregimientosContents = file_get_contents( $url,true);
            $corregimientosList = json_decode($corregimientosContents, true);
            
            foreach ($corregimientosList as $corregimiento) {

                
                if ($corregimiento['IBSNFECorregimientos'] === (int)$iBSNFECorregimiento && $corregimiento['IBSNFEProvincia'] === (int)$iBSNFEProvincia){
                    return $corregimiento;
                } 
            }
            return;
        }
        function BSNFE_ObtenerProvincia($id){
            $url=  WP_PLUGIN_DIR ."/WooCommerce-PA/Files/Provincias.json";
            $provinciasContents = file_get_contents( $url,true);
            $provinciasList = json_decode($provinciasContents, true);
            
            foreach ($provinciasList as $provincia) {
                if ($provincia['IBSNFEProvincia'] === $id){
                    return $provincia;
                } 
            }
            return;
        }
        function BSNFE_ObtenerDistrito($iBSNFEDistrito,$iBSNFEProvincia){
            $url=  WP_PLUGIN_DIR ."/WooCommerce-PA/Files/Distritos.json";
            $distritosContents = file_get_contents( $url,true);
            $distritosList = json_decode($distritosContents, true);
            
            foreach ($distritosList as $distrito) {
                if ($distrito['IBSNFEDistrito'] === $iBSNFEDistrito &&  $distrito['IBSNFEProvincia'] === $iBSNFEProvincia){
                    return $distrito;
                } 
            }
            return;
        }

        
    }


?>