<div class="sc-stripe-ideal-gateway">
    <div>
        <label for="sc-payment-[[+method_id]]-bank">[[%simplecart_stripe.ideal.bank]]: <span>*</span></label>
        <select name="ideal_bank" id="sc-payment-[[+method_id]]-bank" required>
            <option value="abn_amro">[[%simplecart_stripe.ideal.abn_amro]]</option>
            <option value="bunq">[[%simplecart_stripe.ideal.bunq]]</option>
            <option value="asn_bank">[[%simplecart_stripe.ideal.asn_bank]]</option>
            <option value="ing">[[%simplecart_stripe.ideal.ing]]</option>
            <option value="knab">[[%simplecart_stripe.ideal.knab]]</option>
            <option value="rabobank">[[%simplecart_stripe.ideal.rabobank]]</option>
            <option value="regiobank">[[%simplecart_stripe.ideal.regiobank]]</option>
            <option value="sns_bank">[[%simplecart_stripe.ideal.sns_bank]]</option>
            <option value="triodos_bank">[[%simplecart_stripe.ideal.triodos_bank]]</option>
            <option value="van_lanschot">[[%simplecart_stripe.ideal.van_lanschot]]</option>
        </select>
        <label class="error">[[+fi.error.ideal_bank]]</label>
    </div>
</div>