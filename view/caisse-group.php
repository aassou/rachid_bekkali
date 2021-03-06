<?php

require_once('../app/classLoad.php');
session_start();

if (isset($_SESSION['userstock'])) {
    // Legacy calls
    $caisseManager = new CaisseManager(PDOFactory::getMysqlConnection());
    $caisses =$caisseManager->getCaissesGroupByMonth();

    $totalCaisse =
    $caisseManager->getTotalCaisseByType('Entree') - $caisseManager->getTotalCaisseByType('Sortie');
    $totalEntrees = $caisseManager->getTotalCaisseByType('Entree');
    $totalSorties = $caisseManager->getTotalCaisseByType('Sortie');

    $breadcurmb = new Breadcrumb([
        [
            'class' => 'icon-money',
            'link' => 'stock.php',
            'title' => '<strong>La Caisse</strong>'
        ]
    ]);
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
                        <?= $breadcurmb->getBreadcrumb() ?>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <?php
                            if( isset($_SESSION['caisse-action-message'])
                            and isset($_SESSION['caisse-type-message']) ){
                                $message = $_SESSION['caisse-action-message'];
                                $typeMessage = $_SESSION['caisse-type-message'];
                            ?>
                            <div class="alert alert-<?= $typeMessage ?>">
                                <button class="close" data-dismiss="alert"></button>
                                <?= $message ?>
                            </div>
                             <?php }
                                unset($_SESSION['caisse-action-message']);
                                unset($_SESSION['caisse-type-message']);
                             ?>
                            <!-- addCaisse box begin -->
                            <div id="addCaisse" class="modal hide fade in" tabindex="-1" role="dialog" aria-labelledby="login" aria-hidden="false" >
                                <form id="addCaisseForm" class="form-horizontal" action="../controller/CaisseActionController.php" method="post">
                                    <div class="modal-header">
                                        <h3>Ajouter une opération à la caisse</h3>
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="control-group">
                                            <label class="control-label">Type Opération</label>
                                            <div class="controls">
                                                <select name="type">
                                                    <option value="Entree">Entree</option>
                                                    <option value="Sortie">Sortie</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Date Opération</label>
                                            <div class="controls date date-picker" data-date="" data-date-format="yyyy-mm-dd">
                                                <input name="dateOperation" id="dateOperation" class="m-wrap m-ctrl-small date-picker" type="text" value="<?= date('Y-m-d') ?>" />
                                                <span class="add-on"><i class="icon-calendar"></i></span>
                                             </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Montant</label>
                                            <div class="controls">
                                                <input required="required" id="montant" type="text" name="montant" value="" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Designation</label>
                                            <div class="controls">
                                                <input id="designation" type="text" name="designation" value="" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="control-group">
                                            <div class="controls">
                                                <input type="hidden" name="action" value="add">
                                                <input type="hidden" name="source" value="caisse-group">
                                                <button class="btn" data-dismiss="modal"aria-hidden="true">Non</button>
                                                <button type="submit" class="btn red" aria-hidden="true">Oui</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- addCaisse box end -->
                            <!-- printBilanCaisse box begin -->
                            <div id="printCaisseBilan" class="modal hide fade in" tabindex="-1" role="dialog" aria-labelledby="login" aria-hidden="false" >
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                    <h3>Imprimer Bilan de la Caisse </h3>
                                </div>
                                <div class="modal-body">
                                    <form class="form-horizontal" action="../controller/CaissePrintController.php" method="post" enctype="multipart/form-data">
                                        <p><strong>Séléctionner les opérations de caisse à imprimer</strong></p>
                                        <div class="control-group">
                                          <label class="control-label">Imprimer</label>
                                          <div class="controls">
                                             <label class="radio">
                                                 <div class="radio" id="toutes">
                                                     <span>
                                                         <input type="radio" class="criteriaPrint" name="criteria" value="toutesCaisse" style="opacity: 0;">
                                                     </span>
                                                 </div> Toute la liste
                                             </label>
                                             <label class="radio">
                                                 <div class="radio" id="date">
                                                     <span class="checked">
                                                         <input type="radio" class="criteriaPrint" name="criteria" value="parDate" checked="" style="opacity: 0;">
                                                     </span>
                                                 </div> Par Choix
                                             </label>
                                          </div>
                                       </div>
                                       <div id="showDateRange">
                                        <div class="control-group">
                                            <label class="control-label">Date</label>
                                            <div class="controls date date-picker" data-date="" data-date-format="yyyy-mm-dd">
                                               <input style="width:100px" name="dateFrom" id="dateFrom" class="m-wrap m-ctrl-small date-picker" type="text" value="<?= date('Y-m-d') ?>" />
                                               &nbsp;-&nbsp;
                                               <input style="width:100px" name="dateTo" id="dateTo" class="m-wrap m-ctrl-small date-picker" type="text" value="<?= date('Y-m-d') ?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Type Opération</label>
                                            <div class="controls">
                                                <select class="m-wrap" name="type">
                                                    <option value="Toutes">Toutes les opérations</option>
                                                    <option value="Entree">Entrées</option>
                                                    <option value="Sortie">Sorties</option>
                                                </select>
                                            </div>
                                        </div>
                                       </div>
                                        <div class="control-group">
                                            <div class="controls">
                                                <input type="hidden" name="societe" value="1" />
                                                <button class="btn" data-dismiss="modal"aria-hidden="true">Non</button>
                                                <button type="submit" class="btn red" aria-hidden="true">Oui</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- printBilanCaisse box end -->
                           <table class="table table-striped table-bordered table-advance table-hover">
                                <tbody>
                                    <tr>
                                        <th style="width: 15%"><strong>Total des entrées</strong></th>
                                        <th style="width: 18%"><a><strong><?= number_format($totalEntrees, 2, ',', ' ') ?>&nbsp;DH</strong></a></th>
                                        <th style="width: 15%"><strong>Total des sorties</strong></th>
                                        <th style="width: 18%"><a><strong><?= number_format($totalSorties, 2, ',', ' ') ?>&nbsp;DH</strong></a></th>
                                        <th style="width: 15%"><strong>Solde de caisse</strong></th>
                                        <th style="width: 19%"><a><strong><?= number_format($totalCaisse, 2, ',', ' ') ?>&nbsp;DH</strong></a></th>
                                    </tr>
                                </tbody>
                            </table>
                           <div class="portlet box light-grey">
                                <div class="portlet-title">
                                    <h4>Liste des opérations de la caisse</h4>
                                    <div class="tools">
                                        <a href="javascript:;" class="reload"></a>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="clearfix">
                                        <?php
                                        if (
                                            $_SESSION['userstock']->profil() == "admin" ||
                                            $_SESSION['userstock']->profil() == "manager"
                                            ) {
                                        ?>
                                        <div class="btn-group pull-left">
                                            <a class="btn blue" href="#addCaisse" data-toggle="modal">
                                                <i class="icon-plus-sign"></i>
                                                 Ajouter
                                            </a>
                                        </div>
                                        <?php
                                        }
                                        ?>
                                        <div class="btn-group pull-right">
                                            <a class="btn green" href="#printCaisseBilan" data-toggle="modal">
                                                <i class="icon-print"></i>
                                                 Bilan de Caisse
                                            </a>
                                        </div>
                                    </div>
                                    <table class="table table-striped table-bordered table-hover" id="sample_1">
                                        <thead>
                                            <tr>
                                                <th style="width:25%">Mois/Année</th>
                                                <th style="width:25%">Crédit</th>
                                                <th style="width:25%">Débit</th>
                                                <th style="width:25%">Solde</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach($caisses as $caisse){
                                            ?>
                                            <tr class="odd gradeX">
                                                <?php
                                                $mois = date('m', strtotime($caisse->dateOperation()));
                                                $annee = date('Y', strtotime($caisse->dateOperation()));
                                                $credit = $caisseManager->getTotalCaissesByMonthYearByType($mois, $annee, 'Entree');
                                                $debit = $caisseManager->getTotalCaissesByMonthYearByType($mois, $annee, 'Sortie');
                                                $solde = $credit - $debit;
                                                ?>
                                                <td>
                                                    <a class="btn mini" href="caisse-mois-annee.php?mois=<?= $mois ?>&annee=<?= $annee ?>">
                                                        <strong><?= date('m/Y', strtotime($caisse->dateOperation())) ?></strong>
                                                    </a>
                                                </td>
                                                <td>
                                                    <strong><?= number_format($credit, 2, ',', ' ') ?> DH</strong>
                                                </td>
                                                <td>
                                                    <strong><?= number_format($debit, 2, ',', ' ') ?> DH</strong>
                                                </td>
                                                <td>
                                                    <strong><?= number_format($solde, 2, ',', ' ') ?> DH</strong>
                                                </td>
                                            </tr>
                                            <?php
                                            }//end of loop
                                            ?>
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                               </div>
                            </div>
                        </div>
                    </div>
                    <!-- END PAGE CONTENT -->
                </div>
                <!-- END PAGE CONTAINER-->
            </div>
            <!-- END PAGE -->
        </div>
        <!-- END CONTAINER -->
        <!-- BEGIN FOOTER -->
        <div class="footer">
            2015 &copy; ImmoERP. Management Application.
            <div class="span pull-right">
                <span class="go-top"><i class="icon-angle-up"></i></span>
            </div>
        </div>
        <!-- END FOOTER -->
        <!-- BEGIN JAVASCRIPTS -->
        <!-- Load javascripts at bottom, this will reduce page load time -->
        <script src="../assets/js/jquery-1.8.3.min.js"></script>
        <script src="../assets/breakpoints/breakpoints.js"></script>
        <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="../assets/js/jquery.blockui.js"></script>
        <script src="../assets/js/jquery.cookie.js"></script>
        <script src="../assets/fancybox/source/jquery.fancybox.pack.js"></script>
        <script type="text/javascript" src="../assets/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script type="text/javascript" src="../assets/bootstrap-daterangepicker/date.js"></script>
        <!-- ie8 fixes -->
        <!--[if lt IE 9]>
        <script src="../assets/js/excanvas.js"></script>
        <script src="../assets/js/respond.js"></script>
        <![endif]-->
        <script type="text/javascript" src="../assets/uniform/jquery.uniform.min.js"></script>
        <script type="text/javascript" src="../assets/data-tables/jquery.dataTables.js"></script>
        <script type="text/javascript" src="../assets/data-tables/DT_bootstrap.js"></script>
        <script src="../assets/js/app.js"></script>
        <script>
            jQuery(document).ready(function() {
                // initiate layout and plugins
                //App.setPage("table_managed");
                App.init();
                $('.criteriaPrint').on('change',function(){
                    if( $(this).val()==="toutesCaisse"){
                    $("#showDateRange").hide()
                    }
                    else{
                    $("#showDateRange").show()
                    }
                });
            });
        </script>
    </body>
<!-- END BODY -->
</html>
<?php
}
/*else if(isset($_SESSION['userstock']) and $_SESSION->profil()!="admin"){
    header('Location:dashboard.php');
}*/
else{
    header('Location:index.php');    
}
?>