<?php

$_lang['simplecart.methods.payment.stripe'] = "Stripe";
$_lang['simplecart.methods.payment.stripe.desc'] = "Bezahlen Sie Ihre Bestellung mit Kreditkarte.";
$_lang['simplecart.methods.payment.stripe.orderdesc'] = "Sie haben die Bestellung mit Kreditkarte abgeschlossen. Wir haben die Zahlung erhalten und Ihre Bestellung wird verschickt.";

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
