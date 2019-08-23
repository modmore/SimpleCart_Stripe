<div class="sc-stripe-gateway">
    <div class="sc-stripe-field sc-stripe-field-holder">
        <label for="sc-stripe-[[+method_id]]-cardholder">[[%simplecart_stripe.card_holder]]:</label>
        <input type="text" id="sc-stripe-[[+method_id]]-cardholder" size="40">
    </div>
    <label for="sc-stripe-[[+method_id]]-card-element">
        [[%simplecart_stripe.credit_or_debit]]
    </label>
    <div id="sc-stripe-[[+method_id]]-card-element"></div>
    <div id="sc-stripe-[[+method_id]]-card-errors" class="payment-errors" role="alert"></div>
</div>