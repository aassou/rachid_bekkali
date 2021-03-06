<?php

require_once('../app/AppController.php');

/**
 * Class SaleActionController
 */
class SaleActionController extends AppController {

    /**
     * @param $formInputs
     */
    public function add($formInputs)
    {
        $this->_formInputs = $formInputs;
        $code = uniqid().date('YmdHis');
        $this->_formInputs['code'] = $code;
        $this->_formInputs['created'] = date('Y-m-d h:i:s');
        $this->_formInputs['createdBy'] = $_SESSION['userstock']->login();

        $this->_validation->validate($this->_formInputs, $this->_formInputs['action']);
        $sale = new Sale($this->_formInputs);
        $this->_manager->add($sale);
        $this->_actionMessage = $this->_validation->getMessage();
        $this->_typeMessage = "success";
        $this->_source = $this->_validation->getTarget();
    }
    /**
     * @return mixed
     */
    public function getSaleNumberPerWeek() {
        return $this->_manager->getSaleNumberPerWeek();
    }
}
