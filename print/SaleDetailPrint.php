<?php

use Mpdf\Mpdf;
use Mpdf\MpdfException;

require_once '../app/classLoad.php';
require_once '../vendor/autoload.php';

session_start();

if (isset($_SESSION['userstock'])) {
    $codeSale = htmlentities($_GET['codeSale']);

    // Create Controller
    $saleActionController = new SaleActionController('sale');
    $saleDetailActionController = new SaleDetailActionController('saleDetail');
    $clientActionController = new ClientActionController('client');

    // Legacy Calls
    $productManager = new ProduitManager(PDOFactory::getMysqlConnection());

    // Vars and objects
    $sale = $saleActionController->getOneByCode($codeSale);
    $saleDetails = $saleDetailActionController->getAllByCode($codeSale);
    $totalAmountByCodeSale = $saleDetailActionController->getTotalAmountByCode($codeSale);
    $products = $productManager->getProduits();
    $client = $clientActionController->getOneById($sale->getClientId());

    ob_start();
?>
<html>
    <head>
        <?php include'styling.php' ?>
    </head>
    <body>
    <h1 class="text-align-center">Facture</h1>
    <p class="text-align-center">
        <span class="bold">Client</span>:
        <?= $client->getName() ?>
    </p>
    <p class="text-align-center">
        <span class="bold">Date</span>:
        <?= date('d/m/Y', strtotime($sale->getOperationDate())) ?>
    </p>
    <p class="text-align-center">
        <span class="bold">Référence</span>:
        <?= $sale->getNumber() ?>
    </p>
    <br><br>
    <table class="w100 text-align-center">
        <tr>
            <td class="header w30">Produit</td>
            <td class="header w20">Prix</td>
            <td class="header w20">Quantité</td>
            <td class="header w20">Total</td>
        </tr>
        <?php
        foreach ($saleDetails as $saleDetail) {
            $product = $productManager->getProduitById($saleDetail->getProductId());
            ?>
            <tr>
                <td class="w30"><?= $product->code() ?></td>
                <td class="w30"><?= Utils::numberFormatMoney($saleDetail->getPrice()) ?></td>
                <td class="w20"><?= $saleDetail->getQuantity() ?></td>
                <td class="w30"><?= Utils::numberFormatMoney($saleDetail->getPrice() * $saleDetail->getQuantity()) ?></td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td class="header w30"></td>
            <td class="header w20"></td>
            <td class="header w20">Total</td>
            <td class="header w20">
                <a>
                    <?= Utils::numberFormatMoney($totalAmountByCodeSale) ?>
                    &nbsp;DH
                </a>
            </td>
        </tr>
    </table>
    </body>
    </html>
    <?php
    $content = ob_get_clean();

    try {
        $mpdf = new Mpdf([
            'format' => 'A5-P',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);
        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle(sprintf("Facture %s", date('d-m-Y')));
        $mpdf->SetAuthor("Acme Trading Co.");
        $mpdf->SetWatermarkText("Paid");
        $mpdf->showWatermarkText = false;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.1;
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($content);
        $mpdf->Output();
    } catch (MpdfException $e) {
        die($e->getMessage());
    }
}
else {
    header('Location:../index.php');
}
