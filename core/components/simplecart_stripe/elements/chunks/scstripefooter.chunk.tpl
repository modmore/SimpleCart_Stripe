<script type="text/javascript" src="https://js.stripe.com/v3/"></script>
<script type="text/javascript">
    var StripeInstanceCard = Stripe('[[+publishable_key]]'),
        StripeElementsCard = StripeInstanceCard.elements(),
        displayErrorCard = document.getElementById('sc-stripe-[[+method_id]]-card-errors'),
        checkoutFormCard = document.getElementById('simplecartCheckout');

    document.addEventListener("DOMContentLoaded", function(event) {
        // Create the Card element
        var StripeCardCard = StripeElementsCard.create('card'),
            btns = checkoutFormCard.getElementsByTagName('button'),
            cardholderName = document.getElementById('sc-stripe-[[+method_id]]-cardholder');

        StripeCardCard.mount('#sc-stripe-[[+method_id]]-card-element');
        StripeCardCard.addEventListener('change', function(event) {
            if (event.error) {
                displayErrorCard.textContent = event.error.message;
            } else {
                displayErrorCard.textContent = '';
            }
        });

        checkoutFormCard.addEventListener('submit', function(event) {
            if (checkoutFormCard.paymentMethod.value == [[+method_id]]) {
                event.preventDefault();
                for (var i = 0; i < btns.length; i++) {
                    btns[i].setAttribute('disabled', 'disabled');
                }
                displayErrorCard.textContent = '';
                StripeInstanceCard.handleCardPayment(
                    '[[+intent_secret]]', StripeCardCard, {
                        payment_method_data: {
                            billing_details: {
                                name: cardholderName.value,
                                address: {
                                    line1: _getValue('street') ? _getValue('street') + _getValue('number') : _getValue('address1'),
                                    city: _getValue('city'),
                                    postal_code: _getValue('zip'),
                                    country: _getValue('country')
                                },
                                email: _getValue('email')
                            }
                        }
                    }
                ).then(function(result) {
                    if (result.error) {
                        // If we somehow have an already-confirmed payment intent, submit to the server to validate
                        if (result.error.payment_intent && result.error.payment_intent.status === 'succeeded') {
                            submitForm();
                        }
                        else {
                            displayErrorCard.textContent = result.error.message;
                            // Re-enable buttons
                            for (var j = 0; j < btns.length; j++) {
                                btns[j].removeAttribute('disabled');
                            }
                        }
                    } else {
                        submitForm();
                    }
                });

//                 Disable the submit button to prevent repeated clicks
//                $form.find('button').attr('disabled', true);

                // Prevent the form from submitting with the default action
                return false;
            }

            return false;
        });

        function _getValue(field) {
            try {
                var fld = checkoutFormCard.querySelector('input[name=' + field + ']');
                if (fld) {
                    return fld.value
                }
            }
            catch (e) { }
            return '';
        }

        function submitForm() {
            // Insert the token ID into the form so it gets submitted to the server
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'payment_intent');
            hiddenInput.setAttribute('value', '1');
            checkoutFormCard.appendChild(hiddenInput);
            // Submit the form
            checkoutFormCard.submit();
        }
    });
</script>