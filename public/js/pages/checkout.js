/**
 * Component used for handling checkout dialog actions
 */
"use strict";
/* global app, trans, trans_choice, launchToast, getWebsiteFormattedAmount, getTaxDescription */

$(function () {
  // Document ready
  // Deposit amount change event listener
  $("#checkout-amount").on("change", function () {
    $(".credit-payment-provider").css("pointer-events", "auto");
    if (!checkout.checkoutAmountValidation()) {
      return false;
    }

    // update payment amount
    checkout.paymentData.amount = parseFloat($("#checkout-amount").val());
    checkout.updatePaymentSummaryData();
  });

  // Checkout proceed button event listener
  $(".checkout-continue-btn").on("click", function () {
    checkout.initPayment();
  });

  $(".custom-control").on("change", function () {
    $(".error-message").hide();
  });

  $("#headingOne").on("click", function () {
    if ($("#headingOne").hasClass("collapsed")) {
      $(".card-header .label-icon").html(
        '<ion-icon name="chevron-up-outline"></ion-icon>'
      );
    } else {
      $(".card-header .label-icon").html(
        '<ion-icon name="chevron-down-outline"></ion-icon>'
      );
    }
  });

  $("#checkout-center").on("show.bs.modal", function (e) {
    //get data-id attribute of the clicked element
    var postId = $(e.relatedTarget).data("post-id");
    var recipientId = $(e.relatedTarget).data("recipient-id");
    var amount = $(e.relatedTarget).data("amount");
    var type = $(e.relatedTarget).data("type");
    var username = $(e.relatedTarget).data("username");
    var firstName = $(e.relatedTarget).data("first-name");
    var lastName = $(e.relatedTarget).data("last-name");
    var billingAddress = $(e.relatedTarget).data("billing-address");
    var name = $(e.relatedTarget).data("name");
    var avatar = $(e.relatedTarget).data("avatar");
    var country = $(e.relatedTarget).data("country");
    var city = $(e.relatedTarget).data("city");
    var state = $(e.relatedTarget).data("state");
    var postcode = $(e.relatedTarget).data("postcode");
    var availableCredit = $(e.relatedTarget).data("available-credit");
    var streamId = $(e.relatedTarget).data("stream-id");
    var userMessageId = $(e.relatedTarget).data("message-id");
    var cardToken = $(e.relatedTarget).data("card_token");
    var cpf = $(e.relatedTarget).data("cpf");
    checkout.initiatePaymentData(
      type,
      amount,
      postId,
      recipientId,
      firstName,
      lastName,
      billingAddress,
      country,
      city,
      state,
      postcode,
      availableCredit,
      streamId,
      userMessageId,
      cardToken,
      cpf
    );
    checkout.updateUserDetails(avatar, username, name);
    checkout.fillCountrySelectOptions();
    checkout.updatePaymentSummaryData();
    checkout.prefillBillingDetails();

    let paymentTitle = "";
    let paymentDescription = "";
    if (type === "tip" || type === "chat-tip") {
      $(".payment-body .checkout-amount-input").removeClass("d-none");
      paymentTitle = trans("Send a tip");
      paymentDescription = trans("Send a tip to this user");
      checkout.togglePaymentProviders(
        true,
        checkout.oneTimePaymentProcessorClasses
      );
    } else if (
      type === "one-month-subscription" ||
      type === "three-months-subscription" ||
      type === "six-months-subscription" ||
      type === "yearly-subscription"
    ) {
      let numberOfMonths = 1;
      let showStripeProvider = !app.stripeRecurringDisabled;
      let showPaypalProvider = !app.paypalRecurringDisabled;
      let showCCBillProvider = !app.ccBillRecurringDisabled;
      let showCreditProvider = !app.localWalletRecurringDisabled;

      // handles ccbill provider as they only allow 30 or 90 days subscriptions
      if (showCCBillProvider) {
        if (type === "three-months-subscription") {
          numberOfMonths = 3;
        } else if (type === "six-months-subscription") {
          numberOfMonths = 6;
          showCCBillProvider = false;
        } else if (type === "yearly-subscription") {
          numberOfMonths = 12;
          showCCBillProvider = false;
        }
      }

      checkout.togglePaymentProviders(
        false,
        checkout.oneTimePaymentProcessorClasses
      );
      checkout.togglePaymentProvider(
        showCCBillProvider,
        ".ccbill-payment-method"
      );
      checkout.togglePaymentProvider(
        showStripeProvider,
        ".stripe-payment-method"
      );
      checkout.togglePaymentProvider(
        showPaypalProvider,
        ".paypal-payment-method"
      );
      checkout.togglePaymentProvider(
        showCreditProvider,
        ".credit-payment-method"
      );

      $(".payment-body .checkout-amount-input").addClass("d-none");
      paymentTitle = trans(type);
      let subscriptionInterval = trans_choice("months", numberOfMonths, {
        number: numberOfMonths,
      });
      let key =
        app.currencyPosition === "left"
          ? "Subscribe to"
          : "Subscribe to rightAligned";
      paymentDescription = trans(key, {
        amount: amount,
        currency: app.currencySymbol,
        username: name,
        subscription_interval: subscriptionInterval,
      });
      checkout.toggleCryptoPaymentProviders(false);
    } else if (type === "post-unlock") {
      $(".payment-body .checkout-amount-input").addClass("d-none");
      paymentTitle = trans("Unlock post");
      paymentDescription =
        trans("Unlock post for") + " " + getWebsiteFormattedAmount(amount);
      checkout.togglePaymentProviders(
        true,
        checkout.oneTimePaymentProcessorClasses
      );
    } else if (type === "stream-access") {
      $(".payment-body .checkout-amount-input").addClass("d-none");
      paymentTitle = trans("Join streaming");
      paymentDescription =
        trans("Join streaming now for") +
        " " +
        getWebsiteFormattedAmount(amount);
      checkout.togglePaymentProviders(
        true,
        checkout.oneTimePaymentProcessorClasses
      );
    } else if (type === "message-unlock") {
      $(".payment-body .checkout-amount-input").addClass("d-none");
      paymentTitle = trans("Unlock message");
      paymentDescription =
        trans("Unlock message for") + " " + getWebsiteFormattedAmount(amount);
      checkout.togglePaymentProviders(
        true,
        checkout.oneTimePaymentProcessorClasses
      );
    }

    if (paymentTitle !== "" || paymentDescription !== "") {
      $("#payment-title").text(paymentTitle);
      $(".payment-body .payment-description").removeClass("d-none");
      $(".payment-body .payment-description").text(paymentDescription);
    }

    if (
      !firstName ||
      !lastName ||
      !billingAddress ||
      !city ||
      !state ||
      !postcode ||
      !country
    ) {
      $("#billingInformation").collapse("show");
    } else {
      $("#billingInformation").collapse("hide");
    }
    $("#checkout-amount").val(amount);
  });

  $("#checkout-center").on("hidden.bs.modal", function () {
    $(this).find("#billing-agreement-form").trigger("reset");
    $(".payment-error").addClass("d-none");
  });

  // Radio button
  $(".radio-buttons .radio-button").on("click", function () {
    $(".radio-buttons .radio-button").removeClass("selected");
    $(this).addClass("selected");
    $(".payment-error").addClass("d-none");
  });

  $(".country-select").on("change", function () {
    checkout.updatePaymentSummaryData();
  });
});

