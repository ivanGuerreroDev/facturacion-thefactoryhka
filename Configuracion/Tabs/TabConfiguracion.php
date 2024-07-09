<?php
namespace BSNFE;

if (!current_user_can('manage_options')) {
    wp_die('No tienes suficientes permisos para acceder a esta página.');
}

add_filter('admin_init', 'BSNFE_Register_my_general_settings_fields');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar el nonce
    if (!isset($_POST['nonce_guardar_ajustes']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce_guardar_ajustes'])), 'guardar_ajustes_factura_electronica')) {
        // El nonce no es válido
        wp_die('Nonce no válido');
    }

    // Saneado y validación
    $BSNFEambiente = isset($_POST['BSNFEambiente']) ? sanitize_text_field(wp_unslash($_POST['BSNFEambiente'])) : ''; // Sanitizamos el campo "BSNFEambiente"
    $puntoFacturacion = isset($_POST['BSNFEPunto_Facturacion']) ? sanitize_text_field(str_pad(wp_unslash($_POST['BSNFEPunto_Facturacion']), 3, "0", STR_PAD_LEFT)) : ''; // Sanitizamos el número de punto de facturación
    $BSNFEToken = isset($_POST['BSNFEToken']) ? sanitize_text_field(wp_unslash($_POST['BSNFEToken'])) : ''; // Sanitizamos el nombre de usuario
    $BSNFEClave = isset($_POST['BSNFEClave']) ? sanitize_text_field(wp_unslash($_POST['BSNFEClave'])) : ''; // Sanitizamos la clave de usuario

    // Validar ambiente
    $BSNFEambiente_valido = ($BSNFEambiente === 'on' || $BSNFEambiente === 'off');

    // Validar punto de facturación como un número de tres dígitos
    $puntoFacturacion_valido = preg_match('/^\d{3}$/', $puntoFacturacion);

    // Validar nombre de usuario
    $BSNFEToken_valido = !empty($BSNFEToken);

    // Validar clave de usuario
    $BSNFEClave_valido = !empty($BSNFEClave);

    // Si todos los datos son válidos, actualizamos las opciones
    if ($BSNFEambiente_valido && $puntoFacturacion_valido && $BSNFEToken_valido && $BSNFEClave_valido) {
        // Actualizamos las opciones
        update_option("BSNFEambiente", $BSNFEambiente); // No es necesario validar ya que solo puede ser 'on' o 'off'
        update_option("BSNFEPunto_Facturacion", $puntoFacturacion);
        update_option("BSNFEToken", $BSNFEToken);
        update_option("BSNFEClave", $BSNFEClave);

        BSNFE_ImprimirMensajeConfiguracion("Datos actualizados correctamente");
    } else {
        // Si algún dato no es válido, mostramos un mensaje de error
        BSNFE_ImprimirMensajeConfiguracion("Los datos ingresados no son válidos");
    }
}

function BSNFE_Register_my_general_settings_fields()
{
    register_setting('general', 'BSNFETitulo_Ajustes_GTI', 'esc_attr');
    register_setting('general', 'BSNFEPunto_Facturacion', 'esc_attr');
    //register_setting('general', 'BSNFEDecimales', 'esc_attr');
    register_setting('general', 'BSNFEToken', 'esc_attr');
    register_setting('general', 'BSNFEClave', 'esc_attr');
    register_setting('general', 'BSNFEambiente', 'esc_attr');
}

// Function to show notifications on screen
function BSNFE_ImprimirMensajeConfiguracion($message)
{
    $vlcClaseError = "success";
    $vlcBackgroundError = "#fff";

    // Escapar el mensaje
    $escaped_message = esc_html($message);

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
    ?>
    <div class="notice <?php echo esc_attr($vlcClaseError); ?> my-acf-notice is-dismissible" style="background:<?php echo esc_attr($vlcBackgroundError); ?>;">
        <p><?php echo esc_html($escaped_message); ?></p>
    </div>
    <?php
}


add_action('admin_notices', 'BSNFE_ImprimirMensajeConfiguracion');
?>

<div class="postarea wp-editor-expand postbox" style="margin-top:15px">
    <h4 class="postbox-header" style="margin: 0;padding: 15px;"><label>Ajustes Factura Electrónica</label></h4>
    <div style="padding:15px;">
        <form method="post">
            <?php wp_nonce_field('guardar_ajustes_factura_electronica', 'nonce_guardar_ajustes'); ?>
            <div style="margin-top: 20px">
                <p>En esta ventana podrás realizar la configuración general del plugin.</p>
                <b>Consideraciones</b>
                <p>1 - Deberás rellenar correctamente toda la información requerida para evitar errores en el funcionamiento normal del plugin.</p>
                <table>
                    <tbody>
                        <tr>
                            <td><label>Punto de facturación</label></td>
                            <td>
                                <?php
                                $Punto = get_option('BSNFEPunto_Facturacion', '');
                                echo '<input type="number" required id="BSNFEPunto_Facturacion" name="BSNFEPunto_Facturacion" value="' . esc_attr($Punto) . '" />';
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Token</label></td>
                            <td>
                                <?php
                                $vlcUsurio = get_option('BSNFEToken', '');
                                echo '<input type="text" required id="BSNFEToken" name="BSNFEToken" value="' . esc_attr($vlcUsurio) . '" />';
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Contraseña</label></td>
                            <td>
                                <?php
                                $vlcContrasenia = get_option('BSNFEClave', '');
                                echo '<input type="password" required id="BSNFEClave" name="BSNFEClave" value="' . esc_attr($vlcContrasenia) . '" />';
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div style="margin-top:20px;">
                    <b>Ambiente de pruebas</b>
                    <p>1 - <b>Esta opción solo deberá estar activa cuando se realicen pruebas del plugin</b>, ya que todas las facturas serán emitidas a un ambiente de pruebas.</p>
                </div>
                <table>
                    <tbody>
                        <tr>
                            <td><label>Ambiente de pruebas&nbsp;&nbsp;&nbsp;</label></td>
                            <td>
                                <?php
                                $vlcAmbiente = get_option('BSNFEambiente', '');

                                if ($vlcAmbiente == "on") {
                                    echo '<input type="checkbox" id="BSNFEambiente" checked name="BSNFEambiente"/>';
                                } else {
                                    echo '<input type="checkbox" id="BSNFEambiente" name="BSNFEambiente"/>';
                                }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <input style="margin-top:20px" type="submit" value="Guardar Configuración" name="submit" class="button-primary">
            </div>
        </form>
    </div>
</div>
