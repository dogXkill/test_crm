<?php

class actionCancelPercentage extends AbstractAction {

    protected $db;

    public function run() {
        // ������ ������������ ����� ���������
        if ($this->user->isGuest()) {
            return array('status' => 401);
        }

        // return $this->request->post;

        $this->db = Database::getInstance();
        $queriesClass = QueriesClass::getInstance();

        $queryId = isset($this->request->post['queryId']) ? intval($this->request->post['queryId']) : 0;
        $value = isset($this->request->post['value']) ? intval($this->request->post['value']) : 0;
        $value = $value ? 1 : 0;

        $query = $queriesClass->getQueryById($queryId);

        if (!$query) {
            return array(
                'status' => 600,
                'message' => '������� � ID=' . $queryId . ' �� ����������.'
            );
        }

        // ��������� �����
        $dbQuery = sprintf("UPDATE queries SET CancelPercentage = %d WHERE uid = %d", $value, $query['uid']);
        $this->db->query($dbQuery);

        // ���������� �������� ���������
        return array(
            'status' => 200,
            'value' => $value
        );
    }

}
