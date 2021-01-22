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
        <p>Let op het bestand dient in CSV formaat te zijn.</p>
        <form method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <label>Bestand:</label>
                    <input type="file" class="form-control" value="" name="import_products" id="import_products">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label>Scheidingsteken:</label>
                    <select name="scheidingsteken" id="scheidingsteken" class="form-control">
                        <option value=";">";" (puntkomma)</option>
                        <option value=",">"," (komma)</option>
                    </select>
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
                $scheidingsteken = $_POST['scheidingsteken'];
                $row = 1;
                $file = $_FILES['import_products']['tmp_name'];
                ini_set('auto_detect_line_endings', true);
                if (($handle = fopen($file, "rb")) !== false) {
                    while ($data[] = fgetcsv($handle, 0, $scheidingsteken, '"')) {
                    }
                    $_SESSION['import_csv'] = $data;
                    $_SESSION['delete_products'] = $_POST['delete'];
                    //print_r($_SESSION['import_csv'][0]);
                    fclose($handle);
                }
            } ?>

            <?php if ($user->getVar('clientId') == '90001053') {
                //van der Pol?>
                <form method="post" class="loading import">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Naam: </label>
                            <select name="name" class="form-control">
                                <option>leeg</option>
                                <?php foreach ($_SESSION['import_csv'][0] as $key => $value) {
                                    ?>
                                    <option value="<?= $key; ?>" <?php if (strtolower($value) == 'naam') echo 'selected="selected"'; ?>><?= $value; ?></option>
                                    <?php
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Code: </label>
                            <select name="code" class="form-control">
                                <option>leeg</option>
                                <?php foreach ($_SESSION['import_csv'][0] as $key => $value) {
                                    ?>
                                    <option value="<?= $key; ?>" <?php if (strtolower($value) == 'code') echo 'selected="selected"'; ?>><?= $value; ?></option>
                                    <?php
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Staffel 1: </label>
                            <select name="amount-1" class="form-control">
                                <option>leeg</option>
                                <?php foreach ($_SESSION['import_csv'][0] as $key => $value) {
                                    ?>
                                    <option value="<?= $key; ?>"><?= $value; ?></option>
                                    <?php
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Staffel 2: </label>
                            <select name="amount-2" class="form-control">
                                <option>leeg</option>
                                <?php foreach ($_SESSION['import_csv'][0] as $key => $value) {
                                    ?>
                                    <option value="<?= $key; ?>"><?= $value; ?></option>
                                    <?php
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Staffel 3: </label>
                            <select name="amount-3" class="form-control">
                                <option>leeg</option>
                                <?php foreach ($_SESSION['import_csv'][0] as $key => $value) {
                                    ?>
                                    <option value="<?= $key; ?>"><?= $value; ?></option>
                                    <?php
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="submit" value="Producten importeren" class="btn btn-primary">
                        </div>
                    </div>
                </form>
                <?php
            } else {
                ?>
                <form method="post" class="loading import">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Naam: </label>
                            <select name="name" class="form-control">
                                <option>leeg</option>
                                <?php foreach ($_SESSION['import_csv'][0] as $key => $value) {
                                    ?>
                                    <option value="<?= $key; ?>" <?php if (strstr(strtolower($value), 'naam')) echo 'selected="selected"'; ?>><?= $value; ?></option>
                                    <?php
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Code: </label>
                            <select name="code" class="form-control">
                                <option>leeg</option>
                                <?php foreach ($_SESSION['import_csv'][0] as $key => $value) {
                                    ?>
                                    <option value="<?= $key; ?>" <?php if (strtolower($value) == 'code') echo 'selected="selected"'; ?>><?= $value; ?></option>
                                    <?php
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Aantal/Oplage: </label>
                            <select name="amount" class="form-control">
                                <option>leeg</option>
                                <?php foreach ($_SESSION['import_csv'][0] as $key => $value) {
                                    ?>
                                    <option value="<?= $key; ?>" <?php if (strtolower($value) == 'aantal') echo 'selected="selected"'; ?>><?= $value; ?></option>
                                    <?php
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Prijs verkoop: </label>
                            <select name="bruto" class="form-control">
                                <option>leeg</option>
                                <?php foreach ($_SESSION['import_csv'][0] as $key => $value) {
                                    ?>
                                    <option value="<?= $key; ?>" <?php if (strtolower($value) == 'prijs verkoop') echo 'selected="selected"'; ?>><?= $value; ?></option>
                                    <?php
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Prijs inkoop: </label>
                            <select name="netto" class="form-control">
                                <option>leeg</option>
                                <?php foreach ($_SESSION['import_csv'][0] as $key => $value) {
                                    ?>
                                    <option value="<?= $key; ?>" <?php if (strtolower($value) == 'prijs inkoop') echo 'selected="selected"'; ?>><?= $value; ?></option>
                                    <?php
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Percentage: </label>
                            <select name="percentage" class="form-control">
                                <option>leeg</option>
                                <?php foreach ($_SESSION['import_csv'][0] as $key => $value) {
                                    ?>
                                    <option value="<?= $key; ?>" <?php if (strtolower($value) == 'percentage/marge') echo 'selected="selected"'; ?>><?= $value; ?></option>
                                    <?php
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Grootboeknummer: </label>
                            <select name="grootboeknummer" class="form-control">
                                <option>leeg</option>
                                <?php foreach ($_SESSION['import_csv'][0] as $key => $value) {
                                    ?>
                                    <option value="<?= $key; ?>" <?php if (strtolower($value) == 'grootboek') echo 'selected="selected"'; ?>><?= $value; ?></option>
                                    <?php
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Planbaar: </label>
                            <select name="planable" class="form-control">
                                <option>leeg</option>
                                <?php foreach ($_SESSION['import_csv'][0] as $key => $value) {
                                    ?>
                                    <option value="<?= $key; ?>" <?php if (strtolower($value) == 'planbaar (1/0)') echo 'selected="selected"'; ?>><?= $value; ?></option>
                                    <?php
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Extra: </label>
                            <select name="extra1" class="form-control">
                                <option>leeg</option>
                                <?php foreach ($_SESSION['import_csv'][0] as $key => $value) {
                                    ?>
                                    <option value="<?= $key; ?>" <?php if (strtolower($value) == 'extra') echo 'selected="selected"'; ?>><?= $value; ?></option>
                                    <?php
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="submit" value="Producten importeren" class="btn btn-primary">
                        </div>
                    </div>
                </form>
                <?php
            } ?>
            <?php
        } ?>

        <?php
        if (isset($_POST['name'])) {
            $i = 0;

            if ($_SESSION['delete_products'] == '1') {
                $dbClient->delete('products', 'productGroupId = :id', array(':id' => $id));
            }

            foreach ($_SESSION['import_csv'] as $key => $import) {
                if ($key != 0 && $import[$_POST['name']] != '') {
                    $update = $dbClient->selectSingle('products INNER JOIN productGroups ON products.productGroupId = productGroups.id', 'products.code = :code AND products.amount = :amount', array(
                        ':code' => ($import[$_POST['code']] != '') ? $import[$_POST['code']] : $import[$_POST['name']],
                        ':amount' => $import[$_POST['amount']]
                    ));

                    if ($user->getVar('clientId') == '90001053') {
                        if ($_POST['amount-1'] != '') {
                            $insertRow = array(
                                'name' => $import[$_POST['name']],
                                'code' => ($import[$_POST['code']] != '') ? $import[$_POST['code']] : $import[$_POST['name']],
                                'amount' => $_SESSION['import_csv'][0][$_POST['amount-1']],
                                'netto' => str_replace(array(',', '€', ' '), array('.', '', ''), $import[$_POST['amount-1']]), //verkoop
                                'productGroupId' => $id,
                            );
                            $product = $dbClient->insert("products", $insertRow);
                            if ($product != "") {
                                $i++;
                            }
                        }
                        if ($_POST['amount-2'] != '') {
                            $insertRow = array(
                                'name' => $import[$_POST['name']],
                                'code' => ($import[$_POST['code']] != '') ? $import[$_POST['code']] : $import[$_POST['name']],
                                'amount' => $_SESSION['import_csv'][0][$_POST['amount-2']],
                                'netto' => str_replace(array(',', '€', ' '), array('.', '', ''), $import[$_POST['amount-2']]), //verkoop
                                'productGroupId' => $id,
                            );
                            $product = $dbClient->insert("products", $insertRow);
                            if ($product != "") {
                                $i++;
                            }
                        }
                        if ($_POST['amount-3'] != '') {
                            $insertRow = array(
                                'name' => $import[$_POST['name']],
                                'code' => ($import[$_POST['code']] != '') ? $import[$_POST['code']] : $import[$_POST['name']],
                                'amount' => $_SESSION['import_csv'][0][$_POST['amount-3']],
                                'netto' => str_replace(array(',', '€', ' '), array('.', '', ''), $import[$_POST['amount-3']]), //verkoop
                                'productGroupId' => $id,
                            );
                            $product = $dbClient->insert("products", $insertRow);
                            if ($product != "") {
                                $i++;
                            }
                        }
                    } elseif (!isset($update['code'])) {
                        $insertRow = array(
                            'name' => $import[$_POST['name']],
                            'code' => ($import[$_POST['code']] != '') ? $import[$_POST['code']] : $import[$_POST['name']],
                            'amount' => (($import[$_POST['amount']] != '') ? $import[$_POST['amount']] : 1),
                            'netto' => str_replace(',', '.', $import[$_POST['bruto']]),
                            'bruto' => str_replace(',', '.', $import[$_POST['netto']]),
                            'percentage' => str_replace(',', '.', $import[$_POST['percentage']]),
                            'productGroupId' => $id,
                            'grootboeknummer' => $import[$_POST['grootboeknummer']],
                            'planable' => ($import[$_POST['planable']] == '1') ? '1' : '0',
                            'extra1' => $import[$_POST['extra1']],
                        );

//                        if($user->getId() == '2'){
//                            echo '<pre>'.print_r($insertRow,true).'</pre>';
//                        }else{
                        $product = $dbClient->insert("products", $insertRow);
//                            echo '<pre>'.print_r($dbClient,true).'</pre>';
//                        }
                        if ($product != "") {
                            $i++;
                        }
                    } else {
                        $product = $dbClient->update(
                            'products',
                            array(
                                'name' => $import[$_POST['name']],
                                'netto' => str_replace(',', '.', $import[$_POST['bruto']]),
                                'bruto' => str_replace(',', '.', $import[$_POST['netto']]),
                                'percentage' => str_replace(',', '.', $import[$_POST['percentage']]),
                                'productGroupId' => $id,
                                'grootboeknummer' => $import[$_POST['grootboeknummer']],
                            ),
                            'code = :codeSelect AND amount = :amountSelect',
                            array(
                                ':code' => ($import[$_POST['code']] != '') ? $import[$_POST['code']] : $import[$_POST['name']],
                                ':amount' => $import[$_POST['amount']]
                            )
                        );
                    }
                }
            }

            unset($_SESSION['import_csv']);
            unset($_SESSION['delete_products']); ?>
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
