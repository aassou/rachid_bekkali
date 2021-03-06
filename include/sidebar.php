<?php
    $currentPage = basename($_SERVER['PHP_SELF']);
?>
<div class="page-sidebar nav-collapse collapse">
    <ul>
        <li>
            <div class="sidebar-toggler hidden-phone"></div>
        </li>
        <li>
        </li>
        <!---------------------------- Dashboard Begin  -------------------------------------------->
        <li class="start <?php if($currentPage=="dashboard.php"
        ){echo "active ";} ?>">
            <a href="dashboard.php">
            <i class="icon-dashboard"></i>
            <span class="title">Accueil</span>
            </a>
        </li>
        <!---------------------------- Dashboard End    -------------------------------------------->
        <!---------------------------- Gestion Factures Begin ----------------------------------->
        <?php
        if (
            $_SESSION['userstock']->profil() == "admin" ||
            $_SESSION['userstock']->profil() == "manager" ||
            $_SESSION['userstock']->profil() == "consultant"
            ) {
            $gestionAchatClass="";
            if($currentPage=="factures.php"
            or $currentPage=="facture-details.php"
            ){
                $gestionAchatClass = "active ";
            }
        ?>
<!--        <li class="--><?php //$gestionAchatClass ?><!--" >-->
<!--            <a href="factures.php">-->
<!--            <i class="icon-file"></i>-->
<!--            <span class="title">Factures</span>-->
<!--            </a>-->
<!--        </li>-->
        <?php
        }
        ?>
        <!---------------------------- Gestion Factures End -------------------------------------->
        <!---------------------------- Gestion Achats Begin ----------------------------------->
        <?php
        if (
            $_SESSION['userstock']->profil() == "admin" ||
            $_SESSION['userstock']->profil() == "manager" ||
            $_SESSION['userstock']->profil() == "consultant"
            ) {
            $gestionAchatClass="";
            if($currentPage == "purchase.php"
                or $currentPage == "purchaseDetail.php"
            ){
                $gestionAchatClass = "active ";
            }
        ?>
        <li class="<?= $gestionAchatClass; ?>" >
            <a href="purchase.php">
            <i class="icon-shopping-cart"></i>
            <span class="title">Achats</span>
            </a>
        </li>
        <?php
        }
        ?>
        <!---------------------------- Gestion Achats End -------------------------------------->
        <!---------------------------- Gestion Ventes Begin ----------------------------------->
        <?php
        if (
            $_SESSION['userstock']->profil() == "admin" ||
            $_SESSION['userstock']->profil() == "manager" ||
            $_SESSION['userstock']->profil() == "consultant"
        ) {
            $gestionAchatClass="";
            if ($currentPage == "sale.php"
                or $currentPage == 'saleDetail.php'
            ){
                $gestionAchatClass = "active ";
            }
            ?>
            <li class="<?= $gestionAchatClass; ?>" >
                <a href="Sale.php">
                    <i class="icon-signal"></i>
                    <span class="title">Ventes</span>
                </a>
            </li>
            <?php
        }
        ?>
        <!---------------------------- Gestion Ventes End -------------------------------------->
        <!---------------------------- Gestion Stock Begin  -------------------------------------------->
        <?php
        if (
            $_SESSION['userstock']->profil() == "admin" ||
            $_SESSION['userstock']->profil() == "manager" ||
            $_SESSION['userstock']->profil() == "consultant"
            ) {
            $gestionStockClass="";
            if($currentPage=="stock.php"
            or $currentPage=="stock-update-produit.php"
            or $currentPage=="stock-delete-produit.php"
            or $currentPage=="warehouse.php"
            ){
                $gestionStockClass = "active ";
            }
        ?>
        <li class="<?= $gestionStockClass ?>" >
            <a href="stock.php">
            <i class="icon-bar-chart"></i>
            <span class="title">Stock</span>
            </a>
        </li>
        <?php
        }
        ?>
        <!---------------------------- Gestion Stock End    -------------------------------------------->
        <!---------------------------- Gestion Clients Begin  -------------------------------------------->
        <?php
        if (
            $_SESSION['userstock']->profil() == "admin" ||
            $_SESSION['userstock']->profil() == "manager" ||
            $_SESSION['userstock']->profil() == "consultant"
        ) {
            $gestionClientsClass="";
            if ($currentPage == "client.php") {
                $gestionClientsClass = "active ";
            }
        ?>
        <li class="<?= $gestionClientsClass ?>">
            <a href="client.php">
            <i class="icon-group"></i>
            <span class="title">Clients</span>
            </a>
        </li>
        <?php
        }
        ?>
        <!---------------------------- Gestion Clients End    -------------------------------------------->
        <!---------------------------- Gestion Fournisseurs Begin  -------------------------------------------->
        <?php
        if (
            $_SESSION['userstock']->profil() == "admin" ||
            $_SESSION['userstock']->profil() == "manager" ||
            $_SESSION['userstock']->profil() == "consultant"
        ) {
            $gestionFournisseurClass="";
            if ($currentPage == "provider.php") {
                $gestionFournisseurClass = "active ";
            }
            ?>
            <li class="<?= $gestionFournisseurClass ?>" >
                <a href="provider.php">
                    <i class="icon-truck"></i>
                    <span class="title">Fournisseurs</span>
                </a>
            </li>
            <?php
        }
        ?>
        <!---------------------------- Gestion Fournisseurs End    -------------------------------------------->
        <!---------------------------- Gestion Charges Begin  -------------------------------------------->
        <?php
        if (
            $_SESSION['userstock']->profil() == "admin" ||
            $_SESSION['userstock']->profil() == "manager" ||
            $_SESSION['userstock']->profil() == "consultant"
            ) {
            $gestionChargesClass = "";
            if($currentPage == "caisse-group.php"
                or $currentPage == "caisse-mois-annee.php"
            ) {
                $gestionChargesClass = "active ";
            }
        ?>
        <li class="<?= $gestionChargesClass ?>" >
            <a href="caisse-group.php">
            <i class="icon-money"></i>
            <span class="title">La Caisse</span>
            </a>
        </li>
        <?php
        }
        ?>
        <!---------------------------- Gestion Charges End    -------------------------------------------->
        <!---------------------------- Parametrage Begin  -------------------------------------------->
        <?php
        if (
            $_SESSION['userstock']->profil() == "admin" ||
            $_SESSION['userstock']->profil() == "manager" ||
            $_SESSION['userstock']->profil() == "consultant"
            ) {
            $gestionParametragesClass="";
            if($currentPage=="configuration.php"
            or $currentPage=="clients-list.php"
            or $currentPage=="categories.php"
            or $currentPage=="produits.php"
            or $currentPage=="produit-update.php"
            or $currentPage=="produit-delete.php"
            ){
                $gestionParametragesClass = "active ";
            }
        ?>
        <li class="<?= $gestionParametragesClass ?>" >
            <a href="configuration.php">
            <i class="icon-wrench"></i>
            <span class="title">Paramètrages</span>
            </a>
        </li>
        <?php
        }
        ?>
        <!---------------------------- Gestion Parametrage End    -------------------------------------------->
    </ul>
</div>