<div id="fixContainer">
	<?php
		$db = Database::getInstance();
	
		if (!isset($_GET['field'])) {
			?>
			<div style="margin: 0 0 20px;">
				<a href="/fix/showDoubles.php">Дубли по INN</a> |
				<a href="/fix/showDoubles.php?by=email">Дубли по email</a> |
				<a href="/fix/showDoubles.php?by=temp_phone">Дубли по обработанным телефонам</a> |
				<a href="/fix/showDoubles.php?by=rs_acc">Дубли по rs_acc</a> |
			</div>
			<?php
			if (isset($_GET['by'])) {
				if ($_GET['by'] == 'email') {
					$field = 'email';
					$q = 'SELECT COUNT(*) AS `Rows`, email AS qfield 
							FROM clients
							GROUP BY qfield
							HAVING Rows > 1 AND qfield != "" AND qfield IS NOT NULL
							ORDER BY qfield';
				} elseif ($_GET['by'] == 'temp_phone') {
					$field = 'temp_phone';
					$q = 'SELECT COUNT(*) AS `Rows`, temp_phone AS qfield 
							FROM clients
							GROUP BY qfield
							HAVING Rows > 1 AND qfield != "" AND qfield IS NOT NULL
							ORDER BY qfield';
				} elseif ($_GET['by'] == 'rs_acc') {
					$field = 'rs_acc';
					$q = 'SELECT COUNT(*) AS `Rows`, rs_acc AS qfield 
							FROM clients
							GROUP BY qfield
							HAVING Rows > 1 AND qfield != "" AND qfield IS NOT NULL
							ORDER BY qfield';
				}
			} else {
				$field = 'inn';
				if (isset($_GET['bads_inn'])) {
					$q = 'SELECT COUNT(*) AS `Rows`, inn AS qfield
							FROM clients
							WHERE LENGTH(qfield) != 12 AND LENGTH(qfield) != 10
							GROUP BY qfield
							ORDER BY qfield';
				} else {
					$q = 'SELECT COUNT(*) AS `Rows`, inn AS qfield
							FROM clients
							GROUP BY qfield
							HAVING Rows > 1 AND qfield != "" AND qfield IS NOT NULL
							ORDER BY qfield';
				}
			}
			
			$doubles = $db->getRows($q);

			if ($doubles) {
				$vals = array_column($doubles, 'Rows');
				echo 'Количество удалений: ' . (array_sum($vals) - count($vals));
				
				foreach ($doubles as $double) {
					?>
					<div class="item clear">
						<div class="count" style="float: left;"><?php echo $double['Rows']; ?></div>
						<div class="inn" style="float: left;"><a target="_blank" href="/fix/showDoubles.php?field=<?php echo $field; ?>&value=<?php echo urlencode($double['qfield']); ?>"><?php echo htmlspecialchars($double['qfield']); ?></a></div>
					</div>
					<?php
				}

			} else {
				echo 'Нет результатов';
			}
		}
		
		if (isset($_GET['field'])) {
			echo '<div id="listPage">';
			$field = $_GET['field'];
			$value = $_GET['value'];
			// $inn = $_GET['inn'];
			
			$clients = $db->getRows("SELECT c.*, (SELECT COUNT(uid) FROM queries q WHERE q.client_id = c.uid) AS orders, (SELECT date_query FROM queries q1 WHERE q1.client_id = c.uid ORDER BY date_query DESC LIMIT 1) AS last_order, EXISTS(SELECT 1 FROM clients_merged_fields WHERE client_id = c.uid) AS merged FROM clients c WHERE c." . $field . " = '" . $db->esc($value) . "' ORDER BY last_order");
			echo $value;
			?>
			<div class="listItemHeader clear">
				<div class="col_uid">UID</div>
				<div class="col_short">short</div>
				<div class="col_name">name</div>
				<div class="col_legal_address">legal_address</div>
				<div class="col_postal_address">postal_address</div>
				<div class="col_inn">inn</div>
				<div class="col_kpp">kpp</div>
				<div class="col_rs_acc">rs_acc</div>
				<div class="col_cont_pers">cont_pers</div>
				<div class="col_cont_tel">cont_tel</div>
				<div class="col_firm_tel">firm_tel</div>
				<div class="col_email">email</div>
				<div class="col_ordersCount">orders</div>
				<!-- <div class="col_merged">&nbsp;</div> -->
			</div>
			<?php
			
			foreach ($clients as $client) {
				$merged_fields = array();
				
				if ($client['merged']) {
					$res = $db->getRows("SELECT * FROM clients_merged_fields WHERE client_id = " . intval($client['uid']));
					foreach ($res as $val) {
						if (!isset($merged_fields[$val['field']])) {
							$merged_fields[$val['field']] = array();
						}
						
						$merged_fields[$val['field']][] = $val['value'];
					}
				}
				
				?>
				<div class="listItem clear">
					<div class="col_uid"><?php echo $client['uid']; ?><?php if ($client['merged']) echo '!'; ?></div>
					<div class="col_short">
						<?php echo $client['short']; ?>
						<?php showDoublesFields('short', $merged_fields); ?>
					</div>
					<div class="col_name">
						<?php echo $client['name']; ?>
						<?php showDoublesFields('name', $merged_fields); ?>
					</div>
					<div class="col_legal_address"><?php echo $client['legal_address']; ?><?php showDoublesFields('legal_address', $merged_fields); ?></div>
					<div class="col_postal_address"><?php echo $client['postal_address']; ?><?php showDoublesFields('postal_address', $merged_fields); ?></div>
					<div class="col_inn"><a href="/fix/showDoubles.php?field=inn&value=<?php echo htmlspecialchars($client['inn']); ?>"><?php echo htmlspecialchars($client['inn']); ?></a><?php showDoublesFields('inn', $merged_fields); ?></div>
					<div class="col_kpp"><?php echo $client['kpp']; ?><?php showDoublesFields('kpp', $merged_fields); ?></div>
					<div class="col_rs_acc"><a href="/fix/showDoubles.php?field=rs_acc&value=<?php echo htmlspecialchars($client['rs_acc']); ?>"><?php echo $client['rs_acc']; ?></a><?php showDoublesFields('rs_acc', $merged_fields); ?></div>
					<div class="col_cont_pers"><?php echo $client['cont_pers']; ?><?php showDoublesFields('cont_pers', $merged_fields); ?></div>
					<div class="col_cont_tel"><?php echo $client['cont_tel']; ?><?php showDoublesFields('cont_tel', $merged_fields); ?></div>
					<div class="col_firm_tel"><?php echo $client['firm_tel']; ?><?php showDoublesFields('firm_tel', $merged_fields); ?></div>
					<div class="col_email"><a href="/fix/showDoubles.php?field=email&value=<?php echo htmlspecialchars($client['email']); ?>"><?php echo htmlspecialchars($client['email']); ?></a><?php showDoublesFields('email', $merged_fields); ?></div>
					<div class="col_ordersCount">
						<?php echo $client['orders']; ?>
						<?php if (intval($client['orders']) > 0) { ?>
						<br/>
						<?php echo date('d.m.Y', strtotime($client['last_order'])); ?>
						<?php } ?>
					</div>
					<!-- <div class="col_merged"><?php echo $client['merged'] ? 1 : ''; ?></div> -->
				</div>
				<?php
			}
			
			echo '</div>';
		}
		
	?>
	
</div>

<?php
	function showDoublesFields($field, $mergedFields) {
		if (isset($mergedFields[$field])) {
			foreach ($mergedFields[$field] as $value) {
				echo '<div style="font-size: 10px;color: #ff9090;border-bottom: 1px solid #ff9090; padding: 2px 0;">';
				if ($field == 'email') {
					echo '<a href="/fix/showDoubles.php?field=email&value=' . $value . '">' . $value . '</a>';
				} else {
					echo $value;
				}
				
				echo '</div>';
			}
		}
	}
?>