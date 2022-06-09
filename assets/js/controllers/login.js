class Login {
  static init() {
    if (window.localStorage.getItem('token')) {
      window.location = 'index.html';
    } else {
      $('body').show();
    }
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('token')) {
      $('#change-password-token').val(urlParams.get('token'));
      Login.showChangePasswordForm();
    }
  }

  static showChangePasswordForm() {
    $('#change-password-form-container').removeClass('hidden');
    $('#login-form-container').addClass('hidden');
    $('#register-form-container').addClass('hidden');
    $('#forgot-form-container').addClass('hidden');
  }

  static showForgotForm() {
    $('#login-form-container').addClass('hidden');
    $('#forgot-form-container').removeClass('hidden');
  }

  static showRegisterForm() {
    $('#login-form-container').addClass('hidden');
    $('#register-form-container').removeClass('hidden');
  }

  static showLoginForm() {
    $('#login-form-container').removeClass('hidden');
    $('#register-form-container').addClass('hidden');
    $('#forgot-form-container').addClass('hidden');
  }

  static doRegister() {
    $('#register-link').prop('disabled', true);

    RestClient.post('api/register', Utils.jsonize_form('#register-form'), (data) => {
      $('#register-form-container').addClass('hidden');
      $('#form-alert').removeClass('hidden');
      $('#form-alert .alert').html(data.message);
    }, (jqXHR, textStatus, errorThrown) => {
      $('#register-link').prop('disabled', false);
      toastr.error(jqXHR.responseJSON.message);
    });
  }

  static doLogin() {
    $('#login-link').prop('disabled', true);

    RestClient.post('api/login', Utils.jsonize_form('#login-form'), (data) => {
      window.localStorage.setItem('token', data.token);
      window.location = 'index.html';
    }, (jqXHR, textStatus, errorThrown) => {
      $('#login-link').prop('disabled', false);
      toastr.error(jqXHR.responseJSON.message);
    });
  }

  static doForgotPassword() {
    $('#forgot-link').prop('disabled', true);

    RestClient.post('api/forgot', Utils.jsonize_form('#forgot-form'), (data) => {
      $('#forgot-form-container').addClass('hidden');
      $('#form-alert').removeClass('hidden');
      $('#form-alert .alert').html(data.message);
    }, (jqXHR, textStatus, errorThrown) => {
      $('#forgot-link').prop('disabled', false);
      $('#forgot-form-container').addClass('hidden');
      toastr.error(jqXHR.responseJSON.message);
    });
  }

  static doChangePassword() {
    $('#change-link').prop('disabled', true);

    RestClient.post('api/reset', Utils.jsonize_form('#change-form'), (data) => {
      window.localStorage.setItem('token', data.token);
      window.location = 'index.html';
    }, (jqXHR, textStatus, errorThrown) => {
      $('#change-link').prop('disabled', false);
      toastr.error(jqXHR.responseJSON.message);
    });
  }
}
