(function ($) {
  'use strict';

  function getCouponEndpoint() {
    if (typeof wc_checkout_params === 'undefined') {
      return '';
    }

    return wc_checkout_params.wc_ajax_url.replace('%%endpoint%%', 'apply_coupon');
  }

  function syncPaymentSelection() {
    $('.dc-payment-method').each(function () {
      const method = $(this);
      method.toggleClass('is-selected', method.children('input[name="payment_method"]').is(':checked'));
    });
  }

  function positionPaymentPanel() {
    const form = $('form.checkout');
    const customerDetails = form.children('#customer_details');
    const payment = form.find('#payment');

    if (customerDetails.length && payment.length && !payment.prev().is('#customer_details')) {
      payment.insertAfter(customerDetails);
    }
  }

  function clearRestoredCustomerDetails() {
    const customerDetails = $('#customer_details');

    customerDetails.find('input[type="text"], input[type="email"], input[type="tel"], textarea').val('');
    customerDetails.find('select').not('#billing_country, #shipping_country').val('').trigger('change.select2');
  }

  $(document.body).on('change', 'input[name="payment_method"]', syncPaymentSelection);
  $(document.body).on('updated_checkout', function () {
    positionPaymentPanel();
    syncPaymentSelection();
  });

  $(document.body).on('click', '.dc-coupon-button', function () {
    const button = $(this);
    const control = button.closest('.dc-coupon-control');
    const input = control.find('.dc-coupon-input');
    const feedback = control.siblings('.dc-coupon-feedback');
    const couponCode = input.val().trim();
    const endpoint = getCouponEndpoint();

    if (!couponCode || !endpoint) {
      feedback.text(couponCode ? 'Unable to apply this code right now.' : 'Enter a discount code.');
      return;
    }

    button.prop('disabled', true).addClass('is-loading');
    feedback.text('');

    $.post(endpoint, {
      security: wc_checkout_params.apply_coupon_nonce,
      coupon_code: couponCode
    })
      .done(function (response) {
        const message = $('<div>').html(response).text().trim();
        feedback.text(message);
        $(document.body).trigger('update_checkout', { update_shipping_method: false });
      })
      .fail(function () {
        feedback.text('Unable to apply this code right now.');
      })
      .always(function () {
        button.prop('disabled', false).removeClass('is-loading');
      });
  });

  $(document.body).on('keydown', '.dc-coupon-input', function (event) {
    if (event.key === 'Enter') {
      event.preventDefault();
      $(this).siblings('.dc-coupon-button').trigger('click');
    }
  });

  positionPaymentPanel();
  syncPaymentSelection();

  window.addEventListener('pageshow', function (event) {
    if (event.persisted) {
      clearRestoredCustomerDetails();
      $(document.body).trigger('update_checkout');
    }
  });
})(jQuery);
