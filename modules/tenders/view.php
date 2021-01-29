<?php
/*
 * Project: start.jamespro.nl
 * File: /modules/tenders/view.php
 * Date: Jan 6, 2016
 */
if ($user->getVar('clientId') == '1'
    || $user->getVar('clientId') == '1264'
    || $user->getVar('clientId') == '1275'
    || $user->getVar('clientId') == '1113'
    || $user->getVar('clientId') >= '43127473'
) {
    $showBTW = true;
} else {
    $showBTW = true;
}


$tender = $dbClient->selectSingle('tenders', 'tenderId = :tenderId AND is_deleted = 0', array(':tenderId' => $id));
$company = $dbClient->selectSingle(
    'companies',
    'companyId = :companyId AND is_deleted = 0',
    array(
        ':companyId' => $tender['companyId']
    )
);

if (isset($_POST['templateAdd'])) {
    $template = $dbClient->selectSingle('tenderTemplates', 'tenderTemplateId = :tenderTemplateId', array(':tenderTemplateId' => $_POST['templateAdd']));
    $rows = $dbClient->select('tenderTemplateRows', 'tendertemplateId = :tendertemplateId ORDER BY tendertemplaterowOrder ASC', array(':tendertemplateId' => $_POST['templateAdd']));

    $order = time();

    if (isset($rows)) {
        foreach ($rows as $row) {
            if ($row['tendertemplaterowType'] != '100') {
                $insertRow = $dbClient->insert('tenderrows', array(
                    'tenderId' => $id,
                    'tenderrowType' => $row['tendertemplaterowType'],
                    'tenderrowBold' => $row['tendertemplaterowBold'],
                    'tenderrowDescription' => $row['tendertemplaterowDescription'],
                    'tenderrowValue' => $row['tendertemplaterowValue'],
                    'tenderrowOrder' => $order + $row['tendertemplaterowOrder'],
                    'tenderrowSelected' => $row['tendertemplaterowSelected'],
                ));
            } else {
                $calculation = $dbClient->selectSingle(
                    'calculations',
                    'id = :calculationId',
                    array(
                        ':calculationId' => $row['tendertemplaterowValue']
                    )
                );

                $calcRows = $dbClient->select(
                    'calcRows',
                    'calculationId = :calculationId',
                    array(
                        ':calculationId' => $calculation['id']
                    )
                );
                unset($calculation['id']);
                unset($calculation['tenderTemplateId']);

                $calculation['tenderId'] = $id;

                $insertcalculation = $dbClient->insert(
                    'calculations',
                    $calculation
                );

                foreach ($calcRows as $calcRow) {
                    unset($calcRow['id']);
                    $calcRow['calculationId'] = $insertcalculation;
                    $dbClient->insert(
                        'calcRows',
                        $calcRow
                    );
                }

                $insertRow = $dbClient->insert('tenderrows', array(
                    'tenderId' => $id,
                    'tenderrowType' => $row['tendertemplaterowType'],
                    'tenderrowBold' => $row['tendertemplaterowBold'],
                    'tenderrowDescription' => $row['tendertemplaterowDescription'],
                    'tenderrowValue' => $insertcalculation,
                    'tenderrowOrder' => $order + $row['tendertemplaterowOrder'],
                    'tenderrowSelected' => $row['tendertemplaterowSelected'],
                ));
            }
        }
    }

    header('location: ' . $_SERVER['REQUEST_URI']);
    exit();
}

if (isset($_GET['layout'])) {
    $update = $dbClient->update('tenders', array('layout' => $_GET['layout']), 'tenderId = :id', array(':id' => $id));
    header('location:' . BASE_URL . 'tenders/view/' . $id . '/');
    exit();
}

if (isset($_GET['deleteNote'])) {
    $delete = $dbClient->delete('notes', 'noteId = :noteId', array(':noteId' => $_GET['deleteNote']));
    echo $delete;
    exit();
}
if (isset($_POST['noteContentNew'], $_GET['editNote'])) {
    $update = $dbClient->update('notes', array('noteContent' => $_POST['noteContentNew']), 'noteId = :id', array(':id' => $_GET['editNote']));
    header('location: ' . $_SERVER['REQUEST_URI']);
    exit();
}
if (isset($_GET['copy'])) {
    $_SESSION['copyTender'] = $id;
    exit();
}
if (isset($_POST['saveAsTemplate'])) {
    require_once(dirname(__FILE__) . '/saveRowTemplate.php');
    exit;
}
if (isset($_POST['templateName'])) {
    require_once(dirname(__FILE__) . '/saveTemplateTender.php');
    exit;
}
if (isset($_POST['_action'])) {
    $calculation = $dbClient->update(
        'calculations',

        array(
            'description' => $_POST['description'],
            'amount' => $_POST['amount'],
        ),

        'id = :id',

        array(
            ':id' => $_POST['_calculationId']
        )
    );

    if ($showBTW === true) {
        $update2 = $dbClient->update('tenderrows', array('tenderrowSelected' => $_POST['tenderrowSelected']), 'tenderrowType = 100 AND tenderrowValue = :calcId', array(':calcId' => $_POST['_calculationId']));
    }


    $update = $dbClient->delete('calcRows', 'calculationId = :id', array(':id' => $_POST['_calculationId']));


    foreach ($_POST['name'] as $key => $name) {
        $calcRows = $dbClient->insert(
            'calcRows',
            array(
                'name' => $_POST['name'][$key],
                'price' => $_POST['price'][$key],
                'inkoop' => $_POST['priceInkoop'][$key],
                'code' => $_POST['code'][$key],
                'amount' => $_POST['amountrow'][$key],
                'korting' => str_replace(',', '.', $_POST['discount'][$key]),
                'calculationId' => $_POST['_calculationId'],
                'nacalculatie' => $_POST['nacalculatie'][$key],
                'extra1' => $_POST['extra1'][$key],
            )
        );
    }

    header('location: ' . $_SERVER['REQUEST_URI']);
    exit();
}


if (isset($_GET['copy'])) {
    $_SESSION['copyTender'] = $id;
    exit();
}

if (isset($_GET['editRow']) && $user->getPermission('tenders') >= 2) {
    $update = $dbClient->update(
        'tenderrows',
        array(
            'tenderrowDescription' => $_POST['tenderrowDescription'],
            'tenderrowValue' => $_POST['tenderrowValue'],
            'tenderrowSelected' => $_POST['tenderrowSelected'],
            'tenderrowType' => $_POST['tenderrowType'],
        ),
        'tenderrowId = :id',
        array(':id' => $_GET['editRow'])
    );
    echo $update;
    exit();
}

if (isset($_GET['changeBold']) && $user->getPermission('tenders') >= 2) {
    $update = $dbClient->update('tenderrows', array('tenderrowBold' => $_POST['bold']), 'tenderrowId = :id', array(':id' => $_POST['rowId']));
    echo $update;
    exit();
}
if (isset($_GET['changeIndent']) && $user->getPermission('tenders') >= 2) {
    $update = $dbClient->update('tenderrows', array('tenderrowIndent' => $_POST['indent']), 'tenderrowId = :id', array(':id' => $_POST['rowId']));
    echo $update;
    exit();
}

if (isset($_GET['deleteRow']) && $user->getPermission('tenders') >= 2) {
    $update = $dbClient->delete('tenderrows', 'tenderrowId = :id', array(':id' => $_POST['tenderrowId']));
    echo $update;
    exit();
}

if (isset($_GET['addCalculationTemplate']) && $user->getPermission('tenders') >= 2) {
    $calculationTemplate = $dbClient->selectSingle(
        'calculationsTemplates',
        'id = :id',
        array(
            ':id' => $_GET['addCalculationTemplate']
        )
    );
    $calculationTemplateRows = $dbClient->select(
        'calcRowsTemplate',
        'calculationTemplateId = :calculationTemplateId',
        array(
            ':calculationTemplateId' => $calculationTemplate['id']
        )
    );

    $calculation = $dbClient->insert('calculations', array(
        'description' => $calculationTemplate['description'],
        'amount' => 1,
        'tenderId' => $id,
    ));
    $tenderrow = $dbClient->insert('tenderrows', array(
        'tenderId' => $id,
        'tenderrowType' => 100,
        'tenderrowDescription' => 'calculatie',
        'tenderrowValue' => $calculation,
        'tenderrowOrder' => 100
    ));

    foreach ($calculationTemplateRows as $calculationTemplateRow) {
        if (floatval($company['discount']) != 0 && $user->getVar('clientId') != '1275') {
            $calculationTemplateRow['korting'] = floatval($company['discount']);
        }
        $calculationRow = $dbClient->insert('calcRows', array(
            'name' => $calculationTemplateRow['name'],
            'price' => $calculationTemplateRow['price'],
            'inkoop' => $calculationTemplateRow['inkoop'],
            'code' => $calculationTemplateRow['code'],
            'amount' => $calculationTemplateRow['amount'],
            'korting' => $calculationTemplateRow['korting'],
            'calculationId' => $calculation
        ));
    }
    header('location: ' . BASE_URL . 'tenders/view/' . $id . '/');
    exit();
}


if (isset($_POST['description'], $_POST['priceInkoop']) && $user->getPermission('tenders') >= 2) {
    $tenderrowOrder = $dbClient->selectSingle('tenderrows', 'tenderId = :tenderId ORDER BY tenderrowOrder DESC', array(':tenderId' => $id));
    if (isset($tenderrowOrder['tenderrowOrder'])) {
        $orderNew = $tenderrowOrder['tenderrowOrder'];
    } else {
        $orderNew = 0;
    }
    $tenderrow = $dbClient->insert('tenderrows', array(
        'tenderId' => $id,
        'tenderrowType' => 100,
        'tenderrowDescription' => 'calculatie',
        // 'tenderrowSelected' => $_POST['tenderrowSelected'],
        'tenderrowValue' => '',
        'tenderrowOrder' => $orderNew,
    ));
    $calculation = $dbClient->insert('calculations', array(
        'description' => $_POST['description'],
        'amount' => $_POST['amount'],
        'tenderId' => $id,
    ));
    $update = $dbClient->update('tenderrows', array('tenderrowValue' => $calculation), 'tenderrowId = :id', array(':id' => $tenderrow));
    foreach ($_POST['name'] as $key => $name) {
        $calculationRow = $dbClient->insert('calcRows', array(
            'name' => $_POST['name'][$key],
            'price' => $_POST['price'][$key],
            'inkoop' => $_POST['priceInkoop'][$key],
            'code' => $_POST['code'][$key],
            'amount' => $_POST['amountrow'][$key],
            'korting' => $_POST['discount'][$key],
            'extra1' => $_POST['extra1'][$key],
            'calculationId' => $calculation
        ));
    }

    // if ($_SERVER['REMOTE_ADDR'] == '77.242.112.190') {
    //     echo '<pre>'.print_r($_POST, true).'</pre>';
    //     exit();
    // }

    header('location: ' . $_SERVER['REQUEST_URI']);
    exit();
}

if (isset($_GET['addTenderRow']) && $user->getPermission('tenders') >= 2) {
    $selectSingle = $dbClient->selectSingle('tenderrows', 'tenderId = :tenderId', array(':tenderId' => $id), 'tenderrowOrder', 'tenderrowOrder DESC');

    $orderNew = $selectSingle['tenderrowOrder'] + 1;

    $insert = $dbClient->insert('tenderrows', array(
        'tenderId' => $id,
        'tenderrowType' => $_POST['tenderrowType'],
        'tenderrowBold' => $_POST['tenderrowBold'],
        'tenderrowDescription' => $_POST['tenderrowDescription'],
        'tenderrowValue' => $_POST['tenderrowValue'],
        'tenderrowOrder' => $orderNew,
//        'tenderrowOrder' =>$_POST['tenderrowOrder']
        'clientId' => $user->getVar('clientId'), // TODO: <- Deze heb ik toegevoegd, Kreeg een database fout bij insert, is dit wel de laatste versie?
    ));

    echo '        <div class="task-header tenderrows" id="' . $insert . '">
            <form class="taskForm" method="post">
                <a href="#" class="btn btn-grey deleteTenderRow"><i class="fa fa-trash"></i></a>
                <a href="#" class="btn btn-grey mover ui-sortable-handle"><i class="fa fa-bars"></i></a>
                <a href="' . BASE_URL . 'update/?copyTenderRow=true&amp;tenderRowId=' . $insert . '" class="btn btn-grey copyTenderRow" rel="' . $insert . '"><i class="fa fa-copy"></i></a>
                <textarea name="tenderrowDescription" id="tenderrowDescription" class="tenderrowDescription">' . $_POST['tenderrowDescription'] . '</textarea>
                <textarea name="tenderrowValue" id="tenderrowValue" class="tenderrowValue">' . $_POST['tenderrowValue'] . '</textarea>
                <div class="flexbox-1">
                <input type="text" name="tenderrowSelected" id="tenderrowSelected" class="tenderrowSelected" value="' . returnVat($_POST['tenderrowSelected']) . '" size="1">
                <select name="tenderrowType" id="tenderrowType" class="tenderrowType">
                    <option value="0"' . (($_POST['tenderrowType'] == 0) ? ' selected="selected"' : '') . '>' . $translate->__('tekst', false, false) . '</option>
                    <option value="2"' . (($_POST['tenderrowType'] == 2) ? ' selected="selected"' : '') . '>' . $translate->__('prijs', false, false) . '</option>
                    <option value="5"' . (($_POST['tenderrowType'] == 5) ? ' selected="selected"' : '') . '>' . $translate->__('subtotaal', false, false) . '</option>
                    <option value="7"' . (($_POST['tenderrowType'] == 7) ? ' selected="selected"' : '') . '>' . $translate->__('totaal btw', false, false) . '</option>
                    <option value="6"' . (($_POST['tenderrowType'] == 6) ? ' selected="selected"' : '') . '>' . $translate->__('totaal incl. btw', false, false) . '</option>
                    <option value="4"' . (($_POST['tenderrowType'] == 4) ? ' selected="selected"' : '') . '>' . $translate->__('totaal excl. btw', false, false) . '</option>
                    <option value="1"' . (($_POST['tenderrowType'] == 1) ? ' selected="selected"' : '') . '>' . $translate->__('blanco', false, false) . '</option>
                    <option value="3"' . (($_POST['tenderrowType'] == 3) ? ' selected="selected"' : '') . '>' . $translate->__('pagina', false, false) . '</option>
                </select>
                <a href="#" class="btn' . (($_POST['tenderrowBold'] == 1) ? ' btn-green' : ' btn-grey') . ' toggleBold"><i class="fa fa-bold"></i></a>
                <a href="#" class="btn btn-green saveTenderRow" data-id="' . $insert . '"><i class="fa fa-save"></i></a>
                </div>
            </form>
        </div>
';
    exit();
}
if (isset($_POST['taskContent']) && $user->getPermission('tenders') >= 2) {
    foreach ($_POST['taskUser'] as $userTask) {
        $insert = $dbClient->insert('tasks', array(
            'taskContent' => $_POST['taskContent'],
            'taskTimePlanned' => $_POST['taskTimePlanned'],
            'taskTimeStop' => $_POST['taskTimeStop'],
            'taskTimeStart' => $_POST['taskTimeStart'],
            'taskDate' => $_POST['taskDate'],
            'taskState' => 0,
            'userId' => $userTask,
            'tenderId' => $id,
            'taskPrio' => ((isset($_POST['priority'])) ? 1 : 0),
            'taskMadeBy' => $user->getId(),
            'taskMadeDate' => date('Y-m-d H:i:s'),
            'taskRepeat' => ((isset($_POST['repeat'])) ? $_POST['repeat'] : ''),
        ));
    }
    header('location: ' . $_SERVER['REQUEST_URI']);
    exit();
}

if ($user->getPermission('tenders') >= '2') {
    if (isset($_POST['note']) && $user->getPermission('tenders') >= 2) {
        $insert = $dbClient->insert('notes', array(
            'noteContent' => $_POST['note'],
            'noteDate' => date('Y-m-d H:i:s'),
            'noteType' => 'tender',
            'noteTypeId' => $id,
            'userId' => $user->getId()
        ));
        header('location: ' . $_SERVER['REQUEST_URI']);
        exit();
    }

    if (isset($_GET['addTag'])) {
        $dbClient->delete('tags', 'tagType = :tagType AND tagTypeId = :tagTypeId AND tagValue = :tagValue', array(
            'tagType' => 'tender',
            'tagTypeId' => $id,
            'tagValue' => $_GET['addTag']
        ));
        $insert = $dbClient->insert('tags', array(
            'tagType' => 'tender',
            'tagTypeId' => $id,
            'tagValue' => $_GET['addTag']
        ));
    }
    if (isset($_GET['removeTag'])) {
        $insert = $dbClient->delete('tags', 'tagType = :tagType AND tagTypeId = :tagTypeId AND tagValue = :tagValue', array(
            ':tagType' => 'tender',
            ':tagTypeId' => $id,
            ':tagValue' => $_GET['removeTag']
        ));
    }
}

if ($user->getPermission('tenders') >= '1') {
    if ($tender['version'] == '2') {
        $url = 'location: https://app.jamespro.nl/#!/offers/' . $tender['tenderId'];
        if (isset($_GET['saveTemplate'])) {
            $url .= '?saveTemplate=true';
        }
        header($url);
        exit();
    }

    $contact = $dbClient->selectSingle(
        'contacts',
        'contactId = CASE
            WHEN EXISTS(
            SELECT 1 FROM contacts WHERE contactId = :contactId
            LIMIT  1
            ) THEN :contactId
            ELSE :companyContactId
            END AND is_deleted = 0',
        array(
            ':contactId' => $tender['contactId'],
            ':companyContactId' => $company['contactId']
        )
    );
    $branch = $dbClient->selectSingle(
        'branches',
        'CASE
            WHEN EXISTS(SELECT 1 FROM branches WHERE branchId = :branchId LIMIT  1 ) THEN branchId = :branchId
            WHEN EXISTS(SELECT 1 FROM branches WHERE branchId = :companyBranchId LIMIT  1 ) THEN branchId = :companyBranchId
            ELSE companyId = :companyId
            END AND is_deleted = 0',
        array(
            ':branchId' => $tender['branchId'],
            ':companyBranchId' => $company['branchId'],
            ':companyId' => $company['companyId'],
        )
    );

    $contacts = $dbClient->select(
        'contacts',
        'companyId = :companyId AND is_deleted = 0',
        array(
            ':companyId' => $company['companyId']
        )
    );

    $branches = $dbClient->select(
        'branches',
        'companyId = :companyId AND is_deleted = 0',
        array(
            ':companyId' => $company['companyId']
        )
    );

    $files = $dbClient->select(
        'files',
        'fileType = :type AND fileTypeId = :projectId',
        array(
            ':type' => 'tender',
            ':projectId' => $id
        ),
        'fileId,fileTypeId,fileName,fileMime,fileSize',
        'fileId DESC'
    );
    $tasks = $dbClient->select(
        'tasks',
        'tenderId = :tenderId AND is_deleted = 0',
        array(
            ':tenderId' => $id
        ),
        '*',
        'taskDate DESC'
    );
    $notes = $dbClient->select('notes', 'noteType = :noteType AND noteTypeId = :noteTypeId', array(':noteType' => 'tender', ':noteTypeId' => $id),'*','noteDate DESC');
    $rows = $dbClient->select('tenderrows', 'tenderId = :tenderId ORDER BY tenderrowOrder ASC', array(':tenderId' => $id));

    $tags = $dbClient->select(
        'tags',
        'tagType = :type AND tagTypeId = :tagTypeId group by tagValue',
        array(
            ':type' => 'tender',
            ':tagTypeId' => $id
        ),
        'tagId,tagValue'
    );
    $project = $dbClient->selectSingle('tenders2projects', 'tenderId = :tenderId', array(':tenderId' => $id));


    if (isset($_POST['projectState']) && $project) {
        $update = array(
            'projectState' => $_POST['projectState']
        );
        $bind = array(
            ':projectId' => $project['projectId'],
        );
        $dbClient->update('projects', $update, 'projectId = :projectId LIMIT 1', $bind);
        header('location: ' . BASE_URL . 'tenders/view/' . $id . '/');
        exit();
    }


    $calculationsTemplates = $dbClient->select('calculationsTemplates', '', '', '*', 'description ASC');

    $purchase = 0;
    $margin = 0;
    $marginPercentage = 0;
    $sales = 0;
    $discount = 0;
    $discountPercentage = 0;
    $salesAfterDiscount = 0;
    $gross = 0;
    $grossPercentage = 0;
    $grossCalculation = 0;
    $grossCalculationPercentage = 0;

//    echo "<!-- 1 sales: $sales -->\n";

    foreach ($rows as $row) {
        if ($row['tenderrowType'] == '2') {
            $sales += round(str_replace(',', '.', $row['tenderrowValue']), 2);
//            echo "<!-- 2 sales: $sales -->\n";
        } elseif ($row['tenderrowType'] == '100') {
            $calculatie = $dbClient->selectSingle('calculations', 'id=:id', array(':id' => $row['tenderrowValue']));
            $calcRows = $dbClient->select('calcRows', 'calculationId=:calculationId', array(':calculationId' => $calculatie['id']), "*, REPLACE(price, ',', '.') AS price, REPLACE(inkoop, ',', '.') AS inkoop, REPLACE(amount, ',', '.') AS amount");

            foreach ($calcRows as $calcRow) {
                $iKorting = $calcRow['korting'];
                if ($iKorting > 0) {
                    $discount += ($calcRow['price'] / 100 * ($iKorting)) * $calcRow['amount'] * $calculatie['amount'];
                }
                $price = $calcRow['price'] * $calcRow['amount'] * $calculatie['amount'];
                $sales += $price;

                $grossCalculation += round($calcRow['inkoop'] * $calcRow['nacalculatie'] * $calculatie['amount'], 2);
                $purchase += round($calcRow['inkoop'] * $calcRow['amount'] * $calculatie['amount'], 2);
//                echo "<!-- 3 sales: $sales -->\n";
            }
        }
    }
    $sales = round($sales, 2);
    $salesAfterDiscount = $sales - $discount;
    $margin = $sales - $purchase;
    $marginPercentage = ($sales / $purchase * 100) - 100;
    $discountPercentage = ($discount / $sales * 100);
    $gross = $sales - $purchase - $discount;
    $grossPercentage = ($gross / $salesAfterDiscount) * 100;

    if ($grossCalculation != 0) {
        $grossCalculation = $salesAfterDiscount - $grossCalculation;
        $grossCalculationPercentage = $grossCalculation / $salesAfterDiscount * 100;
    }
    $templates = $dbClient->select('tenderTemplates', '', '', '*', 'tenderTemplateName ASC');
} else {
    $accessdenied = true;
}
