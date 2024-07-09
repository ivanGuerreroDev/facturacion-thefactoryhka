
jQuery(document).ready(function($) {
    $('#BSNFECorreg2').select2();
    var tipoRuc =    $('#billing_options_tiporuc').val();     
        
    SetLabel(tipoRuc);
    $('#billing_options_typefac').val("");
    $('#billing_options_typefac option[value=""]').attr('selected', 'selected');
    $('#billing_options_itiporec').val("");
    $('#billing_options_dv').val("");
    $('#billing_options_ruc').val("");
    $('#billing_options_tiporuc').val("");
    $('#billing_options_razonsocial').val("");
    $('#BSNFECorreg2').val("");

    $('#billing_options_itiporec_field').hide();
    $('#billing_options_dv_field').hide();
    $('#billing_options_ruc_field').hide();
    $('#billing_options_tiporuc_field').hide();
    $('#billing_options_razonsocial_field').hide();
    $('#BSNFECorreg2_field').hide();
    $('#billing_options_typefac').change(function(){
        var tipo =   $(this).find("option:selected").attr('value');
        $('#billing_options_iTipoRec').val("");
        $('#billing_options_tiporuc').val("");
        $('#billing_options_ruc').val("");
        $('#billing_options_dv').val("");
        $('#billing_options_razonsocial').val("");
        $('#BSNFECorreg2').val("");
        if(tipo == "01"){
            $('#billing_options_razonsocial_field').show();
            $('#billing_options_ruc_field').show();
            $('#billing_options_tiporuc_field').show();
            $('#billing_options_itiporec_field').show();
            $('#billing_options_dv_field').show();
            $('#BSNFECorreg2_field').show();
        }
        else if (tipo == "02"){
            $('#billing_options_razonsocial_field').hide();
            $('#billing_options_ruc_field').hide();
            $('#billing_options_itiporec_field').hide();
            $('#billing_options_tiporuc_field').hide();
            $('#billing_options_dv_field').hide();
            $('#BSNFECorreg2_field').hide();

        }else{
            $('#billing_options_razonsocial_field').hide();
            $('#billing_options_ruc_field').hide();
            $('#billing_options_itiporec_field').hide();
            $('#billing_options_tiporuc_field').hide();
            $('#billing_options_dv_field').hide();
            $('#BSNFECorreg2_field').hide();
        }
    });
    $('#billing_options_itiporec').change(function(){
        var tipo =   $(this).find("option:selected").attr('value');     
        var tipoRuc;
        $('#billing_options_razonsocial').val("");
        $('#billing_options_dv').val("");
        if(tipo =="01" || tipo == "03"){
            tipoRuc = 2;
            $('#billing_options_razonsocial_field').show();
            $('#billing_options_dv_field').show();
            $('#BSNFECorreg2_field').show();
        }
        else if (tipo == "02" || tipo == "04"){
            tipoRuc = 1;
            $('#billing_options_razonsocial_field').hide();
            $('#billing_options_dv_field').hide();
            $('#BSNFECorreg2_field').hide();
        }
        SetLabel(tipoRuc);
    });
    
});

function SetLabel(tipo){
    if(tipo ==1){
        jQuery('#rucLabel').html('Número de cédula o pasaporte');
    }
    else if (tipo == 2){
        jQuery('#rucLabel').html('Número de RUC');
    }
}
