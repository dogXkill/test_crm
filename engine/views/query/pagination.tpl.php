<?php
	
	$total = $count;
	
	if ($total > 1) {
		
		if ($total > 5 && $current > 4) {
			echo '<a href="#" data-page="1" class="first">1</a>' . PHP_EOL;
			echo '<span class="separator">...</span>' . PHP_EOL;
		}
		
		if ($current - 2 <= 2) {
			$start = 1;
		} else {
			if ($current != 4 && $current != $total - 3 && $current > $total - 4) {
				$start = $total - 4;
			} else {
				$start = $current - 2;
			}
		}

		if ($start < 1) $start = 1;

		if ($current + 2 >= $total) {
			$end = $total;
		} else {
			if ($current < 4) {
				$end = 5;
			} else {
				$end = $current + 2;
			}
		}

		for ($i = $start; $i <= $end; $i++) {
			if ($i == $current) {
				echo '<span class="current">' . $i . '</span>' . PHP_EOL;
			} else {
				echo '<a href="#" data-page="' . $i . '">' . $i . '</a>' . PHP_EOL;
			}
		}
		
		if ($total > 5 && $current < $total - 2) {
			if ($current != $total - 3) { 
				echo '<span class="separator">...</span>' . PHP_EOL;
			}
			echo '<a href="#" data-page="' . $total . '" class="last">' . $total . '</a>' . PHP_EOL;
		} 
	}
	