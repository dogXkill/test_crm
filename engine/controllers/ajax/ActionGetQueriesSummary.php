<?php

class ActionGetQueriesSummary extends AbstractAction {

	protected $db;

	public function run() {
		// Только залогиненные могут выполнить
		if ($this->user->isGuest()) {
			return array('status' => 401);
		}

		$this->db = Database::getInstance();
		$queriesClass = QueriesClass::getInstance();

		$managers = UsersClass::getInstance()->getManagers();

		$filter = isset($this->request->post['filter']) ? $this->request->post['filter'] : false;

		$processedFilters = QueriesClass::processFilters($filter, compact('managers'));
		$args = $processedFilters['args'];
		$filters = $processedFilters['filters'];

		if (!$this->user->isAdmin()) {
			$args['manager'] = $this->user->getId();
			if (isset($filters['manager'])) {
				unset($filters['manager']);
			}

			if (isset($args['search'])) {
				unset($args['manager']);
			}

			if (isset($filters['printManager'])) {
				unset($filters['printManager']);
			}
		}

		$summary = $queriesClass->getQueries(array_merge($args, array('getSummary' => true)));

		if ($summary) {
		 	$summary['amount'] = MoneyHelper::format($summary['amount']);
		 	$summary['paid'] = MoneyHelper::format($summary['paid']);
		  	$summary['debt'] = MoneyHelper::format($summary['debt']);
		}

		return array(
			'status' => 200,
			'summary' => $summary
		);

	}

}
