var UserService = {
  login: function () {
    FormValidation.validate(
      "#loginForm",
      {
        login: {
          required: true,
        },
        password: {
          required: true,
          minlength: 5,
        },
      },
      function (formData) {
        RestClient.post(
          "login",
          {
            username: formData.login,
            password: formData.password,
          },
          function (response) {
            console.log("Login successful", response);
            localStorage.setItem("token", response.token);
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

  // register: function () {
  //   FormValidation.validate(
  //     "#registrationForm",
  //     {
  //       username: {
  //         required: true,
  //         minlength: 3,
  //       },
  //       email: {
  //         required: true,
  //         email: true,
  //       },
  //       password: {
  //         required: true,
  //         minlength: 5,
  //       },
  //       confirm_password: {
  //         required: true,
  //         equalTo: "#password",
  //       },
  //     },
  //     function (formData) {
  //       RestClient.post(
  //         "/backend/rest/register",
  //         formData,
  //         function (response) {
  //           console.log("Registration successful", response);
  //           window.location.href = "#login";
  //           toastr.success("Registration successful! Please login.");
  //           $("#registrationForm")[0].reset();
  //         },
  //         function (error) {
  //           console.error("Registration failed", error);
  //           toastr.error("Registration failed: " + error.responseText);
  //         }
  //       );
  //     }
  //   );
  // },

  isLoggedIn: function () {
    var token = localStorage.getItem("token");
    console.log("Token in localStorage:", token);
    if (!token) {
      return false;
    }
    
    try {
      var decodedToken = jwt_decode(token);
      console.log("Decoded Token:", decodedToken);
      
      // Check if token is expired
      var currentTime = Date.now() / 1000; // in seconds
      if (decodedToken.exp < currentTime) {
        console.log("Token is expired.");
        localStorage.removeItem("token");
        return false;
      }
      
      return true;
    } catch (e) {
      console.error("Failed to decode token:", e);
      localStorage.removeItem("token");
      return false;
    }
  },

  getCurrentUser: function () {
    var token = localStorage.getItem("token");
    if (token) {
      var decodedToken = jwt_decode(token);
      return decodedToken.user;
    }
    return null;
  },

  logout: function () {
    RestClient.post(
      "logout",
      {},
      function (response) {
        console.log("Logout successful", response);
        localStorage.removeItem("token");
        window.location.href = "#landing";
        toastr.success("Logout successful!");
      },
      function (error) {
        console.error("Logout failed", error);
        toastr.error("Logout failed: " + error.responseText);
      }
    );
  },
};

$(document).ready(function () {
  // Get the current user
  var currentUser = UserService.getCurrentUser();
  if (!currentUser) {
    // toastr.error("User not logged in");
    return;
  }
  // Handle logout button click
  $("#logoutButton").on("click", function () {
    UserService.logout();
  });
});
