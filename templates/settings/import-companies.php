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


require_once 'classes/SimpleXLS.php';
// PRINT <pre> functie:
function pre($print){
    echo '<pre>'.print_r($print,true).'</pre>';
}

// Laat alle errors zien!
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
        <p>Selecteer hier het XLS bestand om te importeren</p>
        <form method="post" enctype="multipart/form-data" action="<?= BASE_URL; ?>settings/import-companies/#stap2">
            <div class="row">
                <div class="col-md-6">
                    <label>Bestand:</label>
                    <input type="file" class="form-control" value="" name="import_companies" id="import_companies">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Bestand uploaden</button>
            <a href="<?= BASE_URL; ?>settings/import-companies/" class="btn">Annuleren</a>
        </form>
        <?php
        if (isset($_FILES['import_companies'])) {

            if (isset($_FILES['import_companies'])) {
                // Upload bestand uitlezen
                $fileName = $_FILES['import_companies']['tmp_name'];
                if ( $xlsx = SimpleXLS::parse($fileName) ) {
                    $filecontent = $xlsx->rows();
                } else {
                    echo SimpleXLS::parseError();
                }

                // OPSCHONEN
                foreach($filecontent as $key => $row){
                    // Skip eerste paar rows, niet nodig
                    if($key < 3){continue;}
                    #if($key === 3) { // Testen met 1 klant
                    $needle = 'Klantnr.:';
                    // Elke row
                    foreach ($row as $col) {
                        // Na deze IF zijn het allemaal klanten!
                        if (strpos($col, 'Klantnr.:')) {
                            //echo $needle . ' Gevonden';
                            $client = explode('        ', $col);
                            $clientId = explode('Klantnr.:', $client[1]);

                            $moreInfoRow = $key + 1;
                            $moreInfo = $filecontent[$moreInfoRow];

                            // Check of de klant actief is
                            $actiefCol = $moreInfo[0]; // Voorbeeld: Actief: Ja  Actief vanaf: 02-11-2020  Inactief vanaf: -   (Actiefstatus hangt af van 'Actief vanaf' en 'Inactief vanaf')          Reden inactief:
                            $statusSplit = explode(' ', $actiefCol); // [0] = Actief: / [1] => Ja / [5] = Actief vanaf: 02-11-2020 / [9] = Inactief vanaf ... / [32] = Reden waarom inactief
                            $clientActive = $statusSplit[1]; // Ja of Nee

                            /*
                             * MORE INFO COL
                            adres = 2
                            telefoon = 4
                            postcode = 10
                            Debiteur = 15
                            Email = 31
                            Land = 19
                            KortingKlant = 25 // Ja / Nee
                            PrijsGroep = 6
                            */

                            if ($moreInfo[19] == '') {
                                $country = 'The Netherlands';
                            } else {
                                $country = $moreInfo[19];
                            }

                            if ($clientActive == 'Ja') {
                                $deleted = 0;
                                $clients[] = array(
                                    // ### Company Fields
                                    'companyId' => htmlspecialchars($clientId[1]),
                                    'companyName' => htmlspecialchars($client[0]),
                                    'companyBtw' => '',
                                    'companyKvk' => '',
                                    'companyClient' => '',
                                    'companySupplier' => '',
                                    'companyNote' => '',
                                    //'branchId' => htmlspecialchars($clientId[1]),
                                    'contactId' => '',
                                    'companyVat' => '',
                                    'clientId' => 0, // TODO: Overleggen waar deze voor nodig is!
                                    'companyIdExternal' => '',
                                    'discount' => '',
                                    'vatLiable' => '',
                                    'companyVia' => '',
                                    'companyDate' => '',
                                    'companyExtra1' => '',
                                    'companyExtra2' => '',
                                    'companyExtra3' => '',
                                    'companyExtra4' => '',
                                    'companyExtra5' => htmlspecialchars($moreInfo[6]), // TODO: Tijdelijk gebruiken voor de Prijsgroep
                                    'is_deleted' => 0, //
                                    'language' => '',
                                    // ### branches Fields
                                    'branchId' => '',
                                    'branchType' => 1, // TODO: INT, overleggen waar dit voor dient
                                    'branchName' => htmlspecialchars(ucfirst(strtolower($moreInfo[11]))),
                                    'branchStreet' => mb_strimwidth(htmlspecialchars($moreInfo[2]), 0, 45, "..."), // htmlspecialchars($moreInfo[2])
                                    'branchStreet2' => '',
                                    'branchZip' => htmlspecialchars($moreInfo[10]),
                                    'branchCity' => htmlspecialchars($moreInfo[11]),
                                    'branchCountry' => $country,
                                    'branchTelephone' => htmlspecialchars($moreInfo[4]),
                                    'branchFax' => htmlspecialchars($moreInfo[21]),
                                    'branchEmail' => htmlspecialchars($moreInfo[31]),
                                    'branchNote' => '',
                                    //'companyId' => '',
                                    //'companyId' => '',
                                    // ### contacts Fields
                                    //'contactId' => '',
                                    'contactName' => 'Contact',
                                    'contactPrefix' => '',
                                    'contactSurName' => 'Persoon',
                                    'contactEmail' => htmlspecialchars($moreInfo[31]),
                                    'contactTelephone' => htmlspecialchars($moreInfo[4]),
                                    'contactFax' => htmlspecialchars($moreInfo[21]),
                                    'contactBranch' => 0, // TODO: INT vragen waar deze voor dient
                                    'contactNote' => '',
                                    //'companyId' => '',
                                    'is_deleted' => $deleted,
                                    //'clientId' => '',
                                );
                            }
                        }
                    }
                }
                // /OPSCHONEN

                // Clients array aangemaakt
                $_SESSION['import_xls'] = $clients;
                $countClients = COUNT($_SESSION['import_xls']);
            }
            ?>
            <hr class="margin-b-60" id="stap2" name="stap2">
            <h2 class="margin-t-40"><?php echo 'Wij hebben <strong>'.$countClients.'</strong> relaties gevonden!';?></h2>
            <p>Klik op importeren om de import te starten!</p>
            <form method="post" class="loading import" action="<?= BASE_URL; ?>settings/import-companies/#stap3">
                <div class="row">
                    <div class="col-md-6">
                        <input type="submit" name="start_import" value="importeren" class="btn btn-primary">
                    </div>
                </div>
            </form>
        <?php } ?>

        <?php
        if(isset($_POST['start_import']) && $_POST['start_import'] == 'importeren'){
            //pre($_SESSION['import_xls']);
            $clients = $_SESSION['import_xls'];
            foreach ($clients as $key => $client) {
                if ($client['companyName'] != '') {
                    // Originele import vanaf Live!
                    $company = $dbClient->insert("companies", array(
                        'companyId' => $client['companyId'],
                        'companyName' => $client['companyName'],
                        'companyClient' => 1, // Hardcoded Klant ipv Levernacier TODO: Besprek met Peter of dit goed is!
                        'companySupplier' => 0, // Hardcoded Levernacier NIET TODO: Besprek met Peter of dit goed is!
                        'companyKvk' => $client['companyKvk'],
                        'companyVat' => $client['companyVat'],
                        'clientId' => 0
                    ));

                    $branch = $dbClient->insert("branches", array(
                        'branchName' => $client['branchName'],
                        'branchStreet' => $client['branchStreet'],
                        'branchZip' => $client['branchZip'],
                        'branchCity' => $client['branchCity'],
                        'branchCountry' => $client['branchCountry'],
                        'branchTelephone' => $client['branchTelephone'],
                        'branchFax' => $client['branchFax'],
                        'branchEmail' => $client['branchEmail'],
                        'companyId' => $company,
                        'branchNote' => $client['branchNote']
                    ));

                }
                $companyName = $client['companyName'];

                $contact = $dbClient->insert("contacts", array(
                    'contactName' => $client['contactName'],
                    'contactSurName' => $client['contactSurName'],
                    'contactPrefix' => $client['contactPrefix'],
                    'contactEmail' => $client['contactEmail'],
                    'contactTelephone' => $client['contactTelephone'],
                    'contactFax' => $client['contactFax'],
                    'contactBranch' => $branch,
                    'contactNote' => '',
                    'companyId' => $company
                ));
                if ($companyName != $client['companyName']) {
                    $company_update = $dbClient->update("companies", array(
                        'branchId' => $branch,
                        'contactId' => $contact,
                    ), "companyId = :id", array(
                            ':id' => $company
                        )
                    );
                }
            }
            ?>
            <hr class="margin-b-60" id="stap3" name="stap3">
            <h2 class="margin-t-40">Relaties ge&iuml;mporteerd</h2>
            <div class="row">
                <div class="col-md-6">
                    <p>Er zijn <?php echo count($clients); ?> relaties geimporteerd.</p>
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
            <?php
            //die();
        }
        ?>
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
