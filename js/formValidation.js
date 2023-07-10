const input_name = $("#name");
const input_email = $("#email");
const input_message = $("#message");
const input_updates = $("#updates");
const select = $("#interested option:selected");

function preload_btn(i) {
  var btn = $(i);
  var btn_w = btn.outerWidth();
  var btn_h = btn.outerHeight();
  var btn_clone = btn.clone(true);

  btn.replaceWith('<div class="__btn _load"></div>');
  $(".__btn._load").outerWidth(btn_w).outerHeight(btn_h);

  return btn_clone;
}

function validateEmail(attachErrorNow) {
  const email = input_email.val();
  const emailRegexp =
    /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

  let error = "";
  if (!email.match(emailRegexp)) {
    error = "Enter correct email";
  }
  if (!email) {
    error = "Enter your email";
  }
  if (attachErrorNow) {
    if (error) {
      input_email.siblings(".input_error").text(error);
    }
  } else {
    return error;
  }
}
function display_errors(errors) {
  if (errors.name) {
    input_name.siblings(".input_error").text(errors.name);
  }
  if (errors.email) {
    input_email.siblings(".input_error").text(errors.email);
  }
  if (errors.message) {
    input_message.siblings(".input_error").text(errors.message);
  }
}

function on_form_input(inputName) {
  const touchedInput = $(`#${inputName}`).siblings(".input_error");
  const errorText = touchedInput.text();

  if (errorText) {
    touchedInput.text("");
  }
  if (inputName === "email") {
    validateEmail(true);
  }
}

function form_send(i) {
  var btn = preload_btn(i);
  $(".input_error").text("");

  const input = input_name.val();
  const email = input_email.val();
  const message = input_message.val();
  const updates = input_updates.is(":checked");
  const selected = false;

  const errors = {};

  if (!input) {
    errors.name = "Enter your name";
  }
  const emailError = validateEmail();
  if (emailError) {
    errors.email = emailError;
  }
  if (!message) {
    errors.message = "White your message";
  }

  if (Object.keys(errors)) {
    display_errors(errors);
  }

  if (select.val() != "placeholder") {
    selected = select.text();
  }

  $.ajax({
    type: "POST",
    url: "/apps/send.app.php",
    data: {
      name,
      email,
      message,
      updates,
      selected,
    },

    success: function (html) {
      var json = JSON.parse(html);
      if (json.process == 1) {
        input_name
          .val("")
          .removeClass(a)
          .siblings(".input_palceholder")
          .removeClass(a);
        input_email
          .val("")
          .removeClass(a)
          .siblings(".input_palceholder")
          .removeClass(a);
        input_message
          .val("")
          .removeClass(a)
          .siblings(".input_palceholder")
          .removeClass(a);
        input_updates.prop("checked", false);
        $("#interested option[value=placeholder]").prop("selected", true);
        $("#interested")
          .siblings(".current")
          .text($("#interested option[value=placeholder]").text());
        $("#interested")
          .siblings(".select_dropdown")
          .find("li")
          .removeClass("selected");

        setTimeout(function () {
          $(".form_btn")
            .hide()
            .before(
              '<div class="form_bottom_message" id="message">your message has been sent</div>'
            );
        }, 10);
      } else {
        display_errors(json.errs);
      }

      $(".__btn._load").replaceWith(btn);
    },

    error: function (error) {
      $(".__btn._load").replaceWith(btn);
    },
  });
}
