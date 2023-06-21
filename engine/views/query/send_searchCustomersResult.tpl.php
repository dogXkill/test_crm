<div class="clientsList_items">
	<?php if ($clients) { ?>
		<?php foreach ($clients as $client) { ?>
		<div class="clientList_item">
			<div class="clientList_item_name"><span class="short"><?=StringHelper::highlightSearchString($client['short'], $searchString);?></span> <span class="full">(<?=$client['name'];?>)</span></div>
			<div class="clientList_item_address"><?=$client['legal_address'];?></div>
			<div class="clientList_item_phones clear"><?php QuerySendHelper::printPhones(array($client['cont_tel'], $client['firm_tel'])); ?></div>
			
			<div class="clientList_item_orders"><?php QuerySendHelper::printCustomerSearchQueries($client); ?></div>
			
			<?php QuerySendHelper::printClientDataTag($client); ?>
		</div>
		<?php } ?>
	<?php } ?>
</div>