<?php
require_once('../app/classLoad.php');
session_start();

if (isset($_SESSION['userstock'])) {
    // Controllers
    $purchaseActionController = new PurchaseActionController('purchase');
    $saleActionController = new SaleActionController('sale');
    $clientActionController = new ClientActionController('client');

    // Legacy calls
    $usersManager = new UserManager(PDOFactory::getMysqlConnection());
    $productManager = new ProduitManager(PDOFactory::getMysqlConnection());
    $caisseManager = new CaisseManager(PDOFactory::getMysqlConnection());

    // objs and vars
    $minimumStockProducts = $productManager->getMinimumStockProducts();
    $soldeWeek = $caisseManager->getSoldeWeekByType('Entree') - $caisseManager->getSoldeWeekByType('Sortie');
    $purchaseNumberWeek = $purchaseActionController->getPurchaseNumberPerWeek();
    $saleNumberWeek = $saleActionController->getSaleNumberPerWeek();
    $clientNumberWeek = $clientActionController->getNumberWeek();

    $breadcrumb = new Breadcrumb([]);
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
    <head>
        <?php include('../include/header.php') ?>
    </head>
    <body class="fixed-top">
        <div class="header navbar navbar-inverse navbar-fixed-top">
            <?php include("../include/top-menu.php"); ?>
        </div>
        <div class="page-container row-fluid sidebar-closed">
            <?php include("../include/sidebar.php"); ?>
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row-fluid">
                        <?= $breadcrumb->getBreadcrumb() ?>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="tiles">
                                <!-- <a href="factures.php">
                                <div class="tile bg-dark-cyan">
                                    <div class="corner"></div>
                                    <div class="tile-body">
                                        <i class="icon-file"></i>
                                    </div>
                                    <div class="tile-object">
                                        <div class="name">
                                            Factures
                                        </div>
                                    </div>
                                </div>
                                </a> -->
                                <a href="purchase.php">
                                <div class="tile bg-blue">
                                    <div class="corner"></div>
                                    <div class="tile-body">
                                        <i class="icon-shopping-cart"></i>
                                    </div>
                                    <div class="tile-object">
                                        <div class="name">
                                            Achats
                                        </div>
                                    </div>
                                </div>
                                </a>
                                <a href="sale.php">
                                <div class="tile bg-cyan">
                                    <div class="corner"></div>
                                    <div class="tile-body">
                                        <i class="icon-signal"></i>
                                    </div>
                                    <div class="tile-object">
                                        <div class="name">
                                            Ventes
                                        </div>
                                    </div>
                                </div>
                                </a>
                                <a href="produits.php">
                                    <div class="tile bg-dark-blue">
                                        <div class="corner"></div>
                                        <div class="tile-body">
                                            <i class="icon-barcode"></i>
                                        </div>
                                        <div class="tile-object">
                                            <div class="name">
                                                Produits
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <a href="stock.php">
                                <div class="tile bg-brown">
                                    <div class="corner"></div>
                                    <div class="tile-body">
                                        <i class="icon-bar-chart"></i>
                                    </div>
                                    <div class="tile-object">
                                        <div class="name">
                                            Stock
                                        </div>
                                    </div>
                                </div>
                                </a>
                                <a href="provider.php">
                                    <div class="tile bg-green">
                                        <div class="tile-body">
                                            <i class="icon-truck"></i>
                                        </div>
                                        <div class="tile-object">
                                            <div class="name">
                                                Fournisseurs
                                            </div>
                                            <div class="number">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <a href="client.php">
                                    <div class="tile bg-purple">
                                        <div class="tile-body">
                                            <i class="icon-group"></i>
                                        </div>
                                        <div class="tile-object">
                                            <div class="name">
                                                Clients
                                            </div>
                                            <div class="number">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <a href="caisse-group.php">
                                <div class="tile bg-grey">
                                    <div class="corner"></div>
                                    <div class="tile-body">
                                        <i class="icon-money"></i>
                                    </div>
                                    <div class="tile-object">
                                        <div class="name">
                                            Caisse
                                        </div>
                                    </div>
                                </div>
                                </a>
                                <a href="configuration.php">
                                <div class="tile bg-red">
                                    <div class="corner"></div>
                                    <div class="tile-body">
                                        <i class="icon-wrench"></i>
                                    </div>
                                    <div class="tile-object">
                                        <div class="name">
                                            Paramètrages
                                        </div>
                                    </div>
                                </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!--      BEGIN TILES      -->
                    <!-- BEGIN DASHBOARD STATS -->
                    <h4 class="breadcrumb"><i class="icon-file"></i> Raccourcis Facture</h4>
                    <div class="row-fluid">
                        <form target="_blank" id="new-facture" action="../controller/FactureActionController.php" method="POST" class="form-horizontal">
                            <div class="row-fluid">
                                <div class="span6">
                                    <div class="control-group">
                                        <label class="control-label">Date</label>
                                        <div class="controls">
                                            <div class="input-append date date-picker" data-date="" data-date-format="yyyy-mm-dd">
                                                <input name="dateFacture" id="dateFacture" class="span12 m-wrap m-ctrl-small date-picker" type="text" value="<?= date('Y-m-d') ?>" />
                                                <span class="add-on"><i class="icon-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="span6">
                                    <div class="control-group autocomplet_container">
                                        <label class="control-label">Client</label>
                                        <div class="controls">
                                            <input class="span12 m-wrap" required="required" id="nomClient" type="text" name="nomClient" onkeyup="autocompletClient()" />
                                            <ul id="clientList"></ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="span3">
                                    <div class="control-group">
                                        <div class="controls">
                                            <input type="submit" class="btn blue" value="Créer Nouvelle Facture">
                                            <input type="hidden" name="action" value="add">
                                            <input type="hidden" name="source" value="dashboard">
                                            <input type="hidden" name="idClient" id="idClient" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                         </form>
                    </div>
                    <!-- END DASHBOARD STATS -->
                    <!-- BEGIN DASHBOARD FEEDS -->
                    <!-- ------------------------------------------------------ -->
                    <h4 class="breadcrumb"><i class="icon-table"></i> Bilans et statistiques de la semaine</h4>
                    <div class="row-fluid">
                        <div class="span3 responsive" data-tablet="span3" data-desktop="span3">
                            <div class="dashboard-stat blue">
                                <div class="visual">
                                    <i class="icon-shopping-cart"></i>
                                </div>
                                <div class="details">
                                    <div class="number">+<?= $purchaseNumberWeek ?></div>
                                    <div class="desc">Achats</div>
                                </div>
                            </div>
                        </div>
                        <div class="span3 responsive" data-tablet="span3" data-desktop="span3">
                            <div class="dashboard-stat green">
                                <div class="visual">
                                    <i class="icon-signal"></i>
                                </div>
                                <div class="details">
                                    <div class="number">
                                        +<?= $saleNumberWeek ?>
                                    </div>
                                    <div class="desc">
                                        Ventes
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="span3 responsive" data-tablet="span3" data-desktop="span3">
                            <a class="more" href="caisse.php">
                            <div class="dashboard-stat red">
                                <div class="visual">
                                    <i class="icon-money"></i>
                                </div>
                                <div class="details">
                                    <div class="number">
                                        <?= Utils::numberFormatMoney($soldeWeek) ?> DH
                                    </div>
                                    <div class="desc">Solde Caisse</div>
                                </div>
                            </div>
                            </a>
                        </div>
                        <div class="span3 responsive" data-tablet="span3" data-desktop="span3">
                            <div class="dashboard-stat blue">
                                <div class="visual">
                                    <i class="icon-group"></i>
                                </div>
                                <div class="details">
                                    <div class="number">+<?= $clientNumberWeek ?></div>
                                    <div class="desc">Clients</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END DASHBOARD STATS -->
                    <!-- BEGIN DASHBOARD FEEDS -->
                    <!-- ------------------------------------------------------ -->
                    <div class="row-fluid">
                    <div class="span12">
                        <!-- BEGIN PORTLET-->
                        <div class="portlet paddingless">
                            <div>
                                <h4 class="breadcrumb"><i class="icon-bell"></i>&nbsp;Nouveautés et alertes</h4>
                            </div>
                            <div class="portlet-body">
                                <!--BEGIN TABS-->
                                <div class="tabbable tabbable-custom">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a href="#tab_1_1" data-toggle="tab">Situation de stock</a></li>
                                        <li><a href="#tab_1_2" data-toggle="tab">Les clients de la semaine</a></li>
                                        <li><a href="#tab_1_3" data-toggle="tab">Notes des clients</a></li>
                                        <!--li><a href="#tab_1_4" data-toggle="tab">Les messages d'aujourd'hui</a></li-->
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="tab_1_1">
                                            <div class="scroller" data-height="290px" data-always-visible="1" data-rail-visible1="1">
                                                <ul class="feeds">
                                                    <?php
                                                    foreach ($minimumStockProducts as $product) {
                                                    ?>
                                                        <li>
                                                            <div class="col1">
                                                                <div class="cont">
                                                                    <div class="cont-col1">
                                                                        <div class="label label-important">
                                                                            <i class="icon-bell"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="cont-col2">
                                                                        <div class="desc">
                                                                            <strong><?= $product->code() ?></strong> :
                                                                            Quté en Stock <a><strong><?= $product->quantite() ?></strong></a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col2">
                                                                <div class="date">
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <hr>
                                                    <?php
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab_1_2">
                                            <div class="scroller" data-height="290px" data-always-visible="1" data-rail-visible1="1">
                                                <ul class="feeds">
                                                    <li>
                                                        <div class="col1">
                                                            <div class="cont">
                                                                <div class="cont-col1">
                                                                    <div class="desc">
                                                                        <strong>Client</strong> : <br>
                                                                        <a href="#" target="_blank">
                                                                            <strong>Contrat</strong> :
                                                                        </a><br>
                                                                        <strong>Projet</strong> :
                                                                        <br>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col2">
                                                            <div class="date">
                                                                <?= date('d/m/Y') ?>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <hr>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab_1_3">
                                            <div class="scroller" data-height="290px" data-always-visible="1" data-rail-visible1="1">
                                                <ul class="feeds">
                                                    <li>
                                                        <div class="col1">
                                                            <div class="cont">
                                                                <div class="cont-col1">
                                                                    <div class="label label-success">
                                                                        <i class="icon-bell"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="cont-col2">
                                                                    <div class="desc">
                                                                        <strong>Note</strong> : <br>
                                                                        <strong>Client</strong> : <br>
                                                                        <a href="#" target="_blank">
                                                                            <strong>Contrat</strong> :
                                                                        </a><br>
                                                                        <strong>Projet</strong> :
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col2">
                                                            <div class="date">
                                                                <?= date('d/m/Y') ?>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <hr>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--END TABS-->
                            </div>
                        </div>
                        <!-- END PORTLET-->
                    </div>
                    </div>
                    <!-- ------------------------------------------------------ -->
                    <!-- END DASHBOARD FEEDS -->
                    <!-- END PAGE HEADER-->
                </div>
                <!-- END PAGE CONTAINER-->
            </div>
            <!-- END PAGE -->
        </div>
        <!-- END CONTAINER -->
        <!-- BEGIN FOOTER -->
        <div class="footer">
            <?= date('Y') ?> &copy; Stock Management Application.
            <div class="span pull-right">
                <span class="go-top"><i class="icon-angle-up"></i></span>
            </div>
        </div>
        <!-- END FOOTER -->
        <!-- BEGIN JAVASCRIPTS -->
        <!-- Load javascripts at bottom, this will reduce page load time -->
        <script src="../assets/js/jquery-1.8.3.min.js"></script>
        <script src="../assets/breakpoints/breakpoints.js"></script>
        <script src="../assets/jquery-slimscroll/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../assets/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="../assets/js/jquery.blockui.js"></script>
        <script src="../assets/js/jquery.cookie.js"></script>
        <script src="../assets/fullcalendar/fullcalendar/fullcalendar.min.js"></script>
        <script type="text/javascript" src="../assets/uniform/jquery.uniform.min.js"></script>
        <script type="text/javascript" src="../assets/chosen-bootstrap/chosen/chosen.jquery.min.js"></script>
        <script src="../assets/jquery-knob/js/jquery.knob.js"></script>
        <script src="../assets/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>
        <script type="text/javascript" src="../assets/gritter/js/jquery.gritter.js"></script>
        <script type="text/javascript" src="../assets/js/jquery.pulsate.min.js"></script>
        <script type="text/javascript" src="../assets/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script type="text/javascript" src="../assets/bootstrap-daterangepicker/date.js"></script>
        <!-- ie8 fixes -->
        <!--[if lt IE 9]>
        <script src="../assets/js/excanvas.js"></script>
        <script src="../assets/js/respond.js"></script>
        <![endif]-->
        <script src="../assets/js/app.js"></script>
        <script src="script.js"></script>
        <script>
            jQuery(document).ready(function() {
                // initiate layout and plugins
                App.setPage("sliders");  // set current page
                App.init();
            });
        </script>
        <!-- END JAVASCRIPTS -->
    </body>
<!-- END BODY -->
</html>
<?php
}
else{
    header('Location:index.php');    
}
?>