<script type="text/javascript" src="https://js.stripe.com/v3/"></script>
<script type="text/javascript">
    var StripeInstance[[+method_id]] = Stripe('[[+publishable_key]]'),
        StripeElements[[+method_id]] = StripeInstance[[+method_id]].elements(),
        displayError[[+method_id]] = document.getElementById('sc-stripe-[[+method_id]]-card-errors'),
        checkoutForm[[+method_id]] = document.getElementById('simplecartCheckout');

    document.addEventListener("DOMContentLoaded", function(event) {
        // Create the Card element
        var StripeCard[[+method_id]] = StripeElements[[+method_id]].create('card');

        StripeCard[[+method_id]].mount('#sc-stripe-[[+method_id]]-card-element');
        StripeCard[[+method_id]].addEventListener('change', function(event) {
            if (event.error) {
                displayError[[+method_id]].textContent = event.error.message;
            } else {
                displayError[[+method_id]].textContent = '';
            }
        });

        checkoutForm[[+method_id]].addEventListener('submit', function(event) {
            if (checkoutForm[[+method_id]].paymentMethod.value == [[+method_id]]) {

                event.preventDefault();

                StripeInstance[[+method_id]].createToken(StripeCard[[+method_id]]).then(function(result) {
                    if (result.error) {
                        // Inform the user if there was an error
                        displayError[[+method_id]].textContent = result.error.message;
                    } else {
                        // Send the token to your server
                        stripeTokenHandler[[+method_id]](result.token);
                    }
                });

//                 Disable the submit button to prevent repeated clicks
//                $form.find('button').attr('disabled', true);

                // Prevent the form from submitting with the default action
                return false;
            }

            return false;
        });

        function stripeTokenHandler[[+method_id]](token) {
            // Insert the token ID into the form so it gets submitted to the server
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            checkoutForm[[+method_id]].appendChild(hiddenInput);
            // Submit the form
            checkoutForm[[+method_id]].submit();
        }
    });
</script>