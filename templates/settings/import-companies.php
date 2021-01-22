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
?>
<div class="row">
    <div class="col-3 col-lg-2 bg-grey px-0" style="margin-top:-60px;padding-top:60px;">
        <ul class="nav nav-settings flex-column min-vh-100">
            <li class="nav-item active"><a href="<?= BASE_URL; ?>settings/" class="nav-link"><?php $translate->__('general', true); ?></a></li>
            <li class="nav-item"><a href="<?= BASE_URL; ?>settings/users/" class="nav-link"><?php $translate->__('users', true); ?></a></li>
            <li class="nav-item"><a href="<?= BASE_URL; ?>settings/projects/" class="nav-link"><?php $translate->__('projects', true); ?></a></li>
            <li class="nav-item"><a href="<?= BASE_URL; ?>settings/tenders/" class="nav-link"><?php $translate->__('tenders', true); ?></a></li>
            <li class="nav-item"><a href="<?= BASE_URL; ?>settings/invoices/" class="nav-link"><?php $translate->__('financial', true); ?></a></li>
            <li class="nav-item"><a href="<?= BASE_URL; ?>settings/email/" class="nav-link"><?php $translate->__('email', true); ?></a></li>
            <li class="nav-item"><a href="<?= BASE_URL; ?>settings/products/" class="nav-link"><?php $translate->__('products', true); ?></a></li>
            <?php if ($user->getVar('clientPackage') == 'demo') { ?>
                <li class="nav-item"><a href="<?= BASE_URL; ?>settings/clear/" class="nav-link active bg-danger text-white"><?php $translate->__('Verwijder data', true); ?></a></li>
            <?php } ?>
        </ul>
    </div>
    <div class="col-9 col-lg-10 px-5">
        <?php if (isset($_GET['edit'])) { ?>
            <div class="alert alert-block alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong><?php $translate->__('data saved', true); ?></strong><br>
                <?php $translate->__('the data has been saved', true); ?>.
            </div>
        <?php } ?>
        <h4><?php $translate->__('import relations', true); ?></h4>
        <p>Selecteer hier het CSV bestand om te importeren</p>
        <form method="post" enctype="multipart/form-data" action="<?= BASE_URL; ?>settings/import-companies/#stap2">
            <div class="row">
                <div class="col-md-6">
                    <label>Bestand:</label>
                    <input type="file" class="form-control" value="" name="import_companies" id="import_companies">
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
            <button type="submit" class="btn btn-primary">Bestand uploaden</button>
            <a href="<?= BASE_URL; ?>settings/import-companies/" class="btn">Annuleren</a>
        </form>

        <?php
        if (isset($_FILES['import_companies']) || isset($_SESSION['import_csv'])) {

            if (isset($_FILES['import_companies'])) {
                $scheidingsteken = $_POST['scheidingsteken'];
                $row = 1;
                $file = $_FILES['import_companies']['tmp_name'];
                ini_set('auto_detect_line_endings', TRUE);
                if (($handle = fopen($file, "rb")) !== FALSE) {
                    while ($data[] = fgetcsv($handle, 0, $scheidingsteken, '"')) {

                    }
                    $_SESSION['import_csv'] = $data;
                    //print_r($_SESSION['import_csv'][0]);
                    fclose($handle);
                }
            }
            ?>
            <hr class="margin-b-60" id="stap2" name="stap2">
            <h2 class="margin-t-40">Velden koppelen</h2>
            <form method="post" class="loading import" action="<?= BASE_URL; ?>settings/import-companies/#stap3">
                <div class="row">
                    <div class="col-md-6">
                        <label for="companyId">Nummer: </label>
                        <select name="companyId" class="form-control">
                            <option>leeg</option>
                            <?php foreach ($_SESSION['import_csv'][0] as $key => $value) { ?>
                                <option value="<?= $key; ?>" <?php if (strtolower($value) == 'nummer') echo 'selected="selected"'; ?>><?= $value; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="companyName">Bedrijfsnaam: </label>
                        <select name="companyName" class="form-control">
                            <option>leeg</option>
                            <?php foreach ($_SESSION['import_csv'][0] as $key => $value) { ?>
                                <option value="<?= $key; ?>" <?php if (strtolower($value) == 'bedrijfsnaam') echo 'selected="selected"'; ?>><?= $value; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="companyKvk">KvK: </label>
                        <select name="companyKvk" class="form-control">
                            <option>leeg</option>
                            <?php foreach ($_SESSION['import_csv'][0] as $key => $value) { ?>
                                <option value="<?= $key; ?>" <?php if (strtolower($value) == 'kvk') echo 'selected="selected"'; ?>><?= $value; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="companyVat">BTW: </label>
                        <select name="companyVat" class="form-control">
                            <option>leeg</option>
                            <?php foreach ($_SESSION['import_csv'][0] as $key => $value) { ?>
                                <option value="<?= $key; ?>" <?php if (strtolower($value) == 'btw') echo 'selected="selected"'; ?>><?= $value; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="branchName">Locatie: </label>
                        <select name="branchName" class="form-control">
                            <option>leeg</option>
                            <?php foreach ($_SESSION['import_csv'][0] as $key => $value) { ?>
                                <option value="<?= $key; ?>" <?php if (strtolower($value) == 'locatie') echo 'selected="selected"'; ?>><?= $value; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="branchStreet">Adres: </label>
                        <select name="branchStreet" class="form-control">
                            <option>leeg</option>
                            <?php foreach ($_SESSION['import_csv'][0] as $key => $value) { ?>
                                <option value="<?= $key; ?>" <?php if (strtolower($value) == 'adres') echo 'selected="selected"'; ?>><?= $value; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="branchZip">Postcode: </label>
                        <select name="branchZip" class="form-control">
                            <option>leeg</option>
                            <?php foreach ($_SESSION['import_csv'][0] as $key => $value) { ?>
                                <option value="<?= $key; ?>" <?php if (strtolower($value) == 'postcode') echo 'selected="selected"'; ?>><?= $value; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="branchCity">Plaats: </label>
                        <select name="branchCity" class="form-control">
                            <option>leeg</option>
                            <?php foreach ($_SESSION['import_csv'][0] as $key => $value) { ?>
                                <option value="<?= $key; ?>" <?php if (strtolower($value) == 'plaats') echo 'selected="selected"'; ?>><?= $value; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="branchCountry">Land: </label>
                        <select name="branchCountry" class="form-control">
                            <option>leeg</option>
                            <?php foreach ($_SESSION['import_csv'][0] as $key => $value) { ?>
                                <option value="<?= $key; ?>" <?php if (strtolower($value) == 'land') echo 'selected="selected"'; ?>><?= $value; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="branchTelephone">Telefoonnr.: </label>
                        <select name="branchTelephone" class="form-control">
                            <option>leeg</option>
                            <?php foreach ($_SESSION['import_csv'][0] as $key => $value) { ?>
                                <option value="<?= $key; ?>" <?php if (strtolower($value) == 'telefoonnummer') echo 'selected="selected"'; ?>><?= $value; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="branchNote">Website: </label>
                        <select name="branchNote" class="form-control">
                            <option>leeg</option>
                            <?php foreach ($_SESSION['import_csv'][0] as $key => $value) { ?>
                                <option value="<?= $key; ?>" <?php if (strtolower($value) == 'website') echo 'selected="selected"'; ?>><?= $value; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="contactName">Contactpersoon naam: </label>
                        <select name="contactName" class="form-control">
                            <option>leeg</option>
                            <?php foreach ($_SESSION['import_csv'][0] as $key => $value) { ?>
                                <option value="<?= $key; ?>" <?php if (strtolower($value) == 'vornaam') echo 'selected="selected"'; ?>><?= $value; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="contactSurName">Contactpersoon achternaam: </label>
                        <select name="contactSurName" class="form-control">
                            <option>leeg</option>
                            <?php foreach ($_SESSION['import_csv'][0] as $key => $value) { ?>
                                <option value="<?= $key; ?>" <?php if (strtolower($value) == 'achternaam') echo 'selected="selected"'; ?>><?= $value; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="contactEmail">Email: </label>
                        <select name="contactEmail" class="form-control">
                            <option>leeg</option>
                            <?php foreach ($_SESSION['import_csv'][0] as $key => $value) { ?>
                                <option value="<?= $key; ?>" <?php if (strtolower($value) == 'email') echo 'selected="selected"'; ?>><?= $value; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="contactTelephone">Telefoonnr.: </label>
                        <select name="contactTelephone" class="form-control">
                            <option>leeg</option>
                            <?php foreach ($_SESSION['import_csv'][0] as $key => $value) { ?>
                                <option value="<?= $key; ?>" <?php if (strtolower($value) == 'telefoonnummer') echo 'selected="selected"'; ?>><?= $value; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="contactFax">Mobiel: </label>
                        <select name="contactFax" class="form-control">
                            <option>leeg</option>
                            <?php foreach ($_SESSION['import_csv'][0] as $key => $value) { ?>
                                <option value="<?= $key; ?>" <?php if (strtolower($value) == 'mobiel') echo 'selected="selected"'; ?>><?= $value; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="companyLeverancier">Klanten/Leverancies: </label>
                        <select name="companyLeverancier" class="form-control">
                            <option value="false">Klanten</option>
                            <option value="true">Leveranciers</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <input type="submit" value="importeren" class="btn btn-primary">
                    </div>
                </div>
            </form>
        <?php } ?>
        <?php
        //Nummer / Telefoonnummer / bij Contactpersoon / www /
        if (isset($_POST['companyName'])) {
            $companyName = '';
            foreach ($_SESSION['import_csv'] as $key => $import) {
                if ($key != 0 && $import[$_POST['companyName']] != '') {
                    if ($companyName != $import[$_POST['companyName']]) {
                        $company = $dbClient->insert("companies", array(
                            'companyId' => $import[$_POST['companyId']],
                            'companyName' => $import[$_POST['companyName']],
                            'companyClient' => (!isset($_POST['companyLeverancier']) || $_POST['companyLeverancier'] == 'false') ? 1 : 0,
                            'companySupplier' => (isset($_POST['companyLeverancier']) && $_POST['companyLeverancier'] == 'true') ? 1 : 0,
                            'companyKvk' => $import[$_POST['companyKvk']],
                            'companyVat' => $import[$_POST['companyVat']]
                        ));

                        $branch = $dbClient->insert("branches", array(
                            'branchName' => (($import[$_POST['branchName']] != '') ? $import[$_POST['branchName']] : $import[$_POST['branchCity']]),
                            'branchStreet' => $import[$_POST['branchStreet']],
                            'branchZip' => $import[$_POST['branchZip']],
                            'branchCity' => $import[$_POST['branchCity']],
                            'branchCountry' => $import[$_POST['branchCountry']],
                            'branchTelephone' => $import[$_POST['branchTelephone']],
                            'branchFax' => '',
                            'branchEmail' => $import[$_POST['contactEmail']],
                            'companyId' => $company,
                            'branchNote' => $import[$_POST['branchNote']]
                        ));
                    }
                    $contact = $dbClient->insert("contacts", array(
                        'contactName' => $import[$_POST['contactName']],
                        'contactSurName' => $import[$_POST['contactSurName']],
                        'contactEmail' => $import[$_POST['contactEmail']],
                        'contactTelephone' => $import[$_POST['contactTelephone']],
                        'contactFax' => $import[$_POST['contactFax']],
                        'contactBranch' => $branch,
                        'contactNote' => '',
                        'companyId' => $company
                    ));
                    if ($companyName != $import[$_POST['companyName']]) {
                        $company_update = $dbClient->update("companies", array(
                            'branchId' => $branch,
                            'contactId' => $contact,
                        ), "companyId = :id", array(
                                ':id' => $company
                            )
                        );
                    }
                    $companyName = $import[$_POST['companyName']];
                }
            }
            ?>
            <hr class="margin-b-60" id="stap3" name="stap3">
            <h2 class="margin-t-40">Relaties ge&iuml;mporteerd</h2>
            <div class="row">
                <div class="col-md-6">
                    <p>Er zijn <?= count($_SESSION['import_csv']) - 1; ?> relaties geimporteerd.</p>
                </div>
            </div>

        <?php if ($user->getVar('clientPackage') == 'demo' && $user->getId() >= '1884') { ?>
            <script type="text/javascript" src="<?= BASE_URL; ?>assets/intro-js/minified/intro.min.js"></script>
            <script type="text/javascript">
                $(document).ready(function (e) {
                    introDashboard.start()
                });

                var introDashboard = introJs();
                introDashboard.setOptions({
                    steps: [
                        {
                            intro: "<p>Oké. Je hebt nu een aantal relaties toegevoegd. Dan heb je nu twee keuzes. We kunnen samen verder door de instellingen heen lopen of ik laten zien hoe je een project toevoegt onder een relatie.</p>"
                                + "<p><a href='/settings/?widget=settings' class='btn'>Verder gaan met instellingen</a> <a href='/companies/?widget=add_project' class='btn'>Project toevoegen</a></p>",
                        }
                    ],
                    disableInteraction: false,
                    exitOnOverlayClick: false,
                    showStepNumbers: false,
                    overlayOpacity: .15
                });

            </script>
        <?php } ?>

        <?php } ?>


    </div>
</div>
<?php if ($user->getVar('clientPackage') == 'demo' && $user->getId() >= '1884' && !isset($_FILES['import_companies'])) { ?>
    <script type="text/javascript" src="<?= BASE_URL; ?>assets/intro-js/minified/intro.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function (e) {
            window.addEventListener('load', function () {
                var doneTour = localStorage.getItem('DashboardTourCompanies') === 'Completed';
                if (doneTour) {
                    return;
                } else {
                    introDashboard.start()

                    introDashboard.oncomplete(function () {
                        localStorage.setItem('DashboardTourCompanies', 'Completed');
                    });

                    introDashboard.onexit(function () {
                        localStorage.setItem('DashboardTourCompanies', 'Completed');
                    });
                }
            });
        });

        var introDashboard = introJs();
        introDashboard.setOptions({
            steps: [
                {
                    element: '#import_companies',
                    intro: "<p>Upload hier je .csv bestand. Geef ook aan wat je als scheidingsteken hebt gebruikt. Klik vervolgens op ‘Bestand uploaden’.</p>",
                    position: 'right'
                }
            ],
            disableInteraction: false,
            exitOnOverlayClick: true,
            showStepNumbers: false,
            overlayOpacity: 0
        });

    </script>
<?php } ?>
