<?php

$fields = array(
	'sc-so-field1' => array(
		'type' => 'text',
		'name' => 'currency',
		'label' => 'Currency',
		'description' => 'The currency used to pay with Stripe. ',
		'default' => '',
	),
	'sc-so-field2' => array(
		'type' => 'text',
		'name' => 'secret_key',
		'label' => 'Secret Key',
		'description' => 'Enter the Secret Key provided by Stripe.',
		'default' => '',
	),
	'sc-so-field3' => array(
		'type' => 'text',
		'name' => 'publishable_key',
		'label' => 'Publishable Key',
		'description' => 'Enter the Publishable Key provided by Stripe.',
		'default' => '',
	),
);

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
		
		$output = '<div style="width:100%;min-width:100%;margin:-15px;">
		<table cellspacing="0" cellpadding="0" style="width:100%;">
		<p><b>IMPORTANT: After installing, you\'ll need to <a href="https://docs.modmore.com/en/SimpleCart/v2.x/Payment_Methods/StripeV2.html#page_Setting+up+the+Gateway" target="_blank" rel="noopener">set up some additional options</a></b> before you can accept payments. <b>This also applies to upgrades from version 1!</b></p>';

		$i = 1;
		foreach ($fields as $id => $attr) {
			
			if ($i % 2 == 1) {
				$output .= '<tr>';
			}
			
			$output .= '<td style="width:5%;">&nbsp;</td>';
			$output .= '<td valign="top" style="width:45%;vertical-align:top;">';
			$output .= '<div class="x-form-item " tabindex="' . $i . '" id="ext-gen-' . $id . '">
				<label for="ext-comp-' . $id . '" style="width:97%;" class="x-form-item-label" id="ext-gen-' . $id . '">' . $attr['label'] . ':</label>
				<div class="x-form-element" id="x-form-el-ext-comp-' . $id . '" style="width:97%;padding-left:0;">';
			
			switch ($attr['type']) {
				case 'select':
					$output .= '<select name="" class="x-form-text x-form-field modx-combo x-form-focus" style="width:100%;height:35px;margin-top:-2px;">';
					foreach ($attr['choices'] as $val => $label) {
						$selected = ($val == $attr['default']) ? ' selected="selected"' : '';
						$output .= '<option value="' . $val . '"' . $selected . '>' . $label . '</option>';
					}
					$output .= '</select>';
					break;
					
				case 'text':
				default:
					$output .= '<input type="text" name="' . $attr['name'] . '"' . ((isset($attr['default']) && !empty($attr['default'])) ? ' value="' . $attr['default'] . '"' : '') . ' autocomplete="on" msgtarget="under" id="ext-comp-' . $id . '" class="x-form-text x-form-field x-form-text-field" style="width:100%;">';
					break;
			}
			$output .= '</div>
				<div class="x-form-clear-left"></div>
			</div>';
			
			if (isset($attr['description']) && !empty($attr['description'])) {
				$output .= '<label for="ext-comp-' . $id . '" class=" desc-under" style="padding-top:8px; width:98%;">' . $attr['description'] . '</label>';
			}
			
			$output .= '</td>';
			
			if ($i % 2 == 0) {
				$output .= '</tr>';
			}
			
			$i++;
		}

		$output .= '</table>
		</div>

		<!-- currently in MODX 2.3.2-pl its not yet possible to fire javascript -->
		<script type="text/javascript">
			var win = Ext.getCmp("modx-window-setupoptions");
				win.config.autoHeight = true;
				win.setWidth(750);
				win.render();
				win.center();
		</script>';

		break;
		
    case xPDOTransport::ACTION_UPGRADE:
    case xPDOTransport::ACTION_UNINSTALL:
		// nothing yet
		break;
}

return $output;