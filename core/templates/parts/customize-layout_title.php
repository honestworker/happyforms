<script type="text/template" id="customize-happyforms-layout_title-template">
	<div class="happyforms-widget happyforms-part-widget" data-part-id="<%= instance.id %>">
		<div class="happyforms-widget-top happyforms-part-widget-top">
			<div class="happyforms-part-widget-title-action">
				<button type="button" class="happyforms-widget-action">
					<span class="toggle-indicator"></span>
				</button>
			</div>
			<div class="happyforms-widget-title">
				<h3><%= settings.label %><span class="in-widget-title"<% if (!instance.label) { %> style="display: none"<% } %>>: <span><%= (instance.label) ? instance.label : '' %></span></span></h3>
			</div>
		</div>
		<div class="happyforms-widget-content">
			<div class="happyforms-widget-form">
				<p>
					<label for="<%= instance.id %>_title"><?php _e( 'Heading', 'happyforms' ); ?></label>
					<input type="text" id="<%= instance.id %>_title" class="widefat title" value="<%- instance.label %>" data-bind="label" />
				</p>
				<p>
					<label for="<%= instance.id %>_level"><?php _e( 'Heading level', 'happyforms' ); ?></label>
					<span class="happyforms-buttongroup">
					    <?php for ( $i = 1; $i <= 6; $i++ ): ?>
						    <label for="<%= instance.id %>-level-h<?php echo $i; ?>">
						        <input type="radio" id="<%= instance.id %>-level-h<?php echo $i; ?>" value="h<?php echo $i; ?>" name="<%= instance.id %>-level" data-bind="level" <%= ( instance.level == 'h<?php echo $i; ?>' ) ? 'checked' : '' %> />
						        <span><?php _e( 'H' . $i, 'happyforms' ); ?></span>
						    </label>
					    <?php endfor; ?>
					</span>
				</p>

				<?php happyforms_customize_part_width_control(); ?>

				<?php do_action( 'happyforms_part_customize_layout_title_after_advanced_options' ); ?>

				<p>
					<label for="<%= instance.id %>_css_class"><?php _e( 'Additional CSS class(es)', 'happyforms' ); ?></label>
					<input type="text" id="<%= instance.id %>_css_class" class="widefat title" value="<%- instance.css_class %>" data-bind="css_class" />
				</p>

				<div class="happyforms-part-logic-wrap">
					<div class="happyforms-logic-view">
						<?php happyforms_customize_part_logic(); ?>
					</div>
				</div>
			<?php happyforms_customize_part_footer(); ?>
</script>