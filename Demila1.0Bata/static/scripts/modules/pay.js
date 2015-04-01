define(function (require, exports, module){
	var $ = require("jq");
	function init(){
		var newSwiftStarted = false, 

	        // cache the dom elements in variables
	        customAmountRadio = document.getElementById("maximum_at_period_end_false"),
	        customAmount = document.getElementById("amount"),
	        allEarnings = document.getElementById("maximum_at_period_end_true"),
	        $allEarningsNotice = $("#all-earnings-notice"),
	        
	        servicePaypal = document.getElementById("service_paypal"),
	        serviceAlipay = document.getElementById("service_alipay"),
	        servicePayoneer = document.getElementById("service_payoneer"),
	        serviceSwift = document.getElementById("service_swift"),

	        $paypalAndAlipayFields = $(".paypal-alipay"),

	        $payoneerNotice = $("#payoneer-notice"),

	        $existingSwift = $("#existing-swift"),
	        $showNewSwift = $("#show_swift_instructions"),
	        $useExisting = $("#existing_swift_details"),
	        $newSwift = $(".new-swift"),

	        aussie = document.getElementById("taxable_chinese_resident"),
	        $taxationDetails = $("#taxation-details"),
	        hobbyistTrue = document.getElementById("hobbyist_true"),
	        hobbyistFalse = document.getElementById("hobbyist_false"),

	        $taxNumbers = $(".tax-number"),

	        $swiftNotice = $(".swift-notice"),
	        $swiftAdditionalInstructions = $("#swift_additional_instructions"),
	        $amountLessFee = $("#amount-less-fee"),
	        maxWithdrawalAmountLessFee = $('#max-withdrawal-amount-less-fee').val(),

	        // functions
	        calculateWithdrawalAmountLessFee,
	        check,
	        checkAmount,
	        checkService,
	        hideServicesExcept,
	        showPaypalAlipayFields,
	        whichSwift,
	        checkTax;
	    calculateWithdrawalAmountLessFee = function () {
	        if (typeof allEarnings !== "undefined" && allEarnings !== null && allEarnings.checked) {
	            return;
	        }

	        var amount = $(customAmount).val().replace('$', ''), dollars = 0, cents = 0, amountLessFee = 0, parts;

	        if (amount.match(/^\d+$/)) {
	            dollars = parseInt(amount, 10);
	        } else if (amount.match(/^\d+\.\d\d$/)) {
	            parts = amount.split('.');
	            dollars = parseInt(parts[0], 10);
	            cents = parseInt(parts[1], 10);
	        } else if (amount.match(/^\d{1,3},\d\d\d\.\d\d$/)) {
	            parts = amount.split(',');
	            dollars = parseInt(parts[0], 10) * 1000;
	            parts = parts[1].split('.');
	            dollars += parseInt(parts[0], 10);
	            cents = parseInt(parts[1], 10);
	        } else if (amount.match(/^\d{1,3},\d\d\d$/)) {
	            parts = amount.split(',');
	            dollars = parseInt(parts[0], 10) * 1000;
	            dollars += parseInt(parts[1], 10);
	        }

	        amountLessFee = (dollars - parseFloat($('#swift-transaction-fee').val())).toString();

	        if (cents < 10) {
	            amountLessFee += '.0' + cents;
	        } else {
	            amountLessFee += '.' + cents;
	        }

	        $amountLessFee.html(amountLessFee);      
	    };

	    check = function () {
	        checkAmount();
	        checkService();
	        checkTax();
	    };

	    checkAmount = function () {
	        if (customAmountRadio && customAmountRadio.checked) {
	            customAmount.disabled = false;
	            $allEarningsNotice.addClass("hidden");
	            calculateWithdrawalAmountLessFee();
	        } else if (allEarnings && allEarnings.checked) {
	            customAmount.disabled = true;
	            $allEarningsNotice.removeClass("hidden");
	            $amountLessFee.html(maxWithdrawalAmountLessFee);
	        }
	    };

	    checkService = function () {
	        if (servicePaypal && servicePaypal.checked) {
	            showPaypalAlipayFields("paypal");
	            hideServicesExcept("paypal");
	        } else if (serviceAlipay && serviceAlipay.checked) {
	            showPaypalAlipayFields("alipay");
	            hideServicesExcept("alipay");
	        } else if (servicePayoneer && servicePayoneer.checked) {
	            $payoneerNotice.removeClass("hidden");
	            hideServicesExcept("payoneer");
	        } else if (serviceSwift && serviceSwift.checked) {
	            if ($existingSwift.length > 0) {
	                whichSwift();
	            } else {
	                $newSwift.removeClass("hidden");
	            }
	            hideServicesExcept("swift");
	            $swiftNotice.removeClass("hidden");
	            $swiftAdditionalInstructions.removeClass("hidden");
	        }
	    };

	    hideServicesExcept = function (serviceToShow) {
	        if (serviceToShow !== "paypal" && serviceToShow !== "alipay") {
	            $paypalAndAlipayFields.addClass("hidden");
	        }
	        if (serviceToShow !== "payoneer") {
	            $payoneerNotice.addClass("hidden");
	        }
	        if (serviceToShow !== "swift") {
	            $existingSwift.addClass("hidden");
	            $newSwift.addClass("hidden");
	            $swiftNotice.addClass("hidden");
	            $swiftAdditionalInstructions.addClass("hidden");
	        }
	    };

	    showPaypalAlipayFields = function (service) {
	        if (service === "paypal") {
	            $paypalAndAlipayFields
	                .removeClass("hidden")
	                .find("label[for=payment_email_address]")
	                .html("PayPal username")
	                .end()
	                .find("label[for=payment_email_address_confirmation]")
	                .html("Confirm PayPal username");
	        } else if (service === "alipay") {
	            $paypalAndAlipayFields
	                .removeClass("hidden")
	                .find("label[for=payment_email_address]")
	                .html("Alipay username")
	                .end()
	                .find("label[for=payment_email_address_confirmation]")
	                .html("Confirm Alipay username");
	        }
	    };

	    whichSwift = function () {
	        if ($useExisting.val() === "true") {
	            $existingSwift.removeClass("hidden");
	        } else {
	            $newSwift.removeClass("hidden");
	        }
	    };

	    checkTax = function () {
	        if (aussie && aussie.checked) {
	            $taxationDetails.removeClass("hidden");

	            if (hobbyistTrue && hobbyistTrue.checked) {
	                $taxNumbers.addClass("hidden");
	            }

	            if (hobbyistFalse && hobbyistFalse.checked) {
	                $taxNumbers.removeClass("hidden");
	            }
	        } else {
	            $taxationDetails.addClass("hidden");
	            $taxNumbers.addClass("hidden");
	        }
	    };

	    $showNewSwift.click(function (e) {
	        e.preventDefault();

	        $existingSwift.addClass("hidden");
	        $useExisting.val("false");
	    });

	    $(customAmount).on({
	        change: calculateWithdrawalAmountLessFee,
	        keyup: calculateWithdrawalAmountLessFee
	    });

	    check();

		$("form").click(function () {
    		check();
		});

		//paymentRequest();
	}
	exports.init = init;

	/*function paymentRequest() {
    var e, t;
    return e = function(e) {
      return $(e).is(":visible")
    }, t = function(e, t, n) {
      return {
        url: "/withdrawals_ajax/validate_swift",
        type: "post",
        data: {
          attribute: n || e,
          value: function() {
            return t.val()
          }
        },
        beforeSend: function() {
          return t.addClass("validating")
        },
        complete: function() {
          return t.removeClass("validating")
        }
      }
    }, $("#payment_form").validate({
      errorClass: "invalid",
      validClass: "valid",
      rules: {
        payment_email_address: {
          required: e
        },
        payment_email_address_confirmation: {
          required: e
        },
        swift_full_address: {
          required: e,
          remote: {
            depends: e,
            param: t("swift_full_address", $("#swift_full_address"))
          }
        },
        swift_full_address_line2: {
          remote: {
            depends: e,
            param: t("swift_full_address_line2", $("#swift_full_address_line2"))
          }
        },
        swift_full_address_line3: {
          remote: {
            depends: e,
            param: t("swift_full_address_line3", $("#swift_full_address_line3"))
          }
        },
        swift_address_state: {
          remote: {
            depends: e,
            param: t("swift_address_state", $("#swift_address_state"))
          }
        },
        swift_address_postcode: {
          remote: {
            depends: e,
            param: t("swift_address_postcode", $("#swift_address_postcode"))
          }
        },
        swift_address_country_code: {
          required: e
        },
        swift_bank_account_name: {
          required: e,
          remote: {
            depends: e,
            param: t("swift_bank_account_name", $("#swift_bank_account_name"))
          }
        },
        swift_bank_account_number: {
          required: e,
          remote: {
            depends: e,
            param: t("swift_bank_account_number", $("#swift_bank_account_number"))
          }
        },
        swift_code: {
          required: e,
          remote: {
            depends: e,
            param: t("swift_code", $("#swift_code"), "swift_swift_code")
          }
        },
        swift_bank_name: {
          required: e,
          remote: {
            depends: e,
            param: t("swift_bank_name", $("#swift_bank_name"))
          }
        },
        swift_bank_branch_country_code: {
          required: e
        },
        swift_bank_branch_city: {
          remote: {
            depends: e,
            param: t("swift_bank_branch_city", $("#swift_bank_branch_city"))
          }
        },
        swift_intermediary_bank_code: {
          remote: {
            depends: e,
            param: t("swift_intermediary_bank_code", $("#swift_intermediary_bank_code"))
          }
        },
        swift_intermediary_bank_name: {
          remote: {
            depends: e,
            param: t("swift_intermediary_bank_name", $("#swift_intermediary_bank_name"))
          }
        },
        swift_intermediary_bank_city: {
          remote: {
            depends: e,
            param: t("swift_intermediary_bank_city", $("#swift_intermediary_bank_city"))
          }
        },
        swift_intermediary_bank_country_code: {
          remote: {
            depends: e,
            param: t("swift_intermediary_bank_country_code", $("#swift_intermediary_bank_country_code"))
          }
        },
        instructions_from_author: {
          remote: {
            depends: e,
            param: t("instructions_from_author", $("#instructions_from_author"), "swift_additional_instructions")
          }
        }
      }
    })
  }*/
});