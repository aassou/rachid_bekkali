<?php
class ValidationController {

    const NOT_VALID = 0;
    const VALID = 1;
    const EXIST = 2;

    const SOURCES = [
        'businessPlanProductType',
        'businessPlanProduct',
        'businessPlanCostType',
        'businessPlanCost',
        'configurationModules',
        'tranche',
        'bloc',
    ];

    //attributes
    protected $_message;
    protected $_source;
    protected $_target;
    protected $_manager;
    
    //constructor
    public function __construct($source){
        $this->_source = $source;
    }
    
    //getters
    public function getMessage(){
        return $this->_message;
    }
    
    public function getSource(){
        return $this->_source;
    }
    
    public function getTarget(){
        return $this->_target;
    }
    
    //methods
    public function validate($formInputs, $action) {
        //Attestation Object Test Validation Begins
        if ( $this->_source == "attestation" ) {
            if( !empty($formInputs['codeCompagnie']) 
            and !empty($formInputs['numeroDebut']) 
            and !empty($formInputs['numeroFin'])
            and ($formInputs['numeroFin'] - $formInputs['numeroDebut'] + 1 >= 1)
            ){
                $numeroDebut = $formInputs['numeroDebut'];
                $numeroFin = $formInputs['numeroFin'];
                $nombreAttestation = $numeroFin - $numeroDebut +1;
                $nombreUtilise = 0;
                //test if the attestation series number doesn't exist
                $manager = ucfirst($this->_source).'Manager';
                $this->_manager = new $manager(PDOFactory::getMysqlConnection());
                $attestationsElements = $this->_manager->getAll();
                $attestationCondition = 0;
                foreach ( $attestationsElements as $element ) {
                    //If the attestation serie's number does exist already in the DB incerement condition
                    if ( 
                    ( $element->numeroDebut() >= $numeroDebut and $element->numeroDebut() <= $numeroFin )
                    or
                    ( $element->numeroFin() >= $numeroDebut and $element->numeroFin() <= $numeroFin )
                    ) 
                    {
                        $attestationCondition++;        
                    }
                }
                //If the attestationCondition attribute is different than 0, an error should be handeled
                if ( $attestationCondition != 0 ) {
                    $this->_message = "Opération Invalide: Cette série de numéro existe déjà";
                    $this->_target = $this->_source.".php";
                    return self::NOT_VALID;
                }
                //Else, add the new attestation serie's number to our DB
                else {
                    if ( $action == "add" ) {
                        $this->_message = "Opération Valide: Ligne ajoutée avec succès";    
                    }
                    else if ( $action == "update" ) {
                        $this->_message = "Opération Valide: Ligne modifiée avec succès";    
                    }
                    else if ( $action == "delete" ) {
                        $this->_message = "Opération Valide: Ligne suprimmée avec succès";    
                    }
                    $this->_target = $this->_source.".php";
                    return self::VALID;
                }
            } else {
                $this->_message = "Opération Invalide: Veuillez remplir les champs obligatoires";
                $this->_target = $this->_source.".php";
                return self::NOT_VALID;
            }     
        } elseif ($this->_source == "user") {
            //Create UserManager
            $manager = ucfirst($this->_source).'Manager';
            $this->_manager = new $manager(PDOFactory::getMysqlConnection());
            //Action add Begins
            if ($action == "add") {
                if( 
                    !empty($formInputs['login']) 
                    && !empty($formInputs['password']) 
                    && !empty($formInputs['rpassword']) 
                    && ( $formInputs['password'] == $formInputs['rpassword'] ) 
                ) {
                    
                    //test if the user exist
                    if ( $this->_manager->exist2($formInputs['login']) ) {
                        $this->_message = "Opération Invalide : Un utilisateur existe déjà avec ce nom.";
                        $this->_target = $this->_source.".php";
                        return self::NOT_VALID;
                    } else {
                        $this->_message = "Opération Valide : User Ajouté(e) avec succès.";
                        $this->_target = $this->_source.".php";
                        return self::VALID;
                    }
                } else {
                    $this->_message = "Opération Invalide : Vous devez remplir tous les champs correctement.";
                    $this->_target = $this->_source.".php";
                    return self::NOT_VALID;
                }
            } elseif ($action == "login") {
                //Test if the user credentials are set
                //Case 1 : Something missing
                if ( empty($formInputs['login']) || empty($formInputs['password']) ) {
                    $this->_message = "Opération Invalide : Tous les champs sont obligatoires.";
                    $this->_target = $this->_source.".php";
                    return self::NOT_VALID;
                }
                //Case 2 : User's credentials are set
                else{
                    $login = htmlspecialchars($formInputs['login']);
                    $password = htmlspecialchars($formInputs['password']);
                    if ( $this->_manager->exist2($login) && $this->_manager->getStatus($login) != 0 ) {
                        if ( password_verify($password, $this->_manager->getPasswordByLogin($login)) ) {
                            $this->_target = "dashboard.php";
                            return self::VALID;
                        }
                        else{
                            $this->_message = "Opération Invalide : Mot de passe incorrecte.";
                            $this->_target = $this->_source.".php";
                            return self::NOT_VALID;
                        }
                    }
                    else{
                        $this->_message = "Opération Invalide : Login invalide ou compte inactif.";
                        $this->_target = $this->_source.".php";
                        return self::NOT_VALID;
                    }
                }
            }
            //Action login Ends
            //Action updateProfil Begins            
            else if ( $action == "updateProfil" ) {
                if ( !empty($formInputs['id']) && !empty($formInputs['profil']) ) {
                    $this->_message = "Opération Valide : Profil User Modifié(e) avec succès.";
                    $this->_target = $this->_source.".php";
                    return self::VALID;
                }
                else{
                    $this->_message = "Opération Invalide : Profil inexistant.";
                    $this->_target = $this->_source.".php";
                    return self::NOT_VALID;
                }
            }
            //Action updateProfil Ends
            //Action updateStatus Begins
            else if ( $action == "updateStatus" ) {
                if ( !empty($formInputs['id']) ) {
                    $this->_message = "Opération Valide : Status Modifié avec succès.";
                    $this->_target = $this->_source.".php";
                    return self::VALID;
                }
                else{
                    $this->_message = "Opération Invalide : Utilisateur inexistant.";
                    $this->_target = $this->_source.".php";
                    return self::NOT_VALID;
                }
            } 
            //Action updateStatus Ends 
            //Action changePassword Begins
            else if ( $action == "changePassword" ) {
                if ( !empty($formInputs['oldPassword']) 
                and !empty($formInputs['newPassword']) 
                and !empty($formInputs['retypeNewPassword']) ) {
                    if ( password_verify($formInputs['oldPassword'], $this->_manager->getPasswordByLogin($formInputs['login'])) 
                    and ( $formInputs['newPassword'] == $formInputs['retypePassword'] ) ) {
                        $this->_message = "Opération Valide : Mot de passe modifié avec succès.";
                        $this->_target = $this->_source.".php";
                        return self::VALID;
                    }
                    else {
                        $this->_message = "Opération Invalide : Ancien Mot de passe est incorrecte.";
                        $this->_target = $this->_source.".php";
                        return self::NOT_VALID;    
                    }
                }    
            }
            //Action changePassword Ends
        } elseif ( $this->_source == "client" ) {
            $this->_target = "client.php";

            if ($action == "add") {
                if (!empty($formInputs['name'])) {
                    $this->_message = "<strong>Opération Valide : </strong>Client ajouté(e) avec succès.";

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur Création Client: </strong>Vous devez remplir tous les champs obligatoires : <sup>*</sup> correctement.";

                    return self::NOT_VALID;
                }
            } elseif ($action == "update") {
                if (!empty($formInputs['name'])) {
                    $this->_message = "<strong>Opération Valide : </strong>Client modifié(e) avec succès.";

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur Création Client : </strong>Vous devez remplir tous les champs obligatoires : <sup>*</sup> correctement.";

                    return self::NOT_VALID;
                }
            } elseif ($action == "delete") {
                if (!empty($formInputs['id'])) {
                    $this->_message = "<strong>Opération Valide : </strong>Client supprimé(e) avec succès.";

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur suppression Client: </strong>Le champs id est inexistant.";

                    return self::NOT_VALID;
                }
            }
        } elseif ( $this->_source == "businessPlanCost" ) {
            $projectId = $formInputs['idProjet'];
            $this->_target = "businessPlan.php?projectId=$projectId";

            if($action == "add") {
                if (!empty($formInputs['idProjet']) and
                    !empty($formInputs['amount']) and
                    !empty($formInputs['costType']) and
                    $formInputs['amount'] > 0
                ) {
                    $this->_message = "<strong>Opération Valide : </strong>CHARGE ajoutée avec succès.";
                    
                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur Création CHARGE : </strong>Vous devez remplir tous les champs obligatoires : <sup>*</sup> correctement.";

                    return self::NOT_VALID;
                }
            } elseif ($action == "update") {
                if (!empty($formInputs['idProjet']) and
                    !empty($formInputs['amount']) and
                    !empty($formInputs['costType']) and
                    $formInputs['amount'] > 0
                ) {
                    $this->_message = "<strong>Opération Valide : </strong>CHARGE modifiée avec succès.";

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur Création CHARGE : </strong>Vous devez remplir tous les champs obligatoires : <sup>*</sup> correctement.";

                    return self::NOT_VALID;
                }
            } elseif ($action == "delete") {
                if (!empty($formInputs['id'])) {
                    $this->_message = "<strong>Opération Valide : </strong>CHARGE supprimée avec succès.";

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur suppression CHARGE : </strong>Le champs idProjet est inexistant.";

                    return self::NOT_VALID;
                }
            }
        } elseif ( $this->_source == "businessPlanProduct" ) {
            $projectId = $formInputs['idProjet'];
            $this->_target = "businessPlan.php?projectId=$projectId";

            if($action == "add") {
                if (!empty($formInputs['idProjet']) and
                    !empty($formInputs['amount']) and
                    !empty($formInputs['productType']) and
                    $formInputs['amount'] > 0
                ) {
                    $this->_message = "<strong>Opération Valide : </strong>PRODUIT ajouté avec succès.";

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur Création PRODUIT : </strong>Vous devez remplir tous les champs obligatoires : <sup>*</sup> correctement.";

                    return self::NOT_VALID;
                }
            } elseif ($action == "update") {
                if (!empty($formInputs['idProjet']) and
                    !empty($formInputs['amount']) and
                    !empty($formInputs['productType']) and
                    $formInputs['amount'] > 0
                ) {
                    $this->_message = "<strong>Opération Valide : </strong>PRODUIT modifié avec succès.";

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur Création PRODUIT : </strong>Vous devez remplir tous les champs obligatoires : <sup>*</sup> correctement.";

                    return self::NOT_VALID;
                }
            } elseif ($action == "delete") {
                if (!empty($formInputs['id'])) {
                    $this->_message = "<strong>Opération Valide : </strong>PRODUIT supprimée avec succès.";

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur suppression PRODUIT : </strong>Le champs idProjet est inexistant.";

                    return self::NOT_VALID;
                }
            }
        } elseif ( $this->_source == "Sale" ) {
            $this->_target = "sale.php";

            if($action == "add") {
                if (!empty($formInputs['number'])) {
                    $this->_message = "<strong>Opération Valide : </strong>Vente ajouté(e) avec succès.";
                    $this->_target = "saleDetail.php?codeSale=" . $formInputs['code'];

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur Création Vente : </strong>Vous devez remplir tous les champs obligatoires : <sup>*</sup> correctement.";
                    $this->_target = "sale.php";

                    return self::NOT_VALID;
                }
            } elseif ($action == "update") {
                if (!empty($formInputs['number'])) {
                    $this->_message = "<strong>Opération Valide : </strong>Vente modifié(e) avec succès.";

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur Création Vente : </strong>Vous devez remplir tous les champs obligatoires : <sup>*</sup> correctement.";

                    return self::NOT_VALID;
                }
            } elseif ($action == "delete") {
                if (!empty($formInputs['id'])) {
                    $this->_message = "<strong>Opération Valide : </strong>Vente supprimé(e) avec succès.";

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur suppression Vente : </strong>Le champs id est inexistant.";

                    return self::NOT_VALID;
                }
            }
        } elseif ( $this->_source == "SaleDetail" ) {
            $codeSale = $formInputs['codeSale'];
            $this->_target = "saleDetail.php?codeSale=$codeSale";

            if($action == "add") {
                if (!empty($formInputs['codeSale']) and
                    !empty($formInputs['price']) and
                    !empty($formInputs['quantity'])
                ) {
                    $this->_message = "<strong>Opération Valide : </strong>Opération Vente ajouté(e) avec succès.";

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur Création Opération Vente : </strong>Vous devez remplir tous les champs obligatoires : <sup>*</sup> correctement.";

                    return self::NOT_VALID;
                }
            } elseif ($action == "update") {
                if (!empty($formInputs['id']) and
                    !empty($formInputs['codeSale']) and
                    !empty($formInputs['price']) and
                    !empty($formInputs['quantity'])
                ) {
                    $this->_message = "<strong>Opération Valide : </strong>Opération Vente modifié(e) avec succès.";

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur Création Opération Vente : </strong>Vous devez remplir tous les champs obligatoires : <sup>*</sup> correctement.";

                    return self::NOT_VALID;
                }
            } elseif ($action == "delete") {
                if (!empty($formInputs['id'])) {
                    $this->_message = "<strong>Opération Valide : </strong>Opération Vente supprimé(e) avec succès.";

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur suppression Opération Vente : </strong>Le champs id est inexistant.";

                    return self::NOT_VALID;
                }
            }
        } elseif ( $this->_source == "purchase" ) {
            $this->_target = "purchase.php";

            if ($action == "add") {
                if (!empty($formInputs['number'])) {
                    $this->_message = "<strong>Opération Valide : </strong>Achat ajouté(e) avec succès.";
                    $this->_target = "purchaseDetail.php?codePurchase=" . $formInputs['code'];

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur Création Achat : </strong>Vous devez remplir tous les champs obligatoires : <sup>*</sup> correctement.";
                    $this->_target = "purchase.php";

                    return self::NOT_VALID;
                }
            } elseif ($action == "update") {
                if (!empty($formInputs['number'])) {
                    $this->_message = "<strong>Opération Valide : </strong>Achat modifié(e) avec succès.";

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur Création Achat : </strong>Vous devez remplir tous les champs obligatoires : <sup>*</sup> correctement.";

                    return self::NOT_VALID;
                }
            } elseif ($action == "delete") {
                if (!empty($formInputs['id'])) {
                    $this->_message = "<strong>Opération Valide : </strong>Vente supprimé(e) avec succès.";

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur suppression Vente : </strong>Le champs id est inexistant.";

                    return self::NOT_VALID;
                }
            }
        } elseif ( $this->_source == "purchaseDetail" ) {
            $codePurchase = $formInputs['codePurchase'];
            $this->_target = "purchaseDetail.php?codePurchase=$codePurchase";

            if ($action == "add") {
                if (!empty($formInputs['codePurchase']) and
                    !empty($formInputs['price']) and
                    !empty($formInputs['quantity'])
                ) {
                    $this->_message = "<strong>Opération Valide : </strong>Opération Achat ajouté(e) avec succès.";

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur Création Opération Achat : </strong>Vous devez remplir tous les champs obligatoires : <sup>*</sup> correctement.";

                    return self::NOT_VALID;
                }
            } elseif ($action == "update") {
                if (!empty($formInputs['id']) and
                    !empty($formInputs['codePurchase']) and
                    !empty($formInputs['price']) and
                    !empty($formInputs['quantity'])
                ) {
                    $this->_message = "<strong>Opération Valide : </strong>Opération Achat modifié(e) avec succès.";

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur Création Opération Achat : </strong>Vous devez remplir tous les champs obligatoires : <sup>*</sup> correctement.";

                    return self::NOT_VALID;
                }
            } elseif ($action == "delete") {
                if (!empty($formInputs['id'])) {
                    $this->_message = "<strong>Opération Valide : </strong>Opération Achat supprimé(e) avec succès.";

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur suppression Opération Achat : </strong>Le champs id est inexistant.";

                    return self::NOT_VALID;
                }
            }
        } elseif ( $this->_source == "provider" ) {
            $this->_target = "provider.php";

            if ($action == "add") {
                if (!empty($formInputs['name'])) {
                    $this->_message = "<strong>Opération Valide : </strong>Fournisseur ajouté(e) avec succès.";

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur Création Fournisseur: </strong>Vous devez remplir tous les champs obligatoires : <sup>*</sup> correctement.";

                    return self::NOT_VALID;
                }
            } elseif ($action == "update") {
                if (!empty($formInputs['name'])) {
                    $this->_message = "<strong>Opération Valide : </strong>Fournisseur modifié(e) avec succès.";

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur Création Fournisseur : </strong>Vous devez remplir tous les champs obligatoires : <sup>*</sup> correctement.";

                    return self::NOT_VALID;
                }
            } elseif ($action == "delete") {
                if (!empty($formInputs['id'])) {
                    $this->_message = "<strong>Opération Valide : </strong>Fournisseur supprimé(e) avec succès.";

                    return self::VALID;
                } else {
                    $this->_message = "<strong>Erreur suppression Fournisseur: </strong>Le champs id est inexistant.";

                    return self::NOT_VALID;
                }
            }
        } else {
            $this->_target = $this->_source.".php";

            if (in_array($this->_source, self::SOURCES)) {
                if ($action == 'add') {
                    $this->_message = "Opération Valide: Ligne ajoutée avec succès";

                    return self::VALID;
                } elseif ($action == 'update') {
                    if (!empty($formInputs['id'])) {
                        $this->_message = "Opération Valide: Ligne modifiée avec succès";

                        return self::VALID;
                    } else {
                        $this->_message = "Opération Invalide: Vous devez remplir tous les champs!";

                        return self::NOT_VALID;
                    }
                } elseif ($action == 'delete') {
                    if (!empty($formInputs['id'])) {
                        $this->_message = "Opération Valide: Ligne supprimée avec succès";

                        return self::VALID;
                    } else {
                        $this->_message = sprintf("Opération Invalide: Le champs id%s est manquant", $this->_source);

                        return self::NOT_VALID;
                    }
                }
            } else {
                $this->_message = "Opération Invalide: Cette source est n'existe pas!";

                return self::NOT_VALID;
            }
        }
    }
}