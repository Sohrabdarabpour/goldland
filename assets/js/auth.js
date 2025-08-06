// goldland/assets/js/auth.js
$(document).ready(function() {
    // اعتبارسنجی همزمان فرم
    $('#username').on('blur', function() {
        const username = $(this).val();
        if (username.length < 4) {
            showError($(this), 'نام کاربری باید حداقل 4 کاراکتر باشد');
        } else if (!/^[a-zA-Z0-9_]+$/.test(username)) {
            showError($(this), 'فقط حروف، اعداد و زیرخط مجاز است');
        } else {
            checkUsernameAvailability(username);
        }
    });
    
    $('#email').on('blur', function() {
        const email = $(this).val();
        if (!validateEmail(email)) {
            showError($(this), 'آدرس ایمیل معتبر نیست');
        } else {
            checkEmailAvailability(email);
        }
    });
    
    $('#phone').on('blur', function() {
        const phone = $(this).val();
        if (!/^09[0-9]{9}$/.test(phone)) {
            showError($(this), 'شماره تلفن معتبر نیست (09123456789)');
        } else {
            clearError($(this));
        }
    });
    
    $('#password, #confirm_password').on('keyup', function() {
        const password = $('#password').val();
        const confirm_password = $('#confirm_password').val();
        
        if (password.length > 0 && password.length < 6) {
            showError($('#password'), 'رمز عبور باید حداقل 6 کاراکتر باشد');
        } else {
            clearError($('#password'));
        }
        
        if (confirm_password.length > 0 && password !== confirm_password) {
            showError($('#confirm_password'), 'رمز عبور و تکرار آن مطابقت ندارند');
        } else {
            clearError($('#confirm_password'));
        }
    });
    
    function showError(element, message) {
        element.addClass('is-invalid');
        element.next('.invalid-feedback').remove();
        element.after('<div class="invalid-feedback">' + message + '</div>');
    }
    
    function clearError(element) {
        element.removeClass('is-invalid');
        element.next('.invalid-feedback').remove();
    }
    
    function checkUsernameAvailability(username) {
        $.ajax({
            url: 'ajax/check_username.php',
            method: 'POST',
            data: { username: username },
            success: function(response) {
                if (response.available) {
                    clearError($('#username'));
                } else {
                    showError($('#username'), 'این نام کاربری قبلا ثبت شده است');
                }
            }
        });
    }
    
    function checkEmailAvailability(email) {
        $.ajax({
            url: 'ajax/check_email.php',
            method: 'POST',
            data: { email: email },
            success: function(response) {
                if (response.available) {
                    clearError($('#email'));
                } else {
                    showError($('#email'), 'این ایمیل قبلا ثبت شده است');
                }
            }
        });
    }
    
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
});