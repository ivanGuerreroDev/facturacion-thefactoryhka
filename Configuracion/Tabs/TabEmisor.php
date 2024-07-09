<?php

namespace BSNFE;

if (!current_user_can('manage_options'))
    wp_die('No tienes suficientes permisos para acceder a esta página.');

add_filter('admin_init', 'BSNFE_Register_my_general_settings_fields');

$ubicacion = new \BSNFEGetUbicacion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar el nonce
    if (!isset($_POST['nonce_guardar_ajustes_emisor']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce_guardar_ajustes_emisor'])), 'guardar_ajustes_emisor_factura_electronica')) {
        // El nonce no es válido
        wp_die('Nonce no válido');
    }

    // Saneado y validación de telefonos y correos electrónicos
    $telefonos = isset($_POST['BSNFETfnEm']) ? json_decode(wp_unslash($_POST['BSNFETfnEm']), true) : array();
    $emails = isset($_POST['BSNFECorElectEmi']) ? json_decode(wp_unslash($_POST['BSNFECorElectEmi']), true) : array();

    $telList = array();
    $emailList = array();

    foreach ($telefonos as $telefono) {
        // Sanear el número de teléfono eliminando cualquier carácter que no sea un dígito
        $telClean = preg_replace('/[^0-9]/', '', $telefono['value']);
        // Añadir un guión en la posición 4 del número de teléfono
        $tel = substr_replace($telClean, "-", 4, 0);
        // Escapar y sanear los números de teléfono
        $tel = sanitize_text_field(wp_unslash($tel));
        // Validar si el teléfono es válido antes de agregarlo a la lista
        if (!empty($tel)) {
            $telList[] = $tel;
        } else {
            $message = 'Es un formato de teléfono inválido: ' . $telefono['value'];
            BSNFE_ImprimirMensajeConfiguracion($message, true);
        }
    }

    foreach ($emails as $email) {
        // Sanear el correo electrónico
        $emailValue = sanitize_email(wp_unslash($email['value']));
        // Validar si el correo electrónico es válido antes de agregarlo a la lista
        if (!empty($emailValue) && is_email($emailValue)) {
            $emailList[] = $emailValue;
        } else {
            $message = 'Es un formato de email inválido: ' . $email['value'];
            BSNFE_ImprimirMensajeConfiguracion($message, true);
        }
    }

    // Escapar y sanear los correos electrónicos
    $emailList = implode(',', array_map('sanitize_email', array_map('trim', $emailList)));

    // Actualizar opciones con saneado y validación
    update_option("BSNFETipoRuc", sanitize_text_field(wp_unslash($_POST['BSNFETipoRuc'])));
    update_option("BSNFERuc", sanitize_text_field(wp_unslash($_POST['BSNFERuc'])));
    update_option("BSNFEDV", sanitize_text_field(wp_unslash($_POST['BSNFEDV'])));
    update_option("BSNFENombEm", sanitize_text_field(wp_unslash($_POST['BSNFENombEm'])));
    update_option("BSNFEBSNFECoordEmLat", sanitize_text_field(wp_unslash($_POST['BSNFEBSNFECoordEmLat'])));
    update_option("BSNFECoordEmLong", sanitize_text_field(wp_unslash($_POST['BSNFECoordEmLong'])));
    update_option("BSNFEDirecEm", sanitize_text_field(wp_unslash($_POST['BSNFEDirecEm'])));
    update_option("BSNFECorreg", sanitize_text_field(wp_unslash($_POST['BSNFECorreg'])));
    update_option("BSNFEDistr", sanitize_text_field(wp_unslash($_POST['BSNFEDistr'])));
    update_option("BSNFEProv", sanitize_text_field(wp_unslash($_POST['BSNFEProv'])));
    update_option("BSNFETfnEm", implode(',', array_map('trim', $telList)));
    update_option("BSNFECorElectEmi", $emailList);

    BSNFE_ImprimirMensajeConfiguracion("Datos actualizados correctamente");
}




function BSNFE_Register_my_general_settings_fields()
{

    register_setting('general', 'BSNFETipoRuc', 'sanitize_text_field');
    register_setting('general', 'BSNFERuc', 'sanitize_text_field');
    register_setting('general', 'BSNFEDV', 'sanitize_text_field');
    register_setting('general', 'BSNFENombEm', 'sanitize_text_field');
    register_setting('general', 'BSNFEBSNFECoordEmLat', 'sanitize_text_field');
    register_setting('general', 'BSNFECoordEmLong', 'sanitize_text_field');
    register_setting('general', 'BSNFEDirecEm', 'sanitize_text_field');
    register_setting('general', 'BSNFECorreg', 'sanitize_text_field');
    register_setting('general', 'BSNFEDistr', 'sanitize_text_field');
    register_setting('general', 'BSNFEProv', 'sanitize_text_field');
    register_setting('general', 'BSNFETfnEm');
    register_setting('general', 'BSNFECorElectEmi');
}

