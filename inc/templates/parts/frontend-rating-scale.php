<?php
    $label_class = ( 'stars' === $part['rating_visuals'] ) ? 'happyforms-star__label' : '';
    $rating_labels = $part['rating_labels_scale'];

    for ( $i = $part['stars_num']; $i >= 1; $i-- ) {
    ?>
    <input class="happyforms-visuallyhidden" type="radio" value="<?php echo esc_attr( $i ); ?>" id="<?php echo esc_attr( $part['id'] ); ?>_<?php echo esc_attr( $i ); ?>" name="<?php happyforms_the_part_name( $part, $form ); ?>" <?php checked( happyforms_get_part_value( $part, $form ), $i ); ?> <?php happyforms_the_part_attributes( $part, $form ); ?> />
    <label class="<?php echo $label_class; ?>" for="<?php echo esc_attr( $part['id'] ); ?>_<?php echo esc_attr( $i ); ?>">
        <?php if ( 'stars' === $part['rating_visuals'] ) { ?>
            <span class="happyforms-visuallyhidden"><?php echo esc_attr( $i ); ?> <?php _e( 'Stars', 'happyforms' ); ?></span>
            <svg class="happyforms-star" enable-background="new 0 0 24 24" viewBox="0 0 24 24"><path class="happyforms-star__star" d="M 14.43,10 12,2 9.57,10 2,10 8.18,14.41 5.83,22 12,17.31 18.18,22 15.83,14.41 22,10z"/></svg>
        <?php } else { ?>
            <span class="happyforms-rating__item-wrap">
                <?php echo $icons[$i-1]; ?>
                <span class="happyforms-rating__item-label"><?php echo ( ! empty( $rating_labels[$i-1] ) ) ? $rating_labels[$i-1] : '<span class="happyforms-visuallyhidden">' . sprintf( __( '%d out of %d', 'happyforms' ), $i, $part['stars_num'] ) .'</span>'; ?></span>
            </span>
        <?php } ?>
    </label>
<?php } ?>
