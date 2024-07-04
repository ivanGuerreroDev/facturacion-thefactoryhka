<?php
namespace BSNFE;

if (!current_user_can('manage_options')) {
    wp_die('No tienes suficientes permisos para acceder a esta página.');
}

add_filter('admin_init', 'BSNFE_Register_my_general_settings_fields');

$download = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar el nonce
    if (!isset($_POST['nonce_exportar_ajustes']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce_exportar_ajustes'])), 'exportar_ajustes_factura_electronica')) {
        // El nonce no es válido
        wp_die('Nonce no válido');
    }

    $base = WP_PLUGIN_DIR;
    $data = array(
        'BSNFEPunto_Facturacion' => sanitize_text_field(get_option('BSNFEPunto_Facturacion')),
        'BSNFEDecimales' => sanitize_text_field(get_option('BSNFEDecimales')),
        'BSNFEUsuario' => sanitize_text_field(get_option('BSNFEUsuario')),
        'BSNFEClave' => sanitize_text_field(get_option('BSNFEClave')),
        'BSNFEambiente' => sanitize_text_field(get_option('BSNFEambiente')),
        'BSNFETipoRuc' => sanitize_text_field(get_option('BSNFETipoRuc')),
        'BSNFERuc' => sanitize_text_field(get_option('BSNFERuc')),
        'BSNFEDV' => sanitize_text_field(get_option('BSNFEDV')),
        'BSNFENombEm' => sanitize_text_field(get_option('BSNFENombEm')),
        'BSNFEBSNFECoordEmLat' => sanitize_text_field(get_option('BSNFEBSNFECoordEmLat')),
        'BSNFECoordEmLong' => sanitize_text_field(get_option('BSNFECoordEmLong')),
        'BSNFEDirecEm' => sanitize_text_field(get_option('BSNFEDirecEm')),
        'BSNFECorreg' => sanitize_text_field(get_option('BSNFECorreg')),
        'BSNFEDistr' => sanitize_text_field(get_option('BSNFEDistr')),
        'BSNFEProv' => sanitize_text_field(get_option('BSNFEProv')),
        'BSNFETfnEm' => sanitize_text_field(get_option('BSNFETfnEm')),
        'BSNFECorElectEmi' => sanitize_text_field(get_option('BSNFECorElectEmi'))
    );
    $jsonString  = json_encode($data);
    $rutaArchivo = $base . '/WooCommerce-PA/respaldo.json';
    $nombre = 'respaldo.json';
    file_put_contents($rutaArchivo, $jsonString);

    $download = true;
}

function BSNFE_Register_my_general_settings_fields()
{
    register_setting('general', 'BSNFETitulo_Ajustes_GTI', 'esc_attr');

    register_setting('general', 'BSNFEPunto_Facturacion', 'esc_attr');
    register_setting('general', 'BSNFEDecimales', 'esc_attr');

    register_setting('general', 'BSNFEUsuario', 'esc_attr');

    register_setting('general', 'BSNFEClave', 'esc_attr');

    register_setting('general', 'BSNFEambiente', 'esc_attr');
}

//Function to show notifications on screen
function BSNFE_ImprimirMensajeConfiguracion($message)
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
    ?>
    <div class="notice <?php echo esc_attr($vlcClaseError); ?> my-acf-notice is-dismissible" style="background:<?php echo esc_attr($vlcBackgroundError); ?>;">
        <p><?php echo esc_html($message); ?></p>
    </div>
<?php
}

add_action('admin_notices', 'BSNFE_ImprimirMensajeConfiguracion');

?>
<div class="postarea wp-editor-expand postbox" style="margin-top:15px">
    <h4 class="postbox-header" style="margin: 0;padding: 15px;"><label><?php esc_html_e('Exportar Ajustes', 'factura-electronica'); ?></label></h4>
    <div style="padding:15px;">
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('exportar_ajustes_factura_electronica', 'nonce_exportar_ajustes'); ?>
            <p><?php esc_html_e('La Configuración se exportará en formato json.', 'factura-electronica'); ?></p>
            <?php if (!$download) { ?>
                <input style="margin-top:20px" type="submit" value="<?php esc_attr_e('Generar archivo de Configuración', 'factura-electronica'); ?>" name="submit" class="button-secondary">
            <?php } ?>
        </form>
        <?php if ($download) { ?>
            <a href="<?php echo esc_url(get_site_url() . '/wp-content/plugins/WooCommerce-PA/respaldo.json'); ?>" download="respaldo.json" style="margin-top:20px" class="button-primary"><?php esc_html_e('Descargar Archivo', 'factura-electronica'); ?></a>
        <?php } ?>
    </div>
</div>

