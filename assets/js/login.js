let $document = jQuery(document);
$document.ready(function ($) {
  function statusChangeCallback(response) {
    // Called with the results from FB.getLoginStatus().
    //console.log(response);
    if (response.status === "connected") {
      // Logged into your webpage and Facebook.
      //console.log('connected');
      insert_user(response);
    } else {
      // Not logged into your webpage or we are unable to tell.
      //console.log('not connected');
    }
  }
  $(document).on("click", ".facebook-login-btn, .facebook-login-facebook-btn", function (e) {
    e.preventDefault();
    //do the login
    try {
      FB.login(
        function (response) {
          // handle the response
          statusChangeCallback(response);
        },
        { scope: "public_profile,email" }
      );
    } catch (e) {
      $(".facebook-login-response").html(
        "Đã có lỗi xảy ra. Vui lòng thử lại sau!"
      );
    }
  });
  function insert_user(data) {
    // Testing Graph API after login.  See statusChangeCallback() for when this call is made.
    FB.api(
      "/me",
      {
        fields: "name, email, picture",
      },
      function (response) {
        try {
          $.ajax({
            url: ajaxURL,
            type: "post",
            data: {
              action: "facebook_login",
              data: response,
            },
            error: function (request) {
              $(".facebook-login-response")
                .html("Đã có lỗi xảy ra. Vui lòng thử lại sau!")
                .fadeIn();
            },
            beforeSend: function () {
              $(".facebook-login-response").html("");
            },
            success: function (result) {
              // let = data = $.parseJSON(response);
              if (result.success) {
                
                window.location.href = result.data.redirect_url;
              } else {
                $(".facebook-login-response").html(result.data.mesage);
              }
            },
          });
        } catch (error) {
          console.log("facebook_ajax_call error:", error.responseText);
          $(".facebook-login-response").html(error.responseText);
        }
      }
    );
  }
});
