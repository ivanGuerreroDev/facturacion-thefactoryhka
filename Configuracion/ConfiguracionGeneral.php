<?php
// Añadir etiquetas
add_filter('admin_init', 'BSNFE_Register_my_general_settings_fields');

function BSNFE_Register_my_general_settings_fields()
{
    register_setting('general', 'BSNFETitulo_Ajustes_GTI', 'esc_attr');
    add_settings_field('BSNFETitulo_Ajustes_GTI', '<h3> Ajustes FACTURA GTI</h3>', 'BSNFE_CrearTituloAjustes', 'general');

    register_setting('general', 'BSNFENumero_de_Cuenta', 'esc_attr');
    add_settings_field('BSNFENumero_de_Cuenta', '<label for="BSNFENumero_de_Cuenta">' . 'Numero de Cuenta' . '</label>', 'BSNFE_CrearInputNumeroCuenta', 'general');

    register_setting('general', 'BSNFEDecimales', 'esc_attr');
    add_settings_field('BSNFEDecimales', '<label for="BSNFEDecimales">' . 'Decimales' . '</label>', 'BSNFE_CrearInputDecimales', 'general');

    register_setting('general', 'BSNFESufijo', 'esc_attr');
    add_settings_field('BSNFESufijo', '<label for="BSNFESufijo">' . 'Sufijo de Exoneración' . '</label>', 'BSNFE_CrearInputSufijo', 'general');

    register_setting('general', 'BSNFECodigo_Actividad', 'esc_attr');
    add_settings_field('BSNFECodigo_Actividad', '<label for="BSNFECodigo_Actividad">' . 'Codigo de Actividad' . '</label>', 'BSNFE_CrearInputCodigoActividad', 'general');

    register_setting('general', 'BSNFEUsuario', 'esc_attr');
    add_settings_field('BSNFEUsuario', '<label for="BSNFEUsuario">' . 'Usuario' . '</label>', 'BSNFE_CrearInputUsuario', 'general');

    register_setting('general', 'BSNFEClave', 'esc_attr');
    add_settings_field('BSNFEClave', '<label for="BSNFEClave">' . 'Contraseña' . '</label>', 'BSNFE_CrearInputClave', 'general');
}

// Añadir campos de entrada
function BSNFE_CrearTituloAjustes()
{
    echo '<h3> Ajustes FACTURA GTI</h3>';
}

function BSNFE_CrearInputNumeroCuenta()
{
    $Cuenta = get_option('BSNFENumero_de_Cuenta', '');
    echo '<input type="number" id="BSNFENumero_de_Cuenta" name="BSNFENumero_de_Cuenta" value="' . esc_attr($Cuenta) . '" />';
}

function BSNFE_CrearInputCodigoActividad()
{
    $Actividad = get_option('BSNFECodigo_Actividad', '');
    echo '<input type="number" id="BSNFECodigo_Actividad" name="BSNFECodigo_Actividad" value="' . esc_attr($Actividad) . '" />';
}

function BSNFE_CrearInputUsuario()
{
    $vlcUsurio = get_option('BSNFEUsuario', '');
    echo '<input type="text" id="BSNFEUsuario" name="BSNFEUsuario" value="' . esc_attr($vlcUsurio) . '" />';
}

function BSNFE_CrearInputDecimales()
{
    $vlcDecimales = get_option('BSNFEDecimales', '');
    echo '<input type="number" id="BSNFEDecimales" name="BSNFEDecimales" oninput="if (this.value > 5) {
        this.value = 2; 
    }" min="1" max="5" value="' . esc_attr($vlcDecimales) . '" />';
}

function BSNFE_CrearInputClave()
{
    $vlcContrasenia = get_option('BSNFEClave', '');
    echo '<input type="password" id="BSNFEClave" name="BSNFEClave" value="' . esc_attr($vlcContrasenia) . '" />';
}

function BSNFE_CrearInputSufijo()
{
    $vlcSufijo = get_option('BSNFESufijo', '');
    echo '<input type="text" id="BSNFESufijo" name="BSNFESufijo" value="' . esc_attr($vlcSufijo) . '" />';
}
?>
