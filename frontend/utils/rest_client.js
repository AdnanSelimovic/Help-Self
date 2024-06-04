var RestClient = {
  request: function (url, method, data, callback, error_callback) {
    $.ajax({
      url: Constants.API_BASE_URL + url,
      type: method,
      data: data,
      headers: {
        "Authorization": "Bearer " + localStorage.getItem("token")
      },
    })
      .done(function (response, status, jqXHR) {
        if (callback) callback(response);
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
        if (error_callback) {
          error_callback(jqXHR);
        } else {
          toastr.error(jqXHR.responseJSON.message);
        }
      });
  },
  get: function (url, callback, error_callback) {
    this.request(url, "GET", null, callback, error_callback);
  },
  post: function (url, data, callback, error_callback) {
    this.request(url, "POST", data, callback, error_callback);
  },
  delete: function (url, data, callback, error_callback) {
    this.request(url, "DELETE", data, callback, error_callback);
  },
  put: function (url, data, callback, error_callback) {
    this.request(url, "PUT", data, callback, error_callback);
  },
};
