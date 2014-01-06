<?php
/**
 * GitHub repo Widget Template.
 *
 * @since     1.0
 */
?>

		<?php if ( isset( $items ) ) : ?>
			<ul>
			<?php foreach ( (array) $items as $item ) : ?>
				<li>
					<?php echo '<a href="' . $item->html_url . '">' . $item->name . '</a>'; ?>
				</li>
			<?php endforeach; ?>
			</ul>
		<?php endif; ?>
