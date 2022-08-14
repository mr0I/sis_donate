jQuery(document).ready(function($) {

    const payPingDonate_Amount_Input = document.getElementById("payPingDonate_Amount_Input");
    const payPingDonate_Name_Input = document.getElementById("payPingDonate_Name_Input");

    (payPingDonate_Amount_Input != null)? payPingDonate_Amount_Input.defaultValue="10000" : '';

    $('#add_donate_frm').on('change' , function () {
        let name_val = payPingDonate_Name_Input.value;
        let select_val = $('#payPingDonate_Amount_Select').val();

        if (name_val !== '' && name_val !== null && select_val !== '0') {
            $('.payPingDonate_Submit').attr('disabled' , false);
        } else {
            $('.payPingDonate_Submit').attr('disabled' , true);
        }
    });

    $('#payPingDonate_Amount_Select').on('change' , function () {
        let val = $(this).val();
        if (val === 'others'){
            $(payPingDonate_Amount_Input).css('display' , 'block');
        } else {
            $(payPingDonate_Amount_Input).css('display' , 'none');
            $('#payPingDonate_Amount_Input').val(Number(val));
        }
    });

    // Separate Digits
    $.fn.digits = function () {
        return this.each(function () {
            $(this).text($(this).text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
        })
    };
    $('.digits').digits();

});