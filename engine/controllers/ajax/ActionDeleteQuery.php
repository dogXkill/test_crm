<?php

class ActionDeleteQuery extends AbstractAction {
	
	protected $db;
	
	public function run() {
		// Только залогиненные
		if ($this->user->isGuest()) {
			return array('status' => 401);
		}

		$this->db = Database::getInstance();
		$queriesClass = QueriesClass::getInstance();
		
		$queryId = isset($this->request->post['queryId']) ? intval($this->request->post['queryId']) : 0;
		$select_val = isset($this->request->post['select_val']) ? intval($this->request->post['select_val']) : 99;//причина удаления
		$comment=isset($this->request->post['comment']) ? $this->request->post['comment'] : "НЕ УКАЗАНО."; //комментарий
		$query = $queriesClass->getQueryById($queryId);
        $act = $this->request->post['act'];

		if (!$query) {
			return array('status' => 404);
		}



        if($act == 'restore'){

 				// восстановление списка предмета счета
				$this->db->query("UPDATE obj_accounts SET deleted = 0 WHERE query_id = " . $queryId);

				//восстановление полей оплаты для передмета счета
				$this->db->query("UPDATE payment_predm SET deleted = 0 WHERE query_id = " . $queryId);

				//восстановление запроса на счет
				$this->db->query("UPDATE queries SET deleted = 0 WHERE uid = " . $queryId);
				$this->db->query("DELETE FROM `queries_comment` WHERE `queryId` = '{$queryId}'");

        }else{



		$ac_dos=$this->user->order_access_edit();
		//$ac_dos=1;//test
		if ($ac_dos == 2) {
			if ($query['deleted'] != 1) {
			
            /*$contractors_list = $this->db->getCol("SELECT uid FROM contractors_list WHERE query_id = " . $queryId);
				if ($contractors_list) {
					// удаление полей оплаты подрядчиков
					$this->db->query("UPDATE payment_podr SET deleted = 1 WHERE contr_id IN (" . implode(', ', $contractors_list) . ")");
					
					// удаление списка подрядчиков
					$this->db->query("UPDATE contractors_list SET deleted = 1 WHERE query_id = " . $queryId);
				}  */



				// удаление списка предмета счета
				$this->db->query("UPDATE obj_accounts SET deleted = 1 WHERE query_id = " . $queryId);

				//удаление полей оплаты для передмета счета
				$this->db->query("UPDATE payment_predm SET deleted = 1 WHERE query_id = " . $queryId);

				//удаление запроса на счет
				$this->db->query("UPDATE queries SET deleted = 1 WHERE uid = " . $queryId);
				if ($this->db->getVar("SELECT * FROM queries_comment WHERE queryId = {$queryId};")!=false) {
					
					$this->db->query("UPDATE `queries_comment` SET `tip_reason`='{$select_val}',`comment`='{$comment}' WHERE `queryId`= {$queryId};");
				}else{
					
					$this->db->query("INSERT INTO `queries_comment` (`id`, `queryId`, `tip_reason`, `comment`) VALUES (NULL, '{$queryId}', '{$select_val}', '{$comment}');");
				}
			}else{
				if ($this->db->getVar("SELECT * FROM queries_comment WHERE queryId = {$queryId};")!=false) {
					
					$this->db->query("UPDATE `queries_comment` SET `tip_reason`='{$select_val}',`comment`='{$comment}' WHERE `queryId`= {$queryId};");
				}else{
					
					$this->db->query("INSERT INTO `queries_comment` (`id`, `queryId`, `tip_reason`, `comment`) VALUES (NULL, '{$queryId}', '{$select_val}', '{$comment}');");
				}
				
			}
		} else {
			// Если не админ
			// помечаем на удаление если не помечено
			if (!$query['deleted'] && $select_val!=99) {
				$this->db->query("UPDATE queries SET deleted = 2 WHERE uid = " . $queryId);
            // удаление списка предмета счета
				$this->db->query("UPDATE obj_accounts SET deleted = 2 WHERE query_id = " . $queryId);
			}else{
				$res='Выберите причину';
			}
			if ($query['deleted'] == 2) {
				$this->db->query("UPDATE queries SET deleted = 0 WHERE uid = " . $queryId);
			}
			if ($this->db->getVar("SELECT * FROM queries_comment WHERE queryId = {$queryId};")!=false) {
					
					$this->db->query("UPDATE `queries_comment` SET `tip_reason`='{$select_val}',`comment`='{$comment}' WHERE `queryId`= {$queryId};");
				}else{
					
					$this->db->query("INSERT INTO `queries_comment` (`id`, `queryId`, `tip_reason`, `comment`) VALUES (NULL, '{$queryId}', '{$select_val}', '{$comment}');");
				}


		}


      }


		// Возвращаем успешный результат
		return array(
			'status' => 200,
			'order_access_edit' => $ac_dos,
            'act' => $act,
			'message'=>$res
		);
	}
	
}
