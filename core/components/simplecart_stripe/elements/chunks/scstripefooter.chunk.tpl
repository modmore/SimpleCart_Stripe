<script type="text/javascript" src="https://js.stripe.com/v3/"></script>
<script type="text/javascript">
    var StripeInstanceCard = Stripe('[[+publishable_key]]'),
        StripeElementsCard = StripeInstanceCard.elements(),
        displayErrorCard = document.getElementById('sc-stripe-[[+method_id]]-card-errors'),
        checkoutFormCard = document.getElementById('simplecartCheckout');

    document.addEventListener("DOMContentLoaded", function(event) {
        // Create the Card element
        var StripeCardCard = StripeElementsCard.create('card');

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

                StripeInstanceCard.createSource(StripeCardCard).then(function(result) {
                    if (result.error) {
                        // Inform the user if there was an error
                        displayErrorCard.textContent = result.error.message;
                    } else {
                        // Send the token to your server
                        stripeSourceHandlerCard(result.source);
                    }
                });

//                 Disable the submit button to prevent repeated clicks
//                $form.find('button').attr('disabled', true);

                // Prevent the form from submitting with the default action
                return false;
            }

            return false;
        });

        function stripeSourceHandlerCard(source) {
            // Insert the token ID into the form so it gets submitted to the server
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeSource');
            hiddenInput.setAttribute('value', source.id);
            checkoutFormCard.appendChild(hiddenInput);
            // Submit the form
            checkoutFormCard.submit();
        }
    });
</script>