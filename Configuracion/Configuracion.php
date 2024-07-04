<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Activar la página de Factura Electrónica
function BSNFE_activate_page()
{
}

// Agregar elemento al menú
add_action('admin_menu', 'BSNFE_AddMenuAdministrator');
function BSNFE_AddMenuAdministrator()
{
    add_menu_page('Factura Electrónica', 'Factura Electrónica', 'manage_options', 'BSNFE_Factura', 'BSNFE_admin_page_html', 'dashicons-analytics', 5);
}

function BSNFE_admin_page_html()
{
    // Verificar capacidades del usuario
    if (!current_user_can('manage_options')) {
        return;
    }

    // Obtener la pestaña activa del parámetro $_GET
    $default_tab = null;
    $tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : $default_tab;

    // Agregar nonce al formulario de la página de administración
    function BSNFE_add_nonce_to_admin_page()
    {
        ?>
        <input type="hidden" name="bsnfe_admin_nonce" value="<?php echo esc_attr(wp_create_nonce('bsnfe_admin_nonce')); ?>" />
        <?php
    }
    add_action('admin_init', 'BSNFE_add_nonce_to_admin_page');

    // Verificar nonce antes de procesar los datos del formulario
    function BSNFE_process_admin_form() {
        // Verificar si se ha enviado el nonce en el formulario
        if (isset($_POST['bsnfe_admin_nonce'])) {
            // Sanear y desinfectar el valor del nonce
            $nonce = sanitize_text_field(wp_unslash($_POST['bsnfe_admin_nonce']));

            // Verificar la validez del nonce
            if (wp_verify_nonce($nonce, 'bsnfe_admin_nonce')) {
                // Procesar los datos del formulario aquí
            } else {
                // El nonce no es válido, mostrar un mensaje de error o tomar alguna acción
                // Por ejemplo:
                wp_die('Nonce no válido');
            }
        } else {
            // El nonce no se ha enviado, mostrar un mensaje de error o tomar alguna acción
            // Por ejemplo:
            wp_die('Nonce no encontrado en la solicitud');
        }
    }

    add_action('admin_post', 'BSNFE_process_admin_form');
    ?>
    <!-- Nuestro contenido de página de administración debe estar dentro de .wrap -->
    <div class="wrap">
        <nav class="nav-tab-wrapper BSNFE_Factura_tabs">
            <a href="?page=<?php echo esc_attr('BSNFE_Factura'); ?>"
                class="nav-tab <?php if ($tab === null) : ?>nav-tab-active<?php endif; ?>">Configuración
                General</a>
            <a href="?page=<?php echo esc_attr('BSNFE_Factura'); ?>&tab=<?php echo esc_attr('Emisor'); ?>"
                class="nav-tab <?php if ($tab === 'Emisor') : ?>nav-tab-active<?php endif; ?>">Configuración
                Emisor</a>
            <a href="?page=<?php echo esc_attr('BSNFE_Factura'); ?>&tab=<?php echo esc_attr('Exportar'); ?>"
                class="nav-tab <?php if ($tab === 'Exportar') : ?>nav-tab-active<?php endif; ?>">Exportar
                Configuración</a>
            <a href="?page=<?php echo esc_attr('BSNFE_Factura'); ?>&tab=<?php echo esc_attr('Importar'); ?>"
                class="nav-tab <?php if ($tab === 'Importar') : ?>nav-tab-active<?php endif; ?>">Importar
                Configuración</a>
        </nav>

        <div class="tab-content">
            <?php
            switch ($tab) :
                case 'Emisor':
                    include(plugin_dir_path(__FILE__) . '/Tabs/TabEmisor.php');
                    break;
                case 'Importar':
                    include(plugin_dir_path(__FILE__) . '/Tabs/ImportData.php');
                    break;
                case 'Exportar':
                    include(plugin_dir_path(__FILE__) . '/Tabs/ExportData.php');
                    break;
                default:
                    include(plugin_dir_path(__FILE__) . '/Tabs/TabConfiguracion.php');
                    break;
            endswitch;
            ?>
        </div>
    </div>
<?php
}

