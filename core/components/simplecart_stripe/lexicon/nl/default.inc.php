<?php

$_lang['simplecart.methods.payment.stripe'] = 'Stripe';
$_lang['simplecart.methods.payment.stripe.desc'] = 'Betaal met Credit Card.';
$_lang['simplecart.methods.payment.stripe.orderdesc'] = "Uw bestelling is betaald met een credit card. De betaling is succesvol ontvangen, en uw order zullen we spoedig verwerken.";
$_lang['simplecart.methods.payment.stripebancontact'] = 'Bancontact';
$_lang['simplecart.methods.payment.stripebancontact.desc'] = 'Betaal met Bancontact.';
$_lang['simplecart.methods.payment.stripebancontact.orderdesc'] = "Bedankt voor uw Bancontact betaling. Uw betaling is ontvangen en uw bestelling wordt spoedig verwerkt.";
$_lang['simplecart.methods.payment.stripeideal'] = 'iDeal';
$_lang['simplecart.methods.payment.stripeideal.desc'] = 'Online betalen met iDeal.';
$_lang['simplecart.methods.payment.stripeideal.orderdesc'] = "Bedankt voor uw iDeal betaling. Uw betaling is ontvangen en uw bestelling wordt spoedig verwerkt.";
$_lang['simplecart_stripe.credit_or_debit'] = 'Voer uw credit of debit card informatie in.';
$_lang['simplecart_stripe.bancontact.account_holder'] = 'Rekeninghouder';
$_lang['simplecart_stripe.ideal.bank'] = 'Kies uw bank';
$_lang['simplecart_stripe.ideal.abn_amro'] = 'ABN Amro'; // Name of a bank, shouldn't be necessary to translate
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
$_lang['simplecart.methods.payment.stripe.property_currency'] = "Valuta";
$_lang['simplecart.methods.payment.stripe.property_currency.desc'] = "De valuta om te gebruiken met Stripe. In sandbox modus moet dit vrijwel altijd USD zijn.";

$_lang['simplecart.methods.payment.stripe.property_secret_key'] = "Secret Key";
$_lang['simplecart.methods.payment.stripe.property_secret_key.desc'] = "Voer de Secret Key in voor uw Stripe account. Dit kan zowel een test of live API Key zijn.";

$_lang['simplecart.methods.payment.stripe.property_publishable_key'] = "Publishable Key";
$_lang['simplecart.methods.payment.stripe.property_publishable_key.desc'] = "Voer de Publishable Key in van uw Stripe account. Dit kan zowel een test of een live API Key zijn.";

$_lang['simplecart.methods.payment.stripe.property_cart_tpl'] = "Stripe Cart Template";
$_lang['simplecart.methods.payment.stripe.property_cart_tpl.desc'] = "De naam van een chunk om te gebruiken voor de verschillende Stripe velden in de cart/checkout. ";

$_lang['simplecart.methods.payment.stripe.property_cart_footer_tpl'] = "Stripe Footer Template";
$_lang['simplecart.methods.payment.stripe.property_cart_footer_tpl.desc'] = "De naam van een chunk om te gebruiken voor de Stripe footer, waarin onder andere de Stripe.js library zich bevindt en ook verdere client-side logica voor de betaling.";
