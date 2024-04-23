var UserService = {
  login: function () {
    FormValidation.validate(
      "#loginForm",
      {
        email: {
          required: true,
          email: true,
        },
        password: {
          required: true,
          minlength: 5,
        },
      },
      function (formData) {
        // This function is called after successful validation
        RestClient.post(
          "/../../backend/rest/login_user.php", // This should be the actual endpoint that handles login
          formData,
          function (response) {
            console.log("Login successful", response);
            window.location.href = "#home";
            $("#loginForm")[0].reset();
            toastr.success("Login successful!");
          },
          function (error) {
            console.error("Login failed", error);
            toastr.error("Login failed: " + error.responseText);
          }
        );
      }
    );
  },

  register: function () {
    FormValidation.validate(
      "#registrationForm",
      {
        username: {
          required: true,
          minlength: 3
        },
        email: {
          required: true,
          email: true,
        },
        password: {
          required: true,
          minlength: 5,
        },
        confirm_password: {
          required: true,
          equalTo: "#password"
        }
      },
      function (formData) {
        RestClient.post(
          "/../../backend/rest/register_user.php",
          formData,
          function (response) {
            console.log("Registration successful", response);
            window.location.href = "#login";
            toastr.success("Registration successful! Please login.");
            $("#registrationForm")[0].reset();
          },
          function (error) {
            console.error("Registration failed", error);
            toastr.error("Registration failed: " + error.responseText);
          }
        );
      }
    );
  }
};


$(document).ready(function () {
//   UserService.login();
//   UserService.register();
});
