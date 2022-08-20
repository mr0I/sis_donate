jQuery(document).ready(function($) {

    // init toast messages
    const toastAlert = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });
    const modalAlert = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    });


    // toastAlert.fire({
    //     icon: 'success',
    //     title: 'در صورتی که به صورت خودکار به درگاه بانک منتقل نشدید، اینجا کلیک کنید.',
    // });


    const sisoogDonate_Amount_Input = document.getElementById('sisoogDonate_Amount_Input');
    const sisoogDonate_Name_Input = document.getElementById('sisoogDonate_Name_Input');
    const sisoogDonate_Amount_Select = document.getElementById('sisoogDonate_Amount_Select');
    const add_donate_frm = document.getElementById('add_donate_frm');
    const sisoogDonate_Submit = $('.sisoogDonate_Submit');
    const sisoogDonate_SubmitText = $(sisoogDonate_Submit).html();
    sisoogDonate_Amount_Input ? sisoogDonate_Amount_Input.defaultValue='10000' : '';


    // add donate frm from
    $(add_donate_frm).on('change' , function () {
        const name_val = sisoogDonate_Name_Input.value;
        const select_val = sisoogDonate_Amount_Select.value;

        if (name_val !== '' && name_val !== null && select_val !== '0') $(sisoogDonate_Submit).attr('disabled', false);
        else $(sisoogDonate_Submit).attr('disabled', true);
    });

    $(sisoogDonate_Amount_Select).on('change' , function () {
        if ($(this).val() === 'other_prices') $(sisoogDonate_Amount_Input).fadeIn('fast');
        else $(sisoogDonate_Amount_Input).fadeOut('fast').val(Number($(this).val()));
    });

    $(add_donate_frm).on('submit' , function (e) {
        e.preventDefault();

        const name = document.forms['add_donate_frm']['name'].value;
        const email = document.forms['add_donate_frm']['email'].value;
        const mobile = document.forms['add_donate_frm']['mobile'].value;
        const desc = document.forms['add_donate_frm']['desc'].value;
        const amount = document.forms['add_donate_frm']['amount'].value !== 'other_prices'
            ? document.forms['add_donate_frm']['amount'].value
            : document.forms['add_donate_frm']['input_amount'].value ;
        const author_id = document.forms['add_donate_frm']['author_id'].value;
        const user_name = document.forms['add_donate_frm']['user_name'].value;
        const post_id = document.forms['add_donate_frm']['post_id'].value;
        const post_url = document.forms['add_donate_frm']['post_url'].value;
        const donate_data = document.forms['add_donate_frm']['donate_data'].value;

        const data = {
            action: 'addDonateFrm',
            security : SISOOGDONATEADMINAJAX.security,
            nonce: document.getElementById('donate_frm_nonce').value,
            'name': name,
            'email': email,
            'mobile': mobile,
            'desc': desc,
            'amount': amount,
            'author_id': author_id,
            'user_name': user_name,
            'post_id': post_id,
            'donate_data': donate_data,
        };
        $.ajax({
            url: SISOOGDONATEADMINAJAX.ajaxurl,
            type: 'POST',
            data: data,
            beforeSend: function () {
                sisoogDonate_Submit.html('<i class="dn-spinner2 fa-spin align-middle mx-1"></i>').attr('disabled', true);
            },
            success: function (res , xhr) {
                const response = JSON.parse(res);

                if (xhr === 'success' && response.success ){
                    let timerInterval;
                    modalAlert.fire({
                        icon: 'success',
                        html: '<span>به زودی به درگاه بانک منتقل میشوید، اگر منتقل نشدید <a href="#" class="text-danger">اینجا</a> کلیک کنید</span>',
                        showConfirmButton: false,
                        timerProgressBar: false,
                        timer: 15000,
                        allowOutsideClick :false,
                        didOpen: () => {modalAlert.showLoading();},
                        willClose: () => {clearInterval(timerInterval)}
                    }).then( () => {
                        console.log('redirect to gateway...');
                    });

                    setTimeout(function () {
                        window.location.href = response.redirect_url;
                    },1000)


                } else if(! response.success && response.status === '400') {
                    alert(response.error);
                    window.location.replace(post_url);
                } else {
                    alert(response.error);
                }
            },error:function (jqXHR, textStatus, errorThrown) {
                if(textStatus==='timeout') {
                    alert('Error');
                }
            }
            ,complete:function () {
                sisoogDonate_Submit.html(sisoogDonate_SubmitText).attr('disabled', false);
            },
            timeout:SISOOGDONATEADMINAJAX.REQUEST_TIMEOUT
        });
    });


    // Separate Digits
    $.fn.digits = function () {
        return this.each(function () {
            $(this).text($(this).text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'));
        })
    };
    $('.digits').digits();

});