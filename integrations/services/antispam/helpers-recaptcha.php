<?php

if ( ! function_exists( 'happyforms_recaptcha' ) ) :

function happyforms_recaptcha( $form ) {
	$has_captcha = apply_filters( 'happyforms_form_has_captcha', false, $form );
	if ( $has_captcha ): ?>
	<div class="happyforms-form__part happyforms-part happyforms-part--recaptcha happyforms-recaptcha-v<?php echo happyforms_get_recaptcha_version(); ?>" data-sitekey="<?php echo $form['captcha_site_key']; ?>" data-happyforms-type="recaptcha_v<?php echo happyforms_get_recaptcha_version(); ?>" data-theme="<?php echo isset( $form['captcha_theme'] ) ? $form['captcha_theme'] : ''; ?>">
		<?php if ( 2 === happyforms_get_recaptcha_version() ) : ?>
			<label for="g-recaptcha-response" class="happyforms-part__label">
				<span class="label"><?php echo $form['captcha_label']; ?></span>
			</label>
		<?php endif; ?>
		<div class="happyforms-part-wrap" id="happyforms-<?php echo $form['ID']; ?>-recaptcha"></div>

		<?php
		if ( 2 === happyforms_get_recaptcha_version() ) {
			happyforms_part_error_message( happyforms_get_recaptcha_part_name( $form ) );
		}
		?>
	</div>
	<?php endif;
}

endif;

if ( ! function_exists( 'happyforms_get_recaptcha_locales' ) ):

function happyforms_get_recaptcha_locales() {
	$locales = array(
		'ar', 'af', 'am', 'hy', 'az', 'eu',
		'bn', 'bg', 'ca', 'zh-HK', 'zh-CN',
		'zh-TW', 'hr', 'cs', 'da', 'nl', 'en-GB',
		'en', 'et', 'fil', 'fi', 'fr', 'fr-CA',
		'gl', 'ka', 'de', 'de-AT', 'de-CH', 'el',
		'gu', 'iw', 'hi', 'hu', 'is', 'id', 'it',
		'ja', 'kn', 'pl', 'pt', 'pt-BR', 'pt-PT',
		'ro', 'ru', 'sr', 'si', 'sk', 'sl', 'es',
		'es-419', 'sw', 'sv', 'ta', 'te', 'th',
		'tr', 'uk', 'ur', 'vi', 'zu'
	);

	return $locales;
}

endif;

if ( ! function_exists( 'happyforms_get_recaptcha_locale' ) ):

function happyforms_get_recaptcha_locale() {
	$wp_locale = get_locale();
	$locale = preg_replace( '/[-_]+.+/m', '', $wp_locale );
	$locales = happyforms_get_recaptcha_locales();
	$locale = in_array( $locale, $locales ) ? $locale : '';
	$locale = apply_filters( 'happyforms_recaptcha_locale', $locale, $wp_locale );

	return $locale;
}

endif;

if ( ! function_exists( 'happyforms_get_recaptcha_part_name' ) ) :

	function happyforms_get_recaptcha_part_name( $form ) {
		return "happyforms-{$form['ID']}-recaptcha";
	}

endif;

if ( ! function_exists( 'happyforms_get_recaptcha_version' ) ) :

	function happyforms_get_recaptcha_version() {
		$version        = null;
		$active_service = happyforms_get_antispam_integration()->get_active_service();

		if ( ! $active_service ) {
			return $version;
		}

		if ( 'recaptcha' === $active_service->id ) {
			$version = 2;
		} else if ( 'recaptchav3' === $active_service->id ) {
			$version = 3;
		}

		return $version;
	}

endif;
