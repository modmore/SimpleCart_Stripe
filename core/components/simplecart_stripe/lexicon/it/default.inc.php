<?php

$_lang['simplecart.methods.payment.stripe'] = "Stripe";
$_lang['simplecart.methods.payment.stripe.desc'] = "Pay your order with a Credit Card.";
$_lang['simplecart.methods.payment.stripe.orderdesc'] = "You've paid your order with a Credit Card. We successfully have received the payment and your order will be shipped.";

// properties
$_lang['simplecart.methods.payment.stripe.property_currency'] = "Currency";
$_lang['simplecart.methods.payment.stripe.property_currency.desc'] = "The currency used to pay inside Stripe. Note: always should be USD for Sandbox Mode.";

$_lang['simplecart.methods.payment.stripe.property_secret_key'] = "Secret Key";
$_lang['simplecart.methods.payment.stripe.property_secret_key.desc'] = "Enter the Secret Key from your Stripe account. This can be both a test or live API Key.";

$_lang['simplecart.methods.payment.stripe.property_publishable_key'] = "Publishable Key";
$_lang['simplecart.methods.payment.stripe.property_publishable_key.desc'] = "Enter the Publishable Key from your Stripe account. This can be both a test or live API Key.";

$_lang['simplecart.methods.payment.stripe.property_cart_tpl'] = "Stripe Cart Tpl";
$_lang['simplecart.methods.payment.stripe.property_cart_tpl.desc'] = "Enter the name of a chunk to use for the various Stripe fields in the Cart/Checkout. ";

$_lang['simplecart.methods.payment.stripe.property_cart_footer_tpl'] = "Stripe Footer Tpl";
$_lang['simplecart.methods.payment.stripe.property_cart_footer_tpl.desc'] = "Enter the name of a chunk to use for the Stripe footer, which contains the Stripe.js library and the client-side logic for the payment.";