//Function to show notifications on screen
function BSNFE_ImprimirMensajeConfiguracion($message, $error = false)
{
    $vlcClaseError = "success";
    $vlcBackgroundError = "#fff";
    if (is_wp_error($message)) {
        if ($message->get_error_data() && is_string($message->get_error_data())) {
            $message = $message->get_error_message() . ': ' . $message->get_error_data();
            $vlcClaseError = "error";
            $vlcBackgroundError = "#fff";
        } else {
            $message = $message->get_error_message();
            $vlcClaseError = "error";
            $vlcBackgroundError = "#fff";
        }
    }

    if ($error) {
        $vlcClaseError = "error";
        $vlcBackgroundError = "#fff";
    }
    ?>
    <div class="notice <?php echo esc_attr($vlcClaseError) ?> my-acf-notice is-dismissible"
        Style='background:<?php echo esc_attr($vlcBackgroundError) ?>;'>
        <p>
            <?php echo esc_html("{$message}"); ?>
        </p>
    </div>
    <?php
}
add_action('admin_notices', 'BSNFE_ImprimirMensajeConfiguracion');

?>


<div class="postarea wp-editor-expand postbox" style="margin-top:15px">
<h4 class="postbox-header" style="margin: 0;padding: 15px;" ><label> Ajustes Emisor Factura Electrónica</label></h4>
    <div style="padding:15px;">
    <form method="post">
    <?php wp_nonce_field('guardar_ajustes_emisor_factura_electronica', 'nonce_guardar_ajustes_emisor'); ?>


<div style="margin-top: 20px">

    <p>En esta ventana podrá realizar la configuración del emisor.</p>

    <b>Consideraciones</b>
    <p>1 - Deberá rellenar correctamente toda la información requerida para evitar errores en el funcionamiento
        normal del plugin.</p>

    <table>
        <tbody>
            <tr>
                <td><label>Tipo de RUC</label></td>
                <td>
                    <?php
                    echo '<select required  id="BSNFETipoRuc" name="BSNFETipoRuc">';
                    echo '<option value="1" ' . selected(1, get_option('BSNFETipoRuc'), false) . '>Natural</option>';
                    echo '<option value="2" ' . selected(2, get_option('BSNFETipoRuc'), false) . '>Jurídico</option>';
                    echo '</select>';
                    ?>
                </td>
            </tr>
            <tr>
                <td><label>RUC</label></td>
                <td>
                    <?php
                    $BSNFERuc = get_option('BSNFERuc', '');
                    echo '<input type="text" required  id="BSNFERuc" name="BSNFERuc" value="' . esc_attr($BSNFERuc) . '" />';
                    ?>
                </td>
            </tr>
            <tr>
                <td><label>Dígito verificador</label></td>
                <td>
                    <?php
                    $BSNFEDV = get_option('BSNFEDV', '');
                    echo '<input type="text" required  id="BSNFEDV" name="BSNFEDV" value="' . esc_attr($BSNFEDV) . '" />';
                    ?>
                </td>
            </tr>
            <tr>
                <td><label>Nombre Emisor</label></td>
                <td>
                    <?php
                    $BSNFENombEm = get_option('BSNFENombEm', '');
                    echo '<input type="text" required  id="BSNFENombEm" name="BSNFENombEm" value="' . esc_attr($BSNFENombEm) . '" />';
                    ?>
                </td>
            </tr>

            <tr>
                <td><label>Coordenadas Emisor (Latitud)</label></td>
                <td>
                    <?php
                    $BSNFEBSNFECoordEmLat = get_option('BSNFEBSNFECoordEmLat', '');
                    echo '<input type="text" required  id="BSNFEBSNFECoordEmLat" name="BSNFEBSNFECoordEmLat" value="' . esc_attr($BSNFEBSNFECoordEmLat) . '" />';
                    ?>
                </td>
            </tr>
            <tr>
                <td><label>Coordenadas Emisor (Longitud)</label></td>
                <td>
                    <?php
                    $BSNFECoordEmLong = get_option('BSNFECoordEmLong', '');
                    echo '<input type="text" required  id="BSNFECoordEmLong" name="BSNFECoordEmLong" value="' . esc_attr($BSNFECoordEmLong) . '" />';
                    ?>
                </td>
            </tr>
            <tr>
                <td><label>Dirección Emisor</label></td>
                <td>
                    <?php
                    $BSNFEDirecEm = get_option('BSNFEDirecEm', '');
                    echo '<input type="text" required  id="BSNFEDirecEm" name="BSNFEDirecEm" value="' . esc_attr($BSNFEDirecEm) . '" />';
                    ?>
                </td>
            </tr>



            <tr>
                <td><label>Provincia</label></td>
                <td>
                    <?php
                    $BSNFEProv = get_option('BSNFEProv', '');
                    echo '<select style="width:100%" id="BSNFEProv" name="BSNFEProv"></select>';


                    ?>
            </tr>
            <tr>
                <td><label>Distrito</label></td>
                <td>
                    <?php
                    $BSNFEDistr = get_option('BSNFEDistr', '');
                    echo '<select style="width:100%" id="BSNFEDistr" name="BSNFEDistr"></select>';
                    ?>
                </td>
            </tr>

            <tr>
                <td><label>Corregimiento</label></td>
                <td>
                    <?php
                    $BSNFECorreg = get_option('BSNFECorreg', '');
                    echo '<select style="width:100%" id="BSNFECorreg" name="BSNFECorreg"></select>';
                    ?>
                </td>
            </tr>

            <tr>
                <td><label>Teléfonos Emisor</label></td>
                <td>
                    <?php
                    $BSNFETfnEm = get_option('BSNFETfnEm', '');
                    echo '<input class="tags" type="text" required  id="BSNFETfnEm" name="BSNFETfnEm" value="' . esc_attr($BSNFETfnEm) . '" />';
                    ?>
                </td>
            </tr>
            <tr>
                <td><label>Correos Electrónicos Emisor</label></td>
                <td>
                    <?php
                    $BSNFECorElectEmi = get_option('BSNFECorElectEmi', '');
                    echo '<input class="tags" type="text" required  id="BSNFECorElectEmi" name="BSNFECorElectEmi" value="' . esc_attr($BSNFECorElectEmi) . '" />';
                    ?>
                </td>
            </tr>
        </tbody>
    </table>




    <input style="margin-top:20px" type="submit" value="Guardar Configuración" name="submit" class="button-primary">