/**
 * Checkout class
 */
var checkout = {
  allowedPaymentProcessors: [
    "stripe",
    "paypal",
    "credit",
    "card",
    "pix",
    "coinbase",
    "nowpayments",
    "ccbill",
    "paystack",
    "oxxo",
    "mercado",
  ],
  paymentData: {},
  oneTimePaymentProcessorClasses: [
    ".nowpayments-payment-method",
    ".coinbase-payment-method",
    ".ccbill-payment-method",
    ".stripe-payment-method",
    ".paypal-payment-method",
    ".paystack-payment-method",
    ".oxxo-payment-method",
    ".mercado-payment-method",
    ".credit-payment-method",
    ".card-payment-method",
    ".pix-payment-method",
  ],

  /**
   * Initiates the payment data payload
   */
  initiatePaymentData: function (
    type,
    amount,
    post,
    recipient,
    firstName,
    lastName,
    billingAddress,
    country,
    city,
    state,
    postcode,
    availableCredit,
    streamId,
    messageId
  ) {
    checkout.paymentData = {
      type: type,
      amount: amount,
      post: post,
      recipient: recipient,
      firstName: firstName,
      lastName: lastName,
      billingAddress: billingAddress,
      country: country,
      city: city,
      state: state,
      postcode: postcode,
      availableCredit: availableCredit,
      stream: streamId,
      messageId: messageId,
    };
  },

  /**
   * Updates the payment form
   */
  updatePaymentForm: function () {
    $("#payment-type").val(checkout.paymentData.type);
    $("#post").val(checkout.paymentData.post);
    $("#recipient").val(checkout.paymentData.recipient);
    $("#provider").val(checkout.paymentData.provider);
    $("#paymentFirstName").val(checkout.paymentData.firstName);
    $("#paymentLastName").val(checkout.paymentData.lastName);
    $("#paymentBillingAddress").val(checkout.paymentData.billingAddress);
    $("#paymentCountry").val(checkout.paymentData.country);
    $("#paymentState").val(checkout.paymentData.state);
    $("#paymentPostcode").val(checkout.paymentData.postcode);
    $("#paymentCity").val(checkout.paymentData.city);
    $("#payment-deposit-amount").val(checkout.paymentData.totalAmount);
    $("#paymentTaxes").val(JSON.stringify(checkout.paymentData.taxes));
    $("#stream").val(checkout.paymentData.stream);
    $("#userMessage").val(checkout.paymentData.messageId);
  },

  stripe: null,

  /**
   * Instantiates the payment session
   */
  initPayment: function () {
    if (!checkout.checkoutAmountValidation()) {
      return false;
    }

    let processor = checkout.getSelectedPaymentMethod();
    if (!processor) {
      $(".payment-error").removeClass("d-none");
    }

    if (processor) {
      $(".paymentProcessorError").hide();
      $(".error-message").hide();
      if (checkout.allowedPaymentProcessors.includes(processor)) {
        checkout.updatePaymentForm();
        $(".checkout-continue-btn .spinner-border").removeClass("d-none");
        checkout.validateAllFields(() => {
          $(".payment-button").trigger("click");
        });
      }
    }
  },

  /**
   * Runs backend validation check for billing data
   * @param callback
   */
  validateAllFields: function (callback) {
    checkout.clearFormErrors();

    function validarCPF(cpf) {
      cpf = cpf.replace(/[^\d]/g, "");

      if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) {
        return false;
      }

      var soma = 0;
      var resto;

      for (var i = 1; i <= 9; i++) {
        soma += parseInt(cpf.substring(i - 1, i)) * (11 - i);
      }
      resto = (soma * 10) % 11;
      if (resto === 10 || resto === 11) {
        resto = 0;
      }
      if (resto !== parseInt(cpf.substring(9, 10))) {
        return false;
      }

      soma = 0;
      for (var i = 1; i <= 10; i++) {
        soma += parseInt(cpf.substring(i - 1, i)) * (12 - i);
      }
      resto = (soma * 10) % 11;
      if (resto === 10 || resto === 11) {
        resto = 0;
      }
      if (resto !== parseInt(cpf.substring(10, 11))) {
        return false;
      }

      return true;
    }

    $.ajax({
      type: "POST",
      data: $("#pp-buyItem").serialize(),
      url: app.baseUrl + "/payment/initiate/validate",
      success: function (response) {
        if ($("#provider").val() === "credit") {
          callback();
          return;
        }
        if ($("#provider").val() === "pix") {
          var cpf = $("#cpf").val();

          if (!validarCPF(cpf) || !cpf) {
            $(".checkout-continue-btn .spinner-border").addClass("d-none");
            launchToast(
              "danger",
              trans("Error"),
              "CPF inválido. Por favor, insira um CPF válido."
            );
            return;
          }
        }

        if ($("#provider").val() === "card") {
          var cardNumber = $(".cardNumber").val().split(" ").join("");
          var cardholderName = $("#name").val();
          var expirationDate = $(".cardDateValidate").val();
          var securityCode = $(".cardCVV").val();

          function validarNumeroCartao(numero) {
            var soma = 0;
            var alternar = false;

            for (var i = numero.length - 1; i >= 0; i--) {
              var n = parseInt(numero.charAt(i), 10);
              if (alternar) {
                n *= 2;
                if (n > 9) {
                  n -= 9;
                }
              }
              soma += n;
              alternar = !alternar;
            }

            return soma % 10 === 0;
          }

          function validarDataValidade(data) {
            var mesAno = data.split("/");
            var mes = parseInt(mesAno[0], 10);
            var ano = parseInt(mesAno[1], 10);

            var dataAtual = new Date();
            var anoAtual = dataAtual.getFullYear();
            var mesAtual = dataAtual.getMonth() + 1;

            return ano > anoAtual || (ano === anoAtual && mes >= mesAtual);
          }

          var cpf = $("#cpf").val();
          if (!validarCPF(cpf) || !cpf) {
            $(".checkout-continue-btn .spinner-border").addClass("d-none");
            launchToast(
              "danger",
              trans("Error"),
              "CPF inválido. Por favor, insira um CPF válido."
            );
            return;
          }

          if (!validarNumeroCartao(cardNumber) || !cardNumber) {
            $(".checkout-continue-btn .spinner-border").addClass("d-none");
            launchToast(
              "danger",
              trans("Error"),
              "Número do cartão de crédito inválido. Por favor, insira um número válido."
            );
            return;
          }

          if (!validarDataValidade(expirationDate)) {
            $(".checkout-continue-btn .spinner-border").addClass("d-none");
            launchToast(
              "danger",
              trans("Error"),
              "Data de validade do cartão é inválida. Por favor, insira uma data válida."
            );
            return;
          }

          if (securityCode.length !== 3) {
            $(".checkout-continue-btn .spinner-border").addClass("d-none");
            launchToast(
              "danger",
              trans("Error"),
              "Código de segurança inválido. O código deve ter 3 dígitos."
            );
            return;
          }

          var cardTokenJson = JSON.stringify({
            card_number: cardNumber,
            cardholder: {
              name: cardholderName || "Jonh Doe",
              identification: {
                type: "CPF",
                number: $("#cpf").val(),
              },
            },
            expiration_month: expirationDate.split("/")[0],
            expiration_year: expirationDate.split("/")[1],
            security_code: securityCode,
          });

          $("#cardToken").val(cardTokenJson);
        }

        $.ajax({
          type: "POST",
          url: app.baseUrl + "/payment/initiate",
          data: {
            amount: $("#payment-deposit-amount").val(),
            transaction_type: $("#payment-type").val(),
            post_id: $("#post").val(),
            user_message_id: $("#userMessage").val(),
            recipient_user_id: $("#recipient").val(),
            provider: $("#provider").val(),
            first_name: $("#paymentFirstName").val(),
            last_name: $("#paymentLastName").val(),
            billing_address: $("#paymentBillingAddress").val(),
            city: $("#paymentCity").val(),
            state: $("#paymentState").val(),
            postcode: $("#paymentPostcode").val(),
            country: $("#paymentCountry").val(),
            taxes: $("#paymentTaxes").val(),
            stream: $("#stream").val(),
            card_token: $("#cardToken").val(),
            cpf: $("#cpf").val(),
          },
          success: function (paymentResponse) {
            if ($("#provider").val() === "pix") {
              const amountPix = document.getElementById("amountPix");
              amountPix.innerHTML = "";
              const pixCopiaECola = paymentResponse.pixCopiaECola;
              launchToast(
                "success",
                trans("Success"),
                "Pix gerado com sucesso"
              );
              $("#checkout-center").modal("hide");
              $("#checkout-pix").modal("show");

              new QRCode(document.getElementById("qrcode"), {
                text: pixCopiaECola,
                width: 200,
                height: 200,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H,
              });
              $(".btnPix").attr("data-value", pixCopiaECola);
              checkout.calculateTimeDifference();
              amountPix.innerText = "R$ " + paymentResponse.valor.original;
            } else {
              console.log(paymentResponse);
              $("#checkout-center").modal("hide");
              launchToast(
                "success",
                trans("Success"),
                "Pagamento bem sucedido"
              );
            }
          },
          error: function (paymentError) {
            $("#checkout-center").modal("hide");
            $(".checkout-continue-btn .spinner-border").addClass("d-none");
            launchToast(
              "danger",
              trans("Error"),
              paymentError.responseJSON.message
            );
          },
        });
      },
      error: function (result) {
        $(".checkout-continue-btn .spinner-border").addClass("d-none");
        if (result.status === 500) {
          launchToast("danger", trans("Error"), result.responseJSON.message);
        }
        $.each(result.responseJSON.errors, function (field, error) {
          let fieldElement = $(".uifield-" + field);
          fieldElement.addClass("is-invalid");
          fieldElement.parent().append(
            `
                    <span class="invalid-feedback" role="alert">
                        <strong>${error}</strong>
                    </span>
                    `
          );
        });
      },
    });
  },

  calculateTimeDifference: function () {
    const now = new Date();
    const expiration = new Date(now.getTime() + 3600000);
    let intervalId;
    let dataExpiration = document.getElementById("dataExpiration");

    const updateRemainingTime = () => {
      const now = new Date();
      let differenceInMilliseconds = expiration.getTime() - now.getTime();
      if (differenceInMilliseconds < 0) {
        clearInterval(intervalId);
        return "00:00:00";
      }

      const differenceInSeconds = Math.floor(differenceInMilliseconds / 1000);
      const hours = Math.floor(differenceInSeconds / 3600);
      const minutes = Math.floor((differenceInSeconds % 3600) / 60);
      const seconds = differenceInSeconds % 60;

      const formattedHours = String(hours).padStart(2, "0");
      const formattedMinutes = String(minutes).padStart(2, "0");
      const formattedSeconds = String(seconds).padStart(2, "0");

      dataExpiration.innerText = `${formattedHours}h ${formattedMinutes}min ${formattedSeconds}s`;
    };

    intervalId = setInterval(updateRemainingTime, 1000);
    updateRemainingTime();
  },
  /**
   * Clears up dialog (all) form errors
   */
  clearFormErrors: function () {
    // Clearing up prev form errors
    $(".invalid-feedback").remove();
    $("input").removeClass("is-invalid");
  },

  /**
   * Returns currently selected payment method
   */
  getSelectedPaymentMethod: function () {
    const paypalProvider = $(".paypal-payment-provider").hasClass("selected");
    const stripeProvider = $(".stripe-payment-provider").hasClass("selected");
    const creditProvider = $(".credit-payment-provider").hasClass("selected");
    const cardProvider = $(".card-payment-provider").hasClass("selected");
    const pixProvider = $(".pix-payment-provider").hasClass("selected");

    const coinbaseProvider = $(".coinbase-payment-provider").hasClass(
      "selected"
    );
    const nowPaymentsProvider = $(".nowpayments-payment-provider").hasClass(
      "selected"
    );
    const ccbillProvider = $(".ccbill-payment-provider").hasClass("selected");
    const paystackProvider = $(".paystack-payment-provider").hasClass(
      "selected"
    );
    const oxxoProvider = $(".oxxo-payment-provider").hasClass("selected");
    const mercadoProvider = $(".mercado-payment-provider").hasClass("selected");
    let val = null;
    if (paypalProvider) {
      val = "paypal";
    } else if (stripeProvider) {
      val = "stripe";
    } else if (creditProvider) {
      val = "credit";
    } else if (cardProvider) {
      val = "card";
    } else if (pixProvider) {
      val = "pix";
    } else if (coinbaseProvider) {
      val = "coinbase";
    } else if (nowPaymentsProvider) {
      val = "nowpayments";
    } else if (ccbillProvider) {
      val = "ccbill";
    } else if (paystackProvider) {
      val = "paystack";
    } else if (oxxoProvider) {
      val = "oxxo";
    } else if (mercadoProvider) {
      val = "mercado";
    }

    if (!val) val = "credit";
    if (val) {
      checkout.paymentData.provider = val;
      return val;
    }
    return false;
  },

  /**
   * Validates the amount field
   * @returns {boolean}
   */
  checkoutAmountValidation: function () {
    const checkoutAmount = $("#checkout-amount").val();
    // Apply a tips min-max validation | Rest don't need any constrains
    if (checkout.paymentData.type == "tip") {
      if (
        checkoutAmount.length > 0 &&
        checkoutAmount >= app.tipMinAmount &&
        checkoutAmount <= app.tipMaxAmount
      ) {
        $("#checkout-amount").removeClass("is-invalid");
        $("#paypal-deposit-amount").val(checkoutAmount);
        if (checkout.paymentData.availableCredit < checkoutAmount) {
          $(".credit-payment-provider").css("pointer-events", "none");
        }
        return true;
      } else {
        $("#checkout-amount").addClass("is-invalid");
        return false;
      }
    }
    return true;
  },

  /**
   * Validates FN field
   */
  validateFirstNameField: function () {
    let firstNameField = $('input[name="firstName"]');
    checkout.paymentData.firstName = firstNameField.val();
  },

  /**
   * Validates LN field
   */
  validateLastNameField: function () {
    let lastNameField = $('input[name="lastName"]');
    checkout.paymentData.lastName = lastNameField.val();
  },

  /**
   * Validates Adress field
   */
  validateBillingAddressField: function () {
    let billingAddressField = $('textarea[name="billingAddress"]');
    checkout.paymentData.billingAddress = billingAddressField.val();
  },

  /**
   * Validates city field
   */
  validateCityField: function () {
    let cityField = $('input[name="billingCity"]');
    checkout.paymentData.city = cityField.val();
  },

  /**
   * Validates state field
   */
  validateStateField: function () {
    let stateField = $('input[name="billingState"]');
    checkout.paymentData.state = stateField.val();
  },

  /**
   * Validates the ZIP code
   */
  validatePostcodeField: function () {
    let postcodeField = $('input[name="billingPostcode"]');
    checkout.paymentData.postcode = postcodeField.val();
  },

  /**
   * Validates the country field
   */
  validateCountryField: function () {
    let countryField = $(".country-select");
    let countryValidation = $(".country-select").find(":selected").val().length;
    let selectedCountry = $(".country-select").find(":selected");
    if (countryValidation) {
      countryField.removeClass("is-invalid");
      checkout.paymentData.country = selectedCountry.text();
    } else {
      checkout.paymentData.country = "";
    }
  },

  /**
   * Prefills user billing data, if available
   */
  prefillBillingDetails: function () {
    $('input[name="firstName"]').val(checkout.paymentData.firstName);
    $('input[name="lastName"]').val(checkout.paymentData.lastName);
    $('input[name="billingCity"]').val(checkout.paymentData.city);
    $('input[name="billingState"]').val(checkout.paymentData.state);
    $('input[name="billingPostcode"]').val(checkout.paymentData.postcode);
    $('textarea[name="billingAddress"]').val(
      checkout.paymentData.billingAddress
    );
  },

  /**
   * Updates user details
   * @param userAvatar
   * @param username
   * @param name
   */
  updateUserDetails: function (userAvatar, username, name) {
    $(".payment-body .user-avatar").attr("src", userAvatar);
    $(".payment-body .name").text(name);
    $(".payment-body .username").text("@" + username);
  },

  /**
   * Fetches list of countries, in order to calculcate taxes
   */
  fillCountrySelectOptions: function () {
    $.ajax({
      type: "GET",
      url: app.baseUrl + "/countries",
      success: function (result) {
        if (
          result !== null &&
          typeof result.countries !== "undefined" &&
          result.countries.length > 0
        ) {
          $(".country-select")
            .find("option")
            .remove()
            .end()
            .append(
              '<option value="">' + trans("Select a country") + "</option>"
            );
          $.each(result.countries, function (i, item) {
            let selected =
              checkout.paymentData.country !== null &&
              checkout.paymentData.country === item.name;
            $(".country-select").append(
              $("<option>", {
                value: item.id,
                text: item.name,
                selected: selected,
              }).data({ taxes: item.taxes })
            );
            if (selected) {
              checkout.updatePaymentSummaryData();
            }
          });
        }
      },
    });
  },

  /**
   * Updates payment summary data, taxes included
   */
  updatePaymentSummaryData: function () {
    let subtotalAmount =
      typeof checkout.paymentData.amount !== "undefined"
        ? parseFloat(checkout.paymentData.amount)
        : 0.0;
    let countryInclusiveTaxesPercentage = 0.0;
    let countryExclusiveTaxesPercentage = 0.0;
    let taxesAmount = 0.0;
    let totalAmount = subtotalAmount;
    let inclusiveTaxesAmount = 0.0;
    let exclusiveTaxesAmount = 0.0;
    let fixedTaxesAmount = 0.0;
    checkout.paymentData.totalAmount = subtotalAmount;
    let taxes = [];

    // calculate taxes by country
    $(".taxes-details").html("");
    let selectedCountry = $(".country-select").find(":selected");
    if (selectedCountry !== null && selectedCountry.val() > 0) {
      let countryTaxes = selectedCountry.data("taxes");
      if (countryTaxes !== null) {
        if (countryTaxes.length > 0) {
          for (let i = 0; i < countryTaxes.length; i++) {
            let countryTaxAmount = 0.0;
            if (countryTaxes[i].type === "exclusive") {
              countryExclusiveTaxesPercentage += parseFloat(
                countryTaxes[i].percentage
              );
              taxes.push({
                countryTaxName: countryTaxes[i].name,
                type: "exclusive",
                countryTaxPercentage: parseFloat(countryTaxes[i].percentage),
                hidden: countryTaxes[i].hidden,
              });
            }

            if (countryTaxes[i].type === "inclusive") {
              countryInclusiveTaxesPercentage += parseFloat(
                countryTaxes[i].percentage
              );
              taxes.push({
                countryTaxAmount: countryTaxAmount,
                countryTaxName: countryTaxes[i].name,
                type: "inclusive",
                countryTaxPercentage: parseFloat(countryTaxes[i].percentage),
                hidden: countryTaxes[i].hidden,
              });
            }

            if (countryTaxes[i].type === "fixed") {
              fixedTaxesAmount += parseFloat(countryTaxes[i].percentage);
              taxes.push({
                countryTaxAmount: countryTaxes[i].percentage,
                countryTaxName: countryTaxes[i].name,
                type: "fixed",
                hidden: countryTaxes[i].hidden,
              });
            }
          }
        }
      }
    }

    let formattedTaxes = {
      data: [],
      taxesTotalAmount: 0.0,
      subtotal: subtotalAmount.toFixed(2),
    };

    if (subtotalAmount > 0) {
      for (let j = 0; j < taxes.length; j++) {
        let taxAmount = 0.0;
        let inclusiveTaxesAmount =
          subtotalAmount -
          subtotalAmount /
            (1 + countryInclusiveTaxesPercentage.toFixed(2) / 100);
        if (taxes[j].type === "inclusive") {
          let countryTaxAmount =
            subtotalAmount -
            subtotalAmount /
              (1 + taxes[j].countryTaxPercentage.toFixed(2) / 100);
          let remainingFees = inclusiveTaxesAmount - countryTaxAmount;
          let amountWithoutRemainingFees = subtotalAmount - remainingFees;
          taxAmount =
            amountWithoutRemainingFees -
            amountWithoutRemainingFees /
              (1 + taxes[j].countryTaxPercentage.toFixed(2) / 100);

          try {
            taxAmount = taxAmount.toString().match(/^-?\d+(?:\.\d{0,2})?/)[0];
          } catch (e) {
            taxAmount = taxAmount.toFixed(2);
          }
        }

        if (taxes[j].type === "exclusive") {
          let amountWithInclusiveFeesDeducted =
            subtotalAmount - inclusiveTaxesAmount;
          taxAmount =
            (taxes[j].countryTaxPercentage.toFixed(2) / 100) *
            amountWithInclusiveFeesDeducted;
          taxAmount = taxAmount.toFixed(2);
        }

        if (taxes[j].type === "fixed") {
          taxAmount = taxes[j].countryTaxAmount;
        }

        formattedTaxes.data.push({
          taxName: taxes[j].countryTaxName,
          taxAmount: taxAmount,
          taxPercentage: taxes[j].countryTaxPercentage,
          taxType: taxes[j].type,
        });

        if (!taxes[j].hidden) {
          let item =
            '<div class="row ml-2">\n' +
            '<span class="col-sm left">' +
            getTaxDescription(
              taxes[j].countryTaxName,
              taxes[j].countryTaxPercentage,
              taxes[j].type
            ) +
            "</span>\n" +
            '<span class="country-tax col-sm right text-right">\n' +
            "    <b>" +
            getWebsiteFormattedAmount(taxAmount) +
            "</b>\n" +
            "</span>\n" +
            "</div>";
          $(".taxes-details").append(item);
        }
      }

      let subtotal = subtotalAmount;
      if (countryInclusiveTaxesPercentage > 0) {
        inclusiveTaxesAmount =
          subtotalAmount -
          subtotalAmount /
            (1 + countryInclusiveTaxesPercentage.toFixed(2) / 100);
        if (inclusiveTaxesAmount > 0) {
          subtotal = subtotalAmount - inclusiveTaxesAmount;
        }
      }

      if (countryExclusiveTaxesPercentage > 0) {
        exclusiveTaxesAmount =
          (countryExclusiveTaxesPercentage.toFixed(2) / 100) * subtotal;
        totalAmount = totalAmount + exclusiveTaxesAmount;
      }

      if (fixedTaxesAmount > 0) {
        totalAmount += fixedTaxesAmount;
      }

      if (formattedTaxes.data && formattedTaxes.data.length > 0) {
        for (let i = 0; i < formattedTaxes.data.length; i++) {
          if (formattedTaxes.data[i]["taxAmount"] !== undefined) {
            taxesAmount =
              taxesAmount + parseFloat(formattedTaxes.data[i]["taxAmount"]);
          }
        }
      }
      formattedTaxes.taxesTotalAmount = taxesAmount.toFixed(2);
      checkout.paymentData.totalAmount = totalAmount.toFixed(2);
    }

    checkout.paymentData.taxes = formattedTaxes;

    $(".available-credit").html(
      "(" +
        getWebsiteFormattedAmount(checkout.paymentData.availableCredit) +
        ")"
    );
    if (checkout.paymentData.availableCredit < totalAmount) {
      $(".credit-payment-provider").css("pointer-events", "none");
    }

    $(".subtotal-amount b").html(
      getWebsiteFormattedAmount(subtotalAmount.toFixed(2))
    );
    $(".total-amount b").html(
      getWebsiteFormattedAmount(totalAmount.toFixed(2))
    );
  },

  toggleCryptoPaymentProviders: function (toggle) {
    let coinbasePaymentMethod = $(".coinbase-payment-method");
    let nowPaymentsPaymentMethod = $(".nowpayments-payment-method");
    if (toggle) {
      if (coinbasePaymentMethod.hasClass("d-none")) {
        coinbasePaymentMethod.removeClass("d-none");
      }
      if (nowPaymentsPaymentMethod.hasClass("d-none")) {
        nowPaymentsPaymentMethod.removeClass("d-none");
      }
    } else {
      if (!coinbasePaymentMethod.hasClass("d-none")) {
        coinbasePaymentMethod.addClass("d-none");
      }
      if (!nowPaymentsPaymentMethod.hasClass("d-none")) {
        nowPaymentsPaymentMethod.addClass("d-none");
      }
    }
  },

  togglePaymentProvider: function (toggle, paymentMethodClass) {
    let paymentMethod = $(paymentMethodClass);
    if (toggle) {
      if (paymentMethod.hasClass("d-none")) {
        paymentMethod.removeClass("d-none");
      }
    } else {
      if (!paymentMethod.hasClass("d-none")) {
        paymentMethod.addClass("d-none");
      }
    }
  },

  togglePaymentProviders: function (toggle, paymentMethodClasses) {
    paymentMethodClasses.forEach(function (paymentMethodClass) {
      checkout.togglePaymentProvider(toggle, paymentMethodClass);
    });
  },
};
