class Login {
  static init() {
    if (window.localStorage.getItem('token')) {
      window.location = 'index.html';
    } else {
      $('body').show();
    }
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('token')) {
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
      window.location = 'confirmation.html';
    }, (jqXHR, textStatus, errorThrown) => {
      $('#register-link').prop('disabled', false);
      toastr.error(jqXHR.responseJSON.message);
    });
  }

  
  static doConfirmCheck() {
    const params = new URLSearchParams(document.location.search);
    const token = params.get("token");

    console.log(token)

    RestClient.post('api/confirm', {"token": token}, (data) => {
      window.localStorage.setItem('token', data.token);
      window.location = 'index.html';
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
      $('#forgot-link').prop('disabled', false);
      $('#form-alert').removeClass('hidden');
      $('#form-alert .alert').html(data.message);
      $('#login-form-container').removeClass('hidden');
    }, (jqXHR, textStatus, errorThrown) => {
      $('#forgot-link').prop('disabled', false);
      $('#forgot-form-container').addClass('hidden');
      toastr.error(jqXHR.responseJSON.message);
    });
  }

  static doChangePassword() {
    $('#change-link').prop('disabled', true);
    var token = location.search.split('token=')[1]
    var password = document.getElementById("password-changed").value;

    var data = {
      "token": token,
      "password": password
    };
    
    RestClient.post('api/reset', data, (data) => {
      window.localStorage.setItem('token', data.token);
      window.location = 'index.html';
    }, (jqXHR, textStatus, errorThrown) => {
      $('#change-link').prop('disabled', false);
      toastr.error(jqXHR.responseJSON.message);
    });
  }
}