</form>
    </div>
</div>


<?php
$BSNFEProv =strlen($BSNFEProv) == 0 ? 0 : $BSNFEProv;
$BSNFEDistr =strlen($BSNFEDistr) == 0 ? 0 : $BSNFEDistr;

?>

<script>

    jQuery(document).ready(function ($) {

        jQuery('#BSNFEDistr').select2();
        jQuery('#BSNFECorreg').select2();
        var corregimientos;
        $.ajax(
            {
                url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
                dataType: 'json',
                data: {
                    'action': 'BSNFE_provincias_get',
                },
                success: function (data) {
                    provincias = data;
                    let options = jQuery.map(provincias, function (item) {
                        return {
                            id: item.IBSNFEProvincia,
                            text: item.Descripcion
                        };
                    })
                    $('#BSNFEProv').select2({
                        data: options,
                    });

                  
                    // Escapar y validar valores PHP antes de insertarlos en el script
                    var BSNFEProv = '<?php echo esc_js($BSNFEProv); ?>';
                    var BSNFEDistr = '<?php echo esc_js($BSNFEDistr); ?>';

                    $('#BSNFEProv').val(BSNFEProv).trigger('change');
                    BSNFE_getDistritos(BSNFEProv);
                    BSNFE_getCorregimientos(BSNFEProv, BSNFEDistr);
              
                },
                error: function (error) {
                    console.log(error)
                }
            }
        )

        $('#BSNFEProv').on('select2:select', function (e) {
            let data = e.params.data;
            BSNFE_getDistritos(data.id);
            let dataDis = $('#BSNFEProv').select2('data');
            let idDis = dataDis[0].id;
            BSNFE_getCorregimientos(data.id, idDis);
        });
        $('#BSNFEDistr').on('select2:select', function (e) {
            let data = e.params.data;
            let dataProv = $('#BSNFEProv').select2('data');
            let iBSNFEProvincia = dataProv[0].id;
            BSNFE_getCorregimientos(iBSNFEProvincia, data.id);
        });

    });

    function BSNFE_getDistritos(provincia) {
    jQuery.ajax(
        {
            url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
            dataType: 'json',
            data: {
                'action': 'BSNFE_distritos_get',
                'provincia': provincia,
            },
            success: function (data) {
                distritos = data;
                let options = jQuery.map(distritos, function (item) {
                    return {
                        id: item.IBSNFEDistrito,
                        text: item.Descripcion
                    };
                })
                jQuery('#BSNFEDistr').html('').select2({
                    data: options
                });

                jQuery('#BSNFEDistr').val('<?php echo esc_attr($BSNFEDistr); ?>');
                jQuery('#BSNFEDistr').trigger('change');
            },
            error: function (error) {
                console.log(error)
            }
        }
    )
}



    function BSNFE_getCorregimientos(provincia, distrito) {
    jQuery.ajax({
        url: '<?php echo esc_url(admin_url("admin-ajax.php")); ?>',
        dataType: 'json',
        data: {
            'action': 'BSNFE_corregimiento_get',
            'provincia': provincia,
            'distrito': distrito,
        },
        success: function (data) {
            corregimientos = data;
            console.log(corregimientos);
            let options = jQuery.map(corregimientos, function (item) {
                return {
                    id: item.IBSNFECorregimientos,
                    text: item.Descripcion
                };
            });
            jQuery('#BSNFECorreg').html('').select2({
                data: options
            });

            // Escapar el valor de $BSNFECorreg antes de asignarlo
            jQuery('#BSNFECorreg').val('<?php echo esc_attr($BSNFECorreg); ?>');
            jQuery('#BSNFECorreg').trigger('change');
        },
        error: function (error) {
            console.log(error);
        }
    });
}

</script>
