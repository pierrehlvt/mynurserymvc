<?php


namespace App\Controller;

use App\Model\RateModel;
use App\Weblitzer\Controller;
use App\Model\CrecheModel;
use App\Service\Validation;
use App\Model\ProfilModel;
use App\Weblitzer\Model;

class NurseController extends Controller
{
    public function nurse() {
        if(empty($_SESSION)) {
            $this->redirect('404');
        }

        $titre = 'Les crèches près de chez vous';
        $creches = CrecheModel::All();



        $this->render('app.default.nurse.nurses',array(
            'titre' => $titre,
            'creches' => $creches,
        ));
    }

    public function nurseall() {
        $titre = 'Liste de toutes les crèches';
        $creches = CrecheModel::All();

        $this->render('app.default.nurse.nurseall',array(
            'titre' => $titre,
            'creches' => $creches,
        ));
    }

    public function singlenurse($id) {
        $creche = CrecheModel::findById($id, 'id_creche');
        $checkUserRate = RateModel::checkRate($id);
        $userid = $_SESSION['login']['id'];
        if (empty($creche)) {
            $this->Abort404();
        }


        $errors = array();
        if (!empty($_POST['submitted']) && $userid != $checkUserRate && !empty($_SESSION)) {
            $post = $this->cleanXss($_POST);
            $valid = new Validation();
            if ($valid->IsValid($errors)){
                RateModel::rateNurse($userid, $id, $post['rate']);
                //$this->redirect('');
            }
        }
        $note = round(RateModel::avgRateNurse($id),2);
        $this->render('app.default.nurse.singlenurse',array(
            'creche' => $creche,
            'note' => $note,
        ));
    }


}