<?php
/*
 * Project: start.jamespro.nl
 * Author: petermol
 * File: settings/products.php
 * Date: Jan 6, 2016
 *
 * In dit document gaan we de opmaak bepalen.
 *
 */

$productgroup = $dbClient->selectSingle('productGroups', 'id = :id', array(':id' => $id));
$products = $dbClient->select('products', 'productGroupId = :id', array(':id' => $id));

require_once 'classes/SimpleXLSX.php';

function pre($print){
    echo '<pre>'.print_r($print,true).'</pre>';
}

// Laat alle errors zien!
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
?>
<div class="row">
    <div class="col-3 col-lg-2 bg-grey px-0" style="margin-top:-60px;padding-top:60px;">
        <ul class="nav nav-settings flex-column min-vh-100">
            <li class="nav-item"><a href="<?= BASE_URL; ?>settings/" class="nav-link"><?php $translate->__('general', true); ?></a></li>
            <li class="nav-item"><a href="<?= BASE_URL; ?>settings/users/" class="nav-link"><?php $translate->__('users', true); ?></a></li>
            <li class="nav-item"><a href="<?= BASE_URL; ?>settings/projects/" class="nav-link"><?php $translate->__('projects', true); ?></a></li>
            <li class="nav-item"><a href="<?= BASE_URL; ?>settings/tenders/" class="nav-link"><?php $translate->__('tenders', true); ?></a></li>
            <li class="nav-item"><a href="<?= BASE_URL; ?>settings/invoices/" class="nav-link"><?php $translate->__('financial', true); ?></a></li>
            <li class="nav-item"><a href="<?= BASE_URL; ?>settings/email/" class="nav-link"><?php $translate->__('email', true); ?></a></li>
            <li class="nav-item active"><a href="<?= BASE_URL; ?>settings/products/" class="nav-link"><?php $translate->__('products', true); ?></a></li>
            <?php if ($user->getVar('clientPackage') == 'demo') { ?>
                <li class="nav-item"><a href="<?= BASE_URL; ?>settings/clear/" class="nav-link active bg-danger text-white"><?php $translate->__('Verwijder data', true); ?></a></li>
            <?php } ?>
        </ul>
    </div>
    <div class="col-9 col-lg-10 px-5">
        <?php if (isset($_GET['edit'])) {
            ?>
            <div class="alert alert-block alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong><?php $translate->__('data saved', true); ?></strong><br>
                <?php $translate->__('the data has been saved', true); ?>.
            </div>
            <?php
        } ?>
        <h4><?php $translate->__('products', true); ?></h4>
        <p>Voer onderstaande velden in om producten te importeren in de groep <strong><?php echo $productgroup['name']; ?></strong></p>
        <p>Let op het bestand dient in XLSX formaat te zijn.</p>
        <form method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <label>Bestand:</label>
                    <input type="file" class="form-control" value="" name="import_products" id="import_products">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label>Oude producten verwijderen?:</label>
                    <select name="delete" id="delete" class="form-control">
                        <option value="1" selected="selected">Ja</option>
                        <option value="0">Nee</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Bestand uploaden</button>
            <a href="<?= BASE_URL; ?>settings/products-view-group/<?php echo $id; ?>/" class="btn">Annuleren</a>
        </form>
        <?php if (isset($_FILES['import_products']) || isset($_SESSION['import_csv'])) {
        ?>
            <hr>
            <?php
            if (isset($_FILES['import_products'])) {
                // Upload bestand uitlezen
                $fileName = $_FILES['import_products']['tmp_name'];
                // XLSX uitlezen
                if ( $xlsx = SimpleXLSX::parse($fileName) ) {
                    $filecontent = $xlsx->rows();
                } else {
                    echo SimpleXLSX::parseError();
                }

                // Producten Array maken
                $products = array();
                $productsDiscounts = array();
                // Zoek in rij 5 naar 'PR: Standaard
                $search_PRStandaard = array_keys($filecontent[5], 'PR: Standaard', true);
                // De laatste ID waar hij in wordt gevonden:
                $end_col = end($search_PRStandaard);

                foreach($filecontent as $key => $row) {
                    $row_nr = $key;
                    // Skip eerste 5 rijen informatie;
                    if ($key > 5) {
                        // Producten zonder staffel prijzen
                        $product_id = '';
                        $product_name = $row[2];
                        $product_code = $row[0];
                        $product_amount = '1';
                        $product_bruto = $row[6];
                        $product_netto = '';
                        $product_percentage = '';
                        $product_productGroupId = '';
                        $product_grootboeknummer = '';
                        $product_planable = '';
                        $product_extra3 = '';
                        $product_extra2 = '';
                        $product_extra1 = 'Vaste prijs';

                        if ($product_bruto != '') {
                            $products[] = array(
                                'id' => $product_id,
                                'name' => $product_name,
                                'code' => $product_code,
                                'amount' => $product_amount,
                                'bruto' => $product_bruto,
                                'netto' => $product_netto,
                                'percentage' => $product_percentage,
                                'productGroupId' => $product_productGroupId,
                                'grootboeknummer' => $product_grootboeknummer,
                                'planable' => $product_planable,
                                'extra3' => $product_extra3,
                                'extra2' => $product_extra2,
                                'extra1' => $product_extra1,
                            );
                        }
                    }

                    foreach ($row as $key => $col) {
                        if ($key > $end_col) {
                            // Producten met Klanten korting
                            $priceDiscount = explode('PR: ',$filecontent[5][$key]); // Split title bijvoorbeeld: PR: 3WO-PV (excel row:5 / key = kolom)
                            if(is_array($priceDiscount)){
                                $priceDiscountGroup = $priceDiscount[1];
                            }else{
                                $priceDiscountGroup = '';
                            }
                            // STANDAARD PRIJZEN
                            // Staffels verzamelen
                            if ($filecontent[3][$key] != '') {
                                $staffel = str_replace('Staffel: ', '', $filecontent[3][$key]);
                            } else {
                                $staffel = '';
                            }
                            // Dit zijn alle producten!
                            if ($row_nr > 5) {
                                if ($col != '') {
                                    // Product heeft staffel prijs!
                                    if($staffel != '') {
                                        $staffel_split = explode(' ', $staffel);
                                        $def_staffel = $staffel_split[0]; // 0 = eerste || 2 = laatste
                                    }else{
                                        $def_staffel = 1;
                                    }
                                    $productsDiscounts[] = array(
                                        'id' => $product_id,
                                        'name' => $product_name,
                                        'code' => $product_code,
                                        'amount' => $def_staffel,
                                        'price' => $col,
                                        'pricegroup' => $priceDiscountGroup
                                    );
                                }
                            }
                        }
                        if ($key < $end_col) {
                            // Producten ZONDER Klanten korting / standaard prijzen
                            // Staffels verzamelen
                            if ($filecontent[3][$key] != '') {
                                $staffel = str_replace('Staffel: ', '', $filecontent[3][$key]);
                            } else {
                                $staffel = '';
                            }
                            if ($row_nr > 5) {
                                if ($staffel != '' && $col != '') {
                                    // Product heeft staffel prijs!
                                    $staffel_split = explode(' ', $staffel);
                                    $def_staffel = $staffel_split[0]; // 0 = eerste || 2 = laatste
                                    $products[] = array(
                                        'id' => $product_id,
                                        'name' => $product_name,
                                        'code' => $product_code,
                                        'amount' => $def_staffel,
                                        'bruto' => $col,
                                        'netto' => $product_netto,
                                        'percentage' => $product_percentage,
                                        'productGroupId' => $product_productGroupId,
                                        'grootboeknummer' => $product_grootboeknummer,
                                        'planable' => $product_planable,
                                        'extra3' => $product_extra3,
                                        'extra2' => $product_extra2,
                                        'extra1' => '',
                                    );
                                }
                            }
                        }
                    }
                }
                // Einde producten array
                $_SESSION['import_products'] = $products;
                $_SESSION['import_discountproducts'] = $productsDiscounts;
                $_SESSION['delete_products'] = $_POST['delete'];
                $countProducts = COUNT($_SESSION['import_products']);
                $countDiscountProducts = COUNT($_SESSION['import_discountproducts']);
//                $scheidingsteken = $_POST['scheidingsteken'];
//                $row = 1;
//                $file = $_FILES['import_products']['tmp_name'];
//                ini_set('auto_detect_line_endings', true);
//                if (($handle = fopen($file, "rb")) !== false) {
//                    while ($data[] = fgetcsv($handle, 0, $scheidingsteken, '"')) {
//                    }
//                    $_SESSION['import_csv'] = $data;
//                    $_SESSION['delete_products'] = $_POST['delete'];
//                    //print_r($_SESSION['import_csv'][0]);
//                    fclose($handle);
//                }
            }
        } ?>
        <?php if($_FILES['import_products'] != ''){?>
        <hr class="margin-b-60" id="stap2" name="stap2">
        <h2 class="margin-t-40"><?php echo 'Wij hebben <strong>'.$countProducts.'</strong> producten gevonden!';?></h2>
        <p>Klik op Producten importeren om de import te starten!</p>
        <form method="post" class="loading import">
            <div class="row">
                <div class="col-md-6">
                    <input type="submit" name="start_import" value="Producten importeren" class="btn btn-primary">
                </div>
            </div>
        </form>
        <?php }?>

        <?php
        if(isset($_POST['start_import']) && $_POST['start_import'] == 'Producten importeren'){
            $i=0;
            echo 'Lets go import...<br>';

            if ($_SESSION['delete_products'] == '1') {
                $dbClient->delete('products', 'productGroupId = :id', array(':id' => $id));
            }
            // Standaard producten inladen!
            echo 'Standaard prijzen inladen..<br>';
            foreach ($_SESSION['import_products'] as $key => $product) {
                $update = $dbClient->selectSingle('products', 'code = :code AND amount = :amount', array(
                    ':code' => ($product['code'] != '') ? $product['code'] : $product['name'],
                    ':amount' => $product['amount']
                ));
                if (!isset($update['code'])) {
                    $insertProductsRow = array(
                        'name' => $product['name'],
                        'code' => ($product['code'] != '') ? $product['code'] : $product['name'],
                        'amount' => (($product['amount'] != '') ? $product['amount'] : 1),
                        'bruto' => str_replace(',', '.', $product['bruto']),
                        'netto' => str_replace(',', '.', $product['netto']),
                        'percentage' => str_replace(',', '.', $product['percentage']),
                        'productGroupId' => 0,
                        'grootboeknummer' => $product['grootboeknummer'],
                        'planable' => ($product['planable'] == '1') ? '1' : '0',
                        'extra3' => $product['extra3'],
                        'extra2' => $product['extra2'],
                        'extra1' => $product['extra1']
                    );
                    $import_product = $dbClient->insert("products", $insertProductsRow);
                    $i++;
                }else{
                    $product = $dbClient->update(
                        'products',
                        array(
                            'name' => $product['name'],
                            'netto' => str_replace(',', '.', $product['bruto']),
                            'bruto' => str_replace(',', '.', $product['bruto']),
                            'percentage' => str_replace(',', '.', $product['percentage']),
                            'productGroupId' => $id,
                            'grootboeknummer' => $product['grootboeknummer'],
                        ),
                        'code = :codeSelect AND amount = :amountSelect',
                        array(
                            ':code' => ($product['code'] != '') ? $product['code'] : $product['name'],
                            ':amount' => $product['amount']
                        )
                    );
                }
            }
            // Kortingen inladen
            echo 'Kortingen prijzen inladen..<br>';
            foreach ($_SESSION['import_discountproducts'] as $key => $discountProduct) {
                $update = $dbClient->selectSingle('productsDiscounts', 'product_code = :code AND product_amount = :amount AND product_pricegroup = :pricegroup', array(
                    ':code' => ($discountProduct['code'] != '') ? $discountProduct['code'] : $discountProduct['name'],
                    ':amount' => $discountProduct['amount'],
                    ':pricegroup' => $discountProduct['pricegroup'],
                ));
                if (!isset($update['code'])) {
                    $insertDiscountProductsRow = array(
                        'product_name' => $discountProduct['name'],
                        'product_code' => ($discountProduct['code'] != '') ? $discountProduct['code'] : $discountProduct['name'],
                        'product_amount' => (($discountProduct['amount'] != '') ? $discountProduct['amount'] : 1),
                        'product_price' => str_replace(',', '.', $discountProduct['price']),
                        'product_pricegroup' => $discountProduct['pricegroup']
                    );
                    $import_discountProduct = $dbClient->insert("productsDiscounts", $insertDiscountProductsRow);
                    $i++;
                }else{
                    $productDiscount = $dbClient->update(
                        'productsDiscounts',
                        array(
                            'product_name' => $discountProduct['name'],
                            'product_amount' => (($discountProduct['amount'] != '') ? $discountProduct['amount'] : 1),
                            'product_price' => str_replace(',', '.', $discountProduct['price']),
//                            'product_pricegroup' => $discountProduct['pricegroup'],
                        ),
                        'product_code = :codeSelect AND amount = :amountSelect AND product_pricegroup = :pricegroup',
                        array(
                            ':code' => ($discountProduct['code'] != '') ? $discountProduct['code'] : $discountProduct['name'],
                            ':amount' => $discountProduct['amount'],
                            ':pricegroup' => $discountProduct['pricegroup'],
                        )
                    );
                }
            }
            unset($_SESSION['import_csv']);
            unset($_SESSION['delete_products']);
            ?>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <p>Er zijn <?= $i; ?> producten geimporteerd.</p>
                    <p><a href="<?= BASE_URL; ?>settings/products-view-group/<?php echo $id; ?>/" class="nav-link">Klik hier om deze te bekijken.</a></p>
                </div>
            </div>
            <?php
        } ?>
    </div>
</div>
