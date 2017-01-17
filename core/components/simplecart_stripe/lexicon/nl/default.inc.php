<?php

$_lang['simplecart.methods.payment.stripe'] = "Stripe";
$_lang['simplecart.methods.payment.stripe.desc'] = "Betaal uw order veilig met een Credit Card.";
$_lang['simplecart.methods.payment.stripe.orderdesc'] = "Uw betaling is met een Credit Card betaald. Wij hebben de betaling succesvol ontvangen en zullen uw order spoedig verwerken.";

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
