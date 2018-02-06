<?php

$_lang['simplecart.methods.payment.stripe'] = 'Stripe';
$_lang['simplecart.methods.payment.stripe.desc'] = 'Mit Kreditkarte bezahlen.';
$_lang['simplecart.methods.payment.stripe.orderdesc'] = "Sie haben Ihre Bestellung mit einer Kreditkarte bezahlt. Wir haben die Zahlung erhalten und werden Ihren Auftrag in Kürze bearbeiten.";
$_lang['simplecart.methods.payment.stripebancontact'] = 'Bancontact';
$_lang['simplecart.methods.payment.stripebancontact.desc'] = 'Mit Bancontact bezahlen.';
$_lang['simplecart.methods.payment.stripebancontact.orderdesc'] = "Vielen Dank für Ihre Bezahlung via Bancontact. Wir haben die Zahlung erhalten und werden Ihren Auftrag in Kürze bearbeiten.";
$_lang['simplecart.methods.payment.stripeideal'] = 'iDeal';
$_lang['simplecart.methods.payment.stripeideal.desc'] = 'Online bezahlen mit iDeal.';
$_lang['simplecart.methods.payment.stripeideal.orderdesc'] = "Vielen Dank für Ihre Bezahlung via iDeal. Wir haben die Zahlung erhalten und werden Ihren Auftrag in Kürze bearbeiten.";
$_lang['simplecart_stripe.credit_or_debit'] = 'Bitte geben Sie Ihre Kredit- oder Debitkarten-Informationen ein.';
$_lang['simplecart_stripe.bancontact.account_holder'] = 'Kontoinhaber';
$_lang['simplecart_stripe.ideal.bank'] = 'Wählen Sie Ihre Bank';
$_lang['simplecart_stripe.ideal.abn_amro'] = 'ABN AMRO'; // Name of a bank, shouldn't be necessary to translate
$_lang['simplecart_stripe.ideal.bunq'] = 'Bunq'; // Name of a bank, shouldn't be necessary to translate
$_lang['simplecart_stripe.ideal.asn_bank'] = 'ASN Bank'; // Name of a bank, shouldn't be necessary to translate
$_lang['simplecart_stripe.ideal.ing'] = 'ING'; // Name of a bank, shouldn't be necessary to translate
$_lang['simplecart_stripe.ideal.knab'] = 'Knab'; // Name of a bank, shouldn't be necessary to translate
$_lang['simplecart_stripe.ideal.rabobank'] = 'Rabobank'; // Name of a bank, shouldn't be necessary to translate
$_lang['simplecart_stripe.ideal.regiobank'] = 'RegioBank'; // Name of a bank, shouldn't be necessary to translate
$_lang['simplecart_stripe.ideal.sns_bank'] = 'SNS Bank'; // Name of a bank, shouldn't be necessary to translate
$_lang['simplecart_stripe.ideal.triodos_bank'] = 'Tridos Bank'; // Name of a bank, shouldn't be necessary to translate
$_lang['simplecart_stripe.ideal.van_lanschot'] = 'Van Lanschot'; // Name of a bank, shouldn't be necessary to translate

// properties
$_lang['simplecart.methods.payment.stripe.property_currency'] = "Währung";
$_lang['simplecart.methods.payment.stripe.property_currency.desc'] = "Die Währung. die für Zahlungen im Stripe Konto verwendet wird. Anmerkung: Für Sandbox Modus immer USD verwenden.";

$_lang['simplecart.methods.payment.stripe.property_secret_key'] = "Geheimer Schlüssel";
$_lang['simplecart.methods.payment.stripe.property_secret_key.desc'] = "Geben Sie den geheimen Schlüssel Ihres Stripe Kontos ein. Dies kann entweder der Test- oder Live-API-Key sein.";

$_lang['simplecart.methods.payment.stripe.property_publishable_key'] = "Öffentlicher Schlüssel";
$_lang['simplecart.methods.payment.stripe.property_publishable_key.desc'] = "Geben Sie den öffentlichen Schlüssel Ihres Stripe Kontos ein. Dies kann entweder der Test- oder Live-API-Key sein.";

$_lang['simplecart.methods.payment.stripe.property_cart_tpl'] = "Stripe Warenkorb Tpl";
$_lang['simplecart.methods.payment.stripe.property_cart_tpl.desc'] = "Geben Sie den Namen des Chunks ein welcher für die verschiedenen Felder im Warenkorb/Checkout verwendet werden soll. ";

$_lang['simplecart.methods.payment.stripe.property_cart_footer_tpl'] = "Stripe Footer Tpl";
$_lang['simplecart.methods.payment.stripe.property_cart_footer_tpl.desc'] = "Geben Sie den Namen des Chunks für den Stripe Footer ein, der das Stripe.js Library und die client-seitige Logik für die Zahlung enthält.";
