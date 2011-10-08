<div id="shopp-cart-ajax">
<?php if (shopp('cart','hasitems')): ?>
<ul>

		<li id="shopp-sidecart-items"><?php shopp('cart','totalitems'); ?> <strong>Items</strong></li>
		<li id="shopp-sidecart-total" class="money"><?php shopp('cart','total'); ?> <strong>Total</strong></li>
</ul><br \>
	<ul>
		<li>&raquo; <a href="<?php shopp('cart','url'); ?>">Edit shopping cart</a></li>
		<?php if (shopp('checkout','local-payment')): ?>
		<li>&raquo; <a href="<?php shopp('checkout','url'); ?>">Proceed to Checkout</a></li>
		<?php endif; ?>
	</ul>
<?php else: ?>
	<p class="status">Your cart is empty.</p>
<?php endif; ?>
</div>
