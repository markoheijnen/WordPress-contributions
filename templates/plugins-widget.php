<?php
/**
 * WordPress plugins Widget Template.
 *
 * @since     1.0
 */
?>

		<?php if ( isset( $items ) ) : ?>
			<ul>
			<?php foreach ( (array) $items as $item ) : ?>
				<li>
					<?php echo '<a href="http://wordpress.org/plugins/' . $item->slug . '">' . $item->name . '</a>'; ?>
				</li>
			<?php endforeach; ?>
			</ul>
		<?php endif; ?>
