<?php if ($customers) { ?>
    <?php foreach ($customers as $customer) { ?>
        <tr id="customer_<?= $customer['uid']; ?>" data-customer-id="<?= $customer['uid']; ?>" data-user-id="<?= $customer['user_id']; ?>"
            data-order-type="<?= $customer['order_type']; ?>">
            <td class="col-clientShort">
                <a href="/acc/query/?search=<?= $customer['short']; ?>" target="_blank"><?= $customer['short']; ?>
                </a>
            </td>
            <td>
                <select style="width: 150px; background: transparent; border: 1px solid black;"
                        onchange="customer.action('update',{ item: { uid: <?= $customer['uid'] ?>, status_id: this.value }})"
                >
                    <?php foreach (CustomersCallsConfig::$customerStatuses as $key => $value): ?>
                        <?php $selected = (int) $key === (int) $customer['status_id'] ? 'selected' : '' ?>
                        <option <?= $selected ?> value="<?= $key ?>">
                            <?= $value ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td class="col-clientFull">
				
				<!--ФИО-->
				<?php if (!empty($customer['cont_pers'])){ ?>
                    <input style="width: 85%;" type="text" value="<?= $customer['cont_pers'] ?>" class="cont_pers"
					onblur="if (customer.cont_pers !== this.value) customer.persChange(<?= $customer['uid'] ?>, this);"
					/>
                    <?php } ?>
				<!--email-->
				<div style="margin-top: 5px;">
				<?php if (!empty($customer['email'])){ ?>
                    <input style="width: 85%;" type="text" value="<?= $customer['email'] ?>" class="cont_email"
					onblur="if (customer.email !== this.value) customer.persEmail(<?= $customer['uid'] ?>, this);"
					/>
                    <?php } ?>
				</div>
					<!--telefon-->
                <div style="margin-top: 5px;">
                    <?php if (!empty($customer['cont_tel']) && strlen($customer['cont_tel']) >= 11) { ?>
                    <input style="width: 100px;" type="text" value="<?= $customer['cont_tel'] ?>" class="cont_tel"
                           data-phone-type="cont_tel"
                           onfocus="customer.cont_tel = this.value;"
                           onblur="if (customer.cont_tel !== this.value) customer.telephoneChange(<?= $customer['uid'] ?>, this);"
                    />
                    <?php } ?>

                    <?php if (!empty($customer['firm_tel']) && strlen($customer['firm_tel']) >= 11) { ?>
                    <input style="width: 100px;" type="text" value="<?= $customer['firm_tel'] ?>" class="firm_tel"
                           data-phone-type="firm_tel"
                           onfocus="customer.firm_tel = this.value;"
                           onblur="if (customer.firm_tel !== this.value) customer.telephoneChange(<?= $customer['uid'] ?>, this);"
                    />
                    <?php } ?>
                </div>
            </td>
            <td class="col-manager" align="center"><?php echo isset($managers[$customer['user_id']])
                    ? $managers[$customer['user_id']]['surname'] : '—'; ?></td>
            <td class="col-orders" align="center"><?php CustomersCallsHelper::printOrderCountCell($customer); ?></td>
            <td class="col-lastOrder" align="center"><?php CustomersCallsHelper::printLastOrderCell($customer); ?></td>
            <td class="col-orderType"><?php CustomersCallsHelper::printOrderTypeCell($customer); ?></td>
            <td class="col-lastCall" align="center"><?php CustomersCallsHelper::printLastCallCell($customer); ?></td>
            <td class="col-actions"><span class="actionViewCalls"><i
                            class="fas fa-phone-alt"></i> <span><?= $customer['calls_count']; ?></span></span></td>
        </tr>
    <?php } ?>
<?php } else { ?>
    <tr class="emptyList">
        <td colspan="8">Нет данных</td>
    </tr>
<?php } ?>
