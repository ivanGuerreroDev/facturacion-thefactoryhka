<?php
namespace BSNFE;

if (!current_user_can('manage_options')) {
    wp_die('No tienes suficientes permisos para acceder a esta página.');
}

add_filter('admin_init', 'BSNFE_Register_my_general_settings_fields');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar el nonce
    if (!isset($_POST['nonce_importar_ajustes']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce_importar_ajustes'])), 'importar_ajustes_factura_electronica')) {
        // El nonce no es válido
        wp_die('Nonce no válido');
    }

    // Verificar si se ha enviado un archivo y si se ha subido correctamente
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        // Obtener el contenido del archivo JSON y sanitizarlo
        $jsonString = file_get_contents($_FILES['archivo']['tmp_name']);
        $jsonString = wp_kses_post($jsonString);

        // Decodificar el JSON en un array asociativo
        $configData = json_decode($jsonString, true);

        // Validar que $configData sea un array
        if (is_array($configData) && isset($configData['BSNFEPunto_Facturacion'])) {
            // Saneado y actualización de opciones
            // Sanitizar y escapar los datos antes de actualizar las opciones uno por uno
            update_option("BSNFEPunto_Facturacion", esc_html(sanitize_text_field($configData['BSNFEPunto_Facturacion'])));
            update_option("BSNFEDecimales", esc_html(sanitize_text_field($configData['BSNFEDecimales'])));
            update_option("BSNFEUsuario", esc_html(sanitize_text_field($configData['BSNFEUsuario'])));
            update_option("BSNFEClave", esc_html(sanitize_text_field($configData['BSNFEClave'])));
            update_option("BSNFETipoRuc", esc_html(sanitize_text_field($configData['BSNFETipoRuc'])));
            update_option("BSNFERuc", esc_html(sanitize_text_field($configData['BSNFERuc'])));
            update_option("BSNFEDV", esc_html(sanitize_text_field($configData['BSNFEDV'])));
            update_option("BSNFENombEm", esc_html(sanitize_text_field($configData['BSNFENombEm'])));
            update_option("BSNFEBSNFECoordEmLat", esc_html(sanitize_text_field($configData['BSNFEBSNFECoordEmLat'])));
            update_option("BSNFECoordEmLong", esc_html(sanitize_text_field($configData['BSNFECoordEmLong'])));
            update_option("BSNFEDirecEm", esc_html(sanitize_text_field($configData['BSNFEDirecEm'])));
            update_option("BSNFECorreg", esc_html(sanitize_text_field($configData['BSNFECorreg'])));
            update_option("BSNFEDistr", esc_html(sanitize_text_field($configData['BSNFEDistr'])));
            update_option("BSNFEProv", esc_html(sanitize_text_field($configData['BSNFEProv'])));
            update_option("BSNFETfnEm", esc_html(sanitize_text_field($configData['BSNFETfnEm'])));
            update_option("BSNFECorElectEmi", esc_html(sanitize_text_field($configData['BSNFECorElectEmi'])));

            BSNFE_ImprimirMensajeConfiguracion("Datos importados correctamente");
        } else {
            BSNFE_ImprimirMensajeConfiguracion("El archivo proporcionado no contiene la estructura esperada.", true);
        }
    }
}


function BSNFE_Register_my_general_settings_fields()
{
    // Puedes registrar tus campos aquí si lo necesitas
}

function BSNFE_ImprimirMensajeConfiguracion($message, $error = false)
{
    $vlcClaseError = "success"; // Clase por defecto para éxito
    $vlcBackgroundError = "#fff"; // Color de fondo por defecto

    if (is_wp_error($message)) {
        if ($message->get_error_data() && is_string($message->get_error_data())) {
            $message = $message->get_error_message() . ': ' . $message->get_error_data();
            $vlcClaseError = "error"; // Si hay un error y datos, cambia la clase a error
        } else {
            $message = $message->get_error_message();
            $vlcClaseError = "error"; // Si hay un error sin datos, cambia la clase a error
        }
    }

    if ($error) {
        $vlcClaseError = "error"; // Si se especifica un error, cambia la clase a error
    }
    ?>
    <div class="notice <?php echo esc_attr($vlcClaseError); ?> my-acf-notice is-dismissible" style="background:<?php echo esc_attr($vlcBackgroundError); ?>;">
        <p><?php echo esc_html($message); ?></p>
    </div>
    <?php
}

add_action('admin_notices', 'BSNFE_ImprimirMensajeConfiguracion');
?>

<div class="postarea wp-editor-expand postbox" style="margin-top:15px">
    <h4 class="postbox-header" style="margin: 0;padding: 15px;"><label>Importar Ajustes de Factura Electrónica</label></h4>
    <div style="padding:15px;">
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('importar_ajustes_factura_electronica', 'nonce_importar_ajustes'); ?>
            <table>
                <tbody>
                    <tr>
                        <td><label>Archivo a importar</label></td>
                        <td>
                            <?php
                            echo '<input type="file" required id="archivo" name="archivo" accept="application/JSON"/>';
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input style="margin-top:20px" type="submit" value="Importar Configuración" name="submit" class="button-primary">
        </form>
    </div>
</div>
