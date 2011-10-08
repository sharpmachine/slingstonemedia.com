<?php if (shopp('cart','hasitems')): ?>
<div id="cart" class="shopp">
<table>
	<tr>
		<th scope="col" class="item">Cart Items</th>
		<th scope="col">Quantity</th>
		<th scope="col" class="money">Item Price</th>
		<th scope="col" class="money">Item Total</th>
	</tr>

	<?php while(shopp('cart','items')): ?>
		<tr>
			<td>
				<a href="<?php shopp('cartitem','url'); ?>"><?php shopp('cartitem','name'); ?></a>
				<?php shopp('cartitem','options','show=selected&before= (&after=)'); ?>
				<?php shopp('cartitem','inputs-list'); ?>
				<?php shopp('cartitem','addons-list'); ?>
			</td>
			<td><?php shopp('cartitem','quantity'); ?></td>
			<td class="money"><?php shopp('cartitem','unitprice'); ?></td>
			<td class="money"><?php shopp('cartitem','total'); ?></td>
		</tr>
	<?php endwhile; ?>
	
	<?php while(shopp('cart','promos')): ?>
		<tr><td colspan="4" class="money"><?php shopp('cart','promo-name'); ?><strong><?php shopp('cart','promo-discount',array('before' => '&nbsp;&mdash;&nbsp;')); ?></strong></td></tr>
	<?php endwhile; ?>
	
	<tr class="totals">
		<td colspan="2" rowspan="5">
			<?php if ((shopp('cart','has-shipping-methods'))): ?>
			<small>Select a shipping method:</small>
			
			<form action="<?php shopp('shipping','url') ?>" method="post">
			
			<ul id="shipping-methods">
			<?php while(shopp('shipping','methods')): ?>
				<li><span><label><?php shopp('shipping','method-selector'); ?>
				<?php shopp('shipping','method-name'); ?> &mdash;
				<strong><?php shopp('shipping','method-cost'); ?></strong><br />
				<small><?php shopp('shipping','method-delivery'); ?></small></label></span>
				</li>
			<?php endwhile; ?>
			</ul>
			</form>
			
			<?php endif; ?>
		</td>
		<th scope="row">Subtotal</th>
		<td class="money"><?php shopp('cart','subtotal'); ?></td>
	</tr>
	<?php if (shopp('cart','hasdiscount')): ?>
	<tr class="totals">
		<th scope="row">Discount</th>
		<td class="money">-<?php shopp('cart','discount'); ?></td>
	</tr>
	<?php endif; ?>
	<?php if (shopp('cart','needs-shipped')): ?>
	<tr class="totals">
		<th scope="row"><?php shopp('cart','shipping','label=Shipping'); ?></th>
		<td class="money"><?php shopp('cart','shipping'); ?></td>
	</tr>
	<?php endif; ?>
	<tr class="totals">
		<th scope="row"><?php shopp('cart','tax','label=Taxes'); ?></th>
		<td class="money"><?php shopp('cart','tax'); ?></td>
	</tr>
	<tr class="totals total">
		<th scope="row">Total</th>
		<td class="money"><?php shopp('cart','total'); ?></td>
	</tr>
</table>

<?php if(shopp('checkout','hasdata')): ?>
	<ul>
	<?php while(shopp('checkout','orderdata')): ?>
		<li><strong><?php shopp('checkout','data','name'); ?>:</strong> <?php shopp('checkout','data'); ?></li>
	<?php endwhile; ?>
	</ul>
<?php endif; ?>

</div>
<?php else: ?>
	<p class="warning">There are currently no items in your shopping cart.</p>
<?php endif; ?>
