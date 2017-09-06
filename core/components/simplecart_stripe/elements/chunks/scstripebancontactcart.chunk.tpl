<div class="sc-stripe-bancontact-gateway">
    <div>
        <label for="sc-payment-[[+method_id]]-accountholder">[[%simplecart_stripe.bancontact.account_holder]]: <span>*</span></label>
        <input type="text" name="account_holder" id="sc-payment-[[+method_id]]-accountholder" value="[[+fi.firstname]] [[+fi.lastname]]" required />
        <label class="error">[[+fi.error.account_holder]]</label>
    </div>
</div>