<?php

    //classes loading begin
    function classLoad ($myClass) {
        if(file_exists('../model/'.$myClass.'.php')){
            include('../model/'.$myClass.'.php');
        }
        elseif(file_exists('../controller/'.$myClass.'.php')){
            include('../controller/'.$myClass.'.php');
        }
    }
    spl_autoload_register("classLoad"); 
    include('../config.php');  
    include('../lib/image-processing.php');
    //classes loading end
    session_start();
    
    //post input processing
    $action = htmlentities($_POST['action']);
    //This var contains result message of CRUD action
    $actionMessage = "";
    $typeMessage = "";
    $redirectLink = "Location:../produits.php";
    //Component Class Manager

    $produitManager = new ProduitManager($pdo);
	//Action Add Processing Begin
    if($action == "add"){
        if( !empty($_POST['code']) ){
			$prixAchat = htmlentities($_POST['prixAchat']);
			$prixVente = htmlentities($_POST['prixVente']);
			$prixVenteMin = htmlentities($_POST['prixVenteMin']);
			$quantite = htmlentities($_POST['quantite']);
			$code = htmlentities($_POST['code']);
			$idCategorie = htmlentities($_POST['idCategorie']);
			$createdBy = $_SESSION['userMerlaTrav']->login();
            $created = date('Y-m-d h:i:s');
            //create object
            $produit = new Produit(array(
				'prixAchat' => $prixAchat,
				'prixVente' => $prixVente,
				'prixVenteMin' => $prixVenteMin,
				'quantite' => $quantite,
				'code' => $code,
				'idCategorie' => $idCategorie,
				'created' => $created,
            	'createdBy' => $createdBy
			));
            //add it to db
            $produitManager->add($produit);
            $actionMessage = "Opération Valide : Produit Ajouté(e) avec succès.";  
            $typeMessage = "success";
        }
        else{
            $actionMessage = "Erreur Ajout produit : Vous devez remplir le champ 'dimension'.";
            $typeMessage = "error";
        }
    }
    //Action Add Processing End
    //Action Update Processing Begin
    else if($action == "update"){
        $idProduit = htmlentities($_POST['idProduit']);
        if(!empty($_POST['code'])){
			$prixAchat = htmlentities($_POST['prixAchat']);
			$prixVente = htmlentities($_POST['prixVente']);
			$prixVenteMin = htmlentities($_POST['prixVenteMin']);
			$quantite = htmlentities($_POST['quantite']);
			$code = htmlentities($_POST['code']);
			$idCategorie = htmlentities($_POST['idCategorie']);
			$updatedBy = $_SESSION['userMerlaTrav']->login();
            $updated = date('Y-m-d h:i:s');
            $produit = new Produit(array(
				'id' => $idProduit,
				'prixAchat' => $prixAchat,
				'prixVente' => $prixVente,
				'prixVenteMin' => $prixVenteMin,
				'quantite' => $quantite,
				'code' => $code,
				'idCategorie' => $idCategorie,
				'updated' => $updated,
            	'updatedBy' => $updatedBy
			));
            $produitManager->update($produit);
            $actionMessage = "Opération Valide : Produit Modifié(e) avec succès.";
            $typeMessage = "success";
        }
        else{
            $actionMessage = "Erreur Modification Produit : Vous devez remplir le champ 'dimension'.";
            $typeMessage = "error";
        }
    }
    //Action Update Processing End
    //Action UpdateQuantite Processing Begin
    else if($action == "updateQuantite"){
        $idProduit = htmlentities($_POST['idProduit']);
        $quantite = htmlentities($_POST['quantite']);
        $updatedBy = $_SESSION['userMerlaTrav']->login();
        $updated = date('Y-m-d h:i:s');
        $produitManager->updateQuantite($idProduit, $quantite);
            ///$actionMessage = "Opération Valide : Produit Modifié(e) avec succès.";
            ///$typeMessage = "success";
        ///}
        ///else{
           /// $actionMessage = "Erreur Modification Produit : Vous devez remplir le champ 'dimension'.";
           /// $typeMessage = "error";
        ///}
    }
    //Action UpdateQuantite Processing End
    //Action Delete Processing Begin
    else if($action == "delete"){
        $idProduit = htmlentities($_POST['idProduit']);
        $produitManager->delete($idProduit);
        $actionMessage = "Opération Valide : Produit supprimé(e) avec succès.";
        $typeMessage = "success";
    }
    //Action Delete Processing End
    $_SESSION['produit-action-message'] = $actionMessage;
    $_SESSION['produit-type-message'] = $typeMessage;
    //redirectLink processing
    if ( isset($_POST['source']) and $_POST['source'] == "stock" ) {
        $redirectLink = "Location:../stock.php";
    }
    else if ( isset($_POST['source']) and $_POST['source'] == "produits" ) {
        $redirectLink = "Location:../produits.php";
    }    
    header($redirectLink);

