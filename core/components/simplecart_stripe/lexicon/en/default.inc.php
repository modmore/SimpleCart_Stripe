<?php

$_lang['simplecart.methods.payment.stripe'] = 'Stripe';
$_lang['simplecart.methods.payment.stripe.desc'] = 'Pay by Credit Card.';
$_lang['simplecart.methods.payment.stripe.orderdesc'] = "You've paid your order with a Credit Card. We successfully received the payment and your order will be processed soon.";
$_lang['simplecart.methods.payment.stripebancontact'] = 'Bancontact';
$_lang['simplecart.methods.payment.stripebancontact.desc'] = 'Pay with Bancontact.';
$_lang['simplecart.methods.payment.stripebancontact.orderdesc'] = "Thank you for your Bancontact payment. We've received the payment and your order will be processed soon.";
$_lang['simplecart.methods.payment.stripeideal'] = 'iDeal';
$_lang['simplecart.methods.payment.stripeideal.desc'] = 'Pay online with iDeal.';
$_lang['simplecart.methods.payment.stripeideal.orderdesc'] = "Thank you for your iDeal payment. We've received the payment and your order will be processed soon.";
$_lang['simplecart_stripe.credit_or_debit'] = 'Please enter your credit or debit card information.';
$_lang['simplecart_stripe.bancontact.account_holder'] = 'Account Holder';
$_lang['simplecart_stripe.ideal.bank'] = 'Choose your Bank';
$_lang['simplecart_stripe.ideal.abn_amro'] = 'ABN AMRO'; // Name of a bank, shouldn't be necessary to translate
$_lang['simplecart_stripe.ideal.bunq'] = 'Bunq'; // Name of a bank, shouldn't be necessary to translate
$_lang['simplecart_stripe.ideal.asn_bank'] = 'ASN Bank'; // Name of a bank, shouldn't be necessary to translate
$_lang['simplecart_stripe.ideal.ing'] = 'ING'; // Name of a bank, shouldn't be necessary to translate
$_lang['simplecart_stripe.ideal.knab'] = 'Knab'; // Name of a bank, shouldn't be necessary to translate
$_lang['simplecart_stripe.ideal.rabobank'] = 'Rabobank'; // Name of a bank, shouldn't be necessary to translate
$_lang['simplecart_stripe.ideal.regiobank'] = 'RegioBank'; // Name of a bank, shouldn't be necessary to translate
$_lang['simplecart_stripe.ideal.sns_bank'] = 'SNS Bank'; // Name of a bank, shouldn't be necessary to translate
$_lang['simplecart_stripe.ideal.triodos_bank'] = 'Triodos Bank'; // Name of a bank, shouldn't be necessary to translate
$_lang['simplecart_stripe.ideal.van_lanschot'] = 'Van Lanschot'; // Name of a bank, shouldn't be necessary to translate

// properties
$_lang['simplecart.methods.payment.stripe.property_currency'] = "Currency";
$_lang['simplecart.methods.payment.stripe.property_currency.desc'] = "The currency used to pay inside Stripe. Note: should always be USD for Sandbox Mode.";

$_lang['simplecart.methods.payment.stripe.property_secret_key'] = "Secret Key";
$_lang['simplecart.methods.payment.stripe.property_secret_key.desc'] = "Enter the Secret Key from your Stripe account. This can be both a test or live API Key.";

$_lang['simplecart.methods.payment.stripe.property_publishable_key'] = "Publishable Key";
$_lang['simplecart.methods.payment.stripe.property_publishable_key.desc'] = "Enter the Publishable Key from your Stripe account. This can be both a test or live API Key.";

$_lang['simplecart.methods.payment.stripe.property_cart_tpl'] = "Stripe Cart Tpl";
$_lang['simplecart.methods.payment.stripe.property_cart_tpl.desc'] = "Enter the name of a chunk to use for the various Stripe fields in the Cart/Checkout. ";

$_lang['simplecart.methods.payment.stripe.property_cart_footer_tpl'] = "Stripe Footer Tpl";
$_lang['simplecart.methods.payment.stripe.property_cart_footer_tpl.desc'] = "Enter the name of a chunk to use for the Stripe footer, which contains the Stripe.js library and the client-side logic for the payment.";
