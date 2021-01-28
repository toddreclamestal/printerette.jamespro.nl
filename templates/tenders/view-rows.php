<?php
/*
 * Project: start.jamespro.nl
 * Author: petermol
 * File: companies/view-tenders.php
 * Date: Jan 6, 2016
 *
 * In dit document gaan we de opmaak bepalen.
 *
 */


$newDiscount = 0;
if (floatval($company['discount']) != 0 && $user->getVar('clientId') != '1275') {
    $newDiscount = floatval($company['discount']);
}


?>
<div class="needspaging">
    <div class="row hidden-xs">
        <div class="col-sm-12">
            <div class="bg-blue task-header">
                <form class="taskForm d-flex flex-row" method="post" id="addTenderRow">
                    <a href="#" class="btn btn-orange addproduct"><i class="fa fa-folder-open"></i></a>
                    <a href="#" class="btn btn-orange addcalc"><i class="fa fa-calculator"></i></a>
                    <a href="?addTenderRow=true" class="btn btn-orange addblankline" data-content="tenderrowDescription=&amp;tenderrowValue=&amp;tenderrowType=1&amp;tenderrowOrder=<?php echo count($rows); ?>&amp;tenderrowBold=0" data-target="#tenderRows"><i class="fa fa-file"></i></a>
                    <input type="text" name="tenderrowDescription" id="tenderrowDescription" class="tenderrowDescription col" placeholder="<?php $translate->__('description'); ?>">
                    <input type="text" name="tenderrowValue" id="tenderrowValue" class="tenderrowValue col" placeholder="<?php $translate->__('value'); ?>">
                    <?php if ($showBTW === true) {
                        ?>
                        <input type="text" name="tenderrowSelected" id="tenderrowSelected" class="tenderrowSelected" value="21" size="2" min="0" step="1">
                        <?php
                    } ?>
                    <select name="tenderrowType" id="tenderrowType">
                        <option value="0"><?php $translate->__('tekst'); ?></option>
                        <option value="2"><?php $translate->__('prijs'); ?></option>
                        <option value="5"><?php $translate->__('subtotaal'); ?></option>
                        <option value="4"><?php $translate->__('totaal excl. btw'); ?></option>
                        <option value="7"><?php $translate->__('totaal btw'); ?></option>
                        <option value="6"><?php $translate->__('totaal incl. btw'); ?></option>
                        <option value="1"><?php $translate->__('blanco'); ?></option>
                        <option value="3"><?php $translate->__('pagina'); ?></option>
                    </select>
                    <input type="hidden" value="<?php echo count($rows) + 1; ?>" name="tenderrowOrder" id="tenderrowOrder">
                    <input type="hidden" value="0" name="tenderrowBold" id="tenderrowBold">
                    <a href="#" class="btn btn-grey toggleBoldNew"><i class="fa fa-bold"></i></a>
                    <a href="#" class="btn btn-green submitTenderRow"><i class="fa fa-save"></i></a>
                    <input type="submit" class="d-none">
                </form>
            </div>
        </div>
    </div>

    <?php
    $tenderHeader = $tender['tenderHeader'];
    if ($tenderHeader == '') {
        $tenderHeader = getSetting('tenderHeader');
    }

    $findName = '%contactpersoon%';
    $replaceName = $contact['contactName'];
    if ($contact['contactSurName'] != '') {
        $replaceName .= ' ' . $contact['contactSurName'];
    }

    $findReplace = array(
        $findName,
        '%voornaam%',
        '%achternaam%',
        '%contactpersoon%',
        '%bedrijfsnaam%',
        '%gebruikersnaam%',
    );
    $replaceReplace = array(
        $replaceName,
        $contact['contactName'],
        $contact['contactSurName'],
        (($contact['contactSurName'] != '') ? $contact['contactName'] . ' ' . $contact['contactSurName'] : $contact['contactName']),
        $company['companyName'],
        $user->getVar('userName'),
    );


    ?>
    <div class="editable tenderHeader bordered"><?php echo str_replace($findReplace, $replaceReplace, $tenderHeader); ?></div>

    <div id="tenderRows">
        <?php foreach ($rows as $row) {
            if ($row['tenderrowType'] != '100') {
                ?>
                <div class="task-header tenderrows" id="<?php echo $row['tenderrowId']; ?>">
                    <form class="taskForm d-flex flex-row" method="post">
                        <a href="#" class="btn btn-grey deleteTenderRow"><i class="fa fa-trash"></i></a>
                        <a href="#" class="btn btn-grey mover"><i class="fa fa-bars"></i></a>
                        <a href="<?= BASE_URL; ?>update/?copyTenderRow=true&amp;tenderRowId=<?php echo $row['tenderrowId']; ?>" class="btn btn-grey copyTenderRow" rel="<?php echo $row['tenderrowId']; ?>"><i class="fa fa-copy"></i></a>
                        <textarea rows="1" name="tenderrowDescription" id="tenderrowDescription" class="tenderrowDescription"><?php echo $row['tenderrowDescription']; ?></textarea>
                        <textarea rows="1" name="tenderrowValue" id="tenderrowValue" class="tenderrowValue"><?php echo $row['tenderrowValue']; ?></textarea>

                        <?php if ($showBTW === true) {
                            ?>
                            <input type="text" name="tenderrowSelected" id="tenderrowSelected" class="tenderrowSelected" value="<?php echo returnVat($row['tenderrowSelected']); ?>" size="2">
                            <?php
                        } ?>
                        <select name="tenderrowType" id="tenderrowType" class="tenderrowType">
                            <option value="0"<?php if ($row['tenderrowType'] == 0) {
                                ?> selected="selected"<?php
                            } ?>><?php $translate->__('tekst'); ?></option>
                            <option value="2"<?php if ($row['tenderrowType'] == 2) {
                                ?> selected="selected"<?php
                            } ?>><?php $translate->__('prijs'); ?></option>
                            <option value="5"<?php if ($row['tenderrowType'] == 5) {
                                ?> selected="selected"<?php
                            } ?>><?php $translate->__('subtotaal'); ?></option>
                            <option value="4"<?php if ($row['tenderrowType'] == 4) {
                                ?> selected="selected"<?php
                            } ?>><?php $translate->__('totaal excl. btw'); ?></option>
                            <option value="7"<?php if ($row['tenderrowType'] == 7) {
                                ?> selected="selected"<?php
                            } ?>><?php $translate->__('totaal btw'); ?></option>
                            <option value="6"<?php if ($row['tenderrowType'] == 6) {
                                ?> selected="selected"<?php
                            } ?>><?php $translate->__('totaal incl. btw'); ?></option>
                            <option value="6"><?php $translate->__('totaal incl. btw'); ?></option>
                            <option value="1"<?php if ($row['tenderrowType'] == 1) {
                                ?> selected="selected"<?php
                            } ?>><?php $translate->__('blanco'); ?></option>
                            <option value="3"<?php if ($row['tenderrowType'] == 3) {
                                ?> selected="selected"<?php
                            } ?>><?php $translate->__('pagina'); ?></option>
                        </select>
                        <a href="#" class="btn<?php if ($row['tenderrowBold'] == 1) {
                            ?> btn-green<?php
                        } else {
                            ?> btn-grey<?php
                        } ?> toggleBold"><i class="fa fa-bold"></i></a>
                        <a href="#" class="btn btn-green saveTenderRow" data-id="<?php echo $row['tenderrowId']; ?>"><i class="fa fa-save"></i></a>
                    </form>
                </div>
                <?php
            } else {
                $calculatie = $dbClient->selectSingle('calculations', 'id=:id', array(':id' => $row['tenderrowValue']));
                $calcRows = $dbClient->select('calcRows', 'calculationId=:calculationId', array(':calculationId' => $calculatie['id']), "*, REPLACE(price, ',', '.') AS price, REPLACE(inkoop, ',', '.') AS inkoop, REPLACE(amount, ',', '.') AS amount");


                $price = 0;
                foreach ($calcRows as $calcRow) {
                    $iKorting = $calcRow['korting'];
                    if ($iKorting > 0 || $iKorting < 0) {
                        $price += ($calcRow['price'] / 100 * (100 - $iKorting)) * $calcRow['amount'] * $calculatie['amount'];
                    } else {
                        $price += $calcRow['price'] * $calcRow['amount'] * $calculatie['amount'];
                    }
                } ?>

                <div class="task-header tenderrows" id="<?php echo $row['tenderrowId']; ?>">
                    <form class="taskForm d-flex flex-row" method="post">
                        <a href="#" class="btn btn-grey deleteTenderRow"><i class="fa fa-trash"></i></a>
                        <a href="#" class="btn btn-grey mover"><i class="fa fa-bars"></i></a>
                        <a href="<?= BASE_URL; ?>update/?copyTenderRow=true&amp;tenderRowId=<?php echo $row['tenderrowId']; ?>" class="btn btn-grey copyTenderRow" rel="<?php echo $row['tenderrowId']; ?>"><i class="fa fa-copy"></i></a>
                        <input type="text" name="tenderrowDescription" id="tenderrowDescription" class="tenderrowDescription" value="<?php echo $calculatie['description'];; ?>" disabled="disabled">
                        <input type="text" name="tenderrowValue" id="tenderrowValue" class="tenderrowValue" value="<?php echo number_format($price, 2, ',', '.'); ?>" disabled="disabled">
                        <?php if ($showBTW === true) {
                            ?>
                            <input type="text" name="tenderrowSelected" id="tenderrowSelected" class="tenderrowSelected" value="<?php echo returnVat($row['tenderrowSelected']); ?>" size="2" disabled="disabled">
                            <?php
                        } ?>

                        <?php
                        if ($showBTW === true) {
                            $calculatie['tenderrowSelected'] = returnVat($row['tenderrowSelected']);
                        } ?>
                        <div style="width: 158px;"></div>
                        <a href="#" class="btn btn-orange editcalc" data-information="<?= htmlspecialchars(json_encode($calculatie)); ?>" data-rows="<?= htmlspecialchars(json_encode($calcRows)); ?>" rel="<?= $calculatie['id']; ?>"><i class="fa fa-edit"></i></a>
                    </form>
                </div>
                <?php
            }
        } ?>
    </div>
    <div class="saveAllRows text-right margin-b-10">
        <a href="#" class="btn btn-green" data-container="#tenderRows" data-target="a.saveTenderRow"><i class="fa fa-save"></i> <?php $translate->__('save all', true); ?></a>
    </div>
    <div class="editable tenderFooter bordered margin-b-10"><?php
        echo str_replace(
            array(
                '%contactpersoon%',
                '%voornaam%',
                '%achternaam%',
                '%bedrijfsnaam%',
                '%gebruikersnaam%'
            ),
            array(
                $replaceName,
                $contact['contactName'],
                $contact['contactSurName'],
                $company['companyName'],
                $user->getVar('userName'),
            ),
            ($tender['tenderFooter'] != "") ? $tender['tenderFooter'] :
                str_replace(
                    array(
                        '%betalingstermijn%'
                    ),
                    array(
                        (!empty($company['companyPaymentDays'])) ? '14' : $company['companyPaymentDays']
                    ),
                    getSetting('tenderPayment')
                )
        ); ?></div>

</div>
<script type="text/javascript">

    function calculate(el) {
        //console.log($(this));
        var item = el;
        var string = item.val();
        //console.log(string);
        string = string.replace(',', '.');
        //console.log(string);
        if (string.length > 0) {
            string = eval(string);
            //console.log(string);
            item.val(eval(string));
        }
        return false;
    }

    $(document).ready(function () {
        $('#page div.calculationEditer, #page div.calculationCreator').on('blur', 'input', function (evt) {
            if ($(this).hasClass('loadjson') == false) {
                calculate($(this));
            } else {
                setTimeout(function () {
                    console.log('test');
                    $('div.list_input').remove();
                }, 500);

            }
            var inkoop = $(this).parent().parent().find('input.inkoop').val();
            var verkoop = $(this).parent().parent().find('input.verkoop').val();
            if ($(this).hasClass('inkoop') || ($(this).hasClass('verkoop'))) {
                if (inkoop > 0 && verkoop > 0) {
                    $(this).parent().parent().find('input.marge').val(calculateMarge(inkoop, verkoop));
                }

            } else if ($(this).hasClass('marge')) {
                var marge = Math.round((1 + ($(this).val() / 100)) * 10000000000) / 10000000000;
                if (marge > 1) {
                    $(this).parent().parent().find('input.verkoop').val((inkoop * marge))
                }
            }
            calcTotal($('#calculationEditor'));
            calcTotal($('#calculationTender'));
        });
    });


    function deleteRow(el) {
        el.closest('tr').remove();

        calcTotal($('#calculationEditor'));
        calcTotal($('#calculationTender'));
    }


    //peter
    $(document).on('click', '.deletecalcrow', function (e) {
        e.preventDefault();
        deleteRow($(this));
    });


    function calculateMarge(inkoop, verkoop) {
        return Math.round(((verkoop - inkoop) / inkoop * 100) * 1000000) / 1000000;
    }

    function calcTotal(editor) {
        var totalInkoop = 0
        var totalVerkoop = 0;
        var totalVerkoopKorting = 0;
        var totalMarge = 0;
        var totalKorting = 0;
        var rows = editor.find('.rows tr');

        rows.each(function (index, row) {
            totalInkoop += $(row).find('input.inkoop').val() * $(row).find('input#amount').val();
            totalVerkoop += $(row).find('input.verkoop').val() * $(row).find('input#amount').val();
            totalVerkoopKorting += $(row).find('input.verkoop').val() * $(row).find('input#amount').val() * (1 - ($(row).find('input#discount').val() / 100));

        });

        if (totalVerkoop != 0 && totalInkoop != 0) {
            totalMarge = ((totalVerkoop / totalInkoop) - 1) * 100;
            totalKorting = 100 / (totalVerkoop / (totalVerkoop - totalVerkoopKorting));
        }

        editor.find('[data-total=inkoop]').text(financialJames(totalInkoop));
        editor.find('[data-total=verkoop]').text(financialJames(totalVerkoopKorting));
//		editor.find('[data-total=verkoop]').text(totalVerkoop.toFixed(2));
        editor.find('[data-total=marge]').text(financialJames(totalMarge) + '%');
        editor.find('[data-total=korting]').text(financialJames(totalKorting) + '%');
    }

    function toFixedNew(num, precision) {
        return (+(Math.round(+(num + 'e' + precision)) + 'e' + -precision)).toFixed(precision);
    }

    function financialJames(x) {
        return toFixedNew(Number.parseFloat(x), 2);
    }

    $('#page').on('click', '.editcalc', function (e) {
        e.preventDefault();

        info = JSON.parse($(this).attr('data-information'));
        data = JSON.parse($(this).attr('data-rows'));
        var editor = $('#calculationEditor');

        editor.modal().find('table').find('tbody').empty();
        editor.find('input[name="description"]').val(info.description);
        editor.find('input[name="amount"]').val(info.amount);
        <?php if ($showBTW === true) {
        ?>
        editor.find('input[name="tenderrowSelected"]').val(info.tenderrowSelected);
        <?php
        }?>
        editor.find('input[name="_calculationId"]').val(info.id);

        for (var key in data) {
            if (!data.hasOwnProperty(key)) continue;
            var obj = data[key];

            var korting = "";


//		    if (obj.korting == parseInt(obj.korting) && obj.korting > 0) {
            korting = obj.korting;
//		    }
            //console.log(obj);
            $('#calculationEditor').find('table').find('tbody').append(
                '<tr>\n\
                    <td><input type="text" class="form-control loadjson" name="name[' + obj['id'] + ']" value="' + obj['name'] + '" id="name" autocomplete="off"><input type="hidden" class="" name="code[]" id="code" value=""></td>\n\
					<td width="10%"><input type="text" class="form-control calculate" name="amountrow[' + obj['id'] + ']" value="' + obj['amount'] + '" id="amount" autocomplete="off"></td>\n\
					<td width="10%"><input type="text" class="form-control inkoop" name="priceInkoop[' + obj['id'] + ']" value="' + obj['inkoop'] + '" id="priceInkoop" autocomplete="off" onkeydown="return keyispressed(event,$(this));"></td>\n\
					<td width="10%"><input type="text" class="form-control verkoop" name="price[' + obj['id'] + ']" value="' + obj['price'] + '" id="price" autocomplete="off" onkeydown="return keyispressed(event,$(this));"></td>\n\
					<td width="10%"><input type="text" class="form-control marge" name="marge[' + obj['id'] + ']" id="marge" autocomplete="off" onkeydown="return keyispressed(event,$(this));"></td>\n\
					<td width="10%"><input type="text" class="form-control" name="discount[' + obj['id'] + ']" value="' + korting + '" id="discount" autocomplete="off" onkeydown="return keyispressed(event,$(this));"></td>\n\
					<td width="10%"><input type="text" class="form-control" name="nacalculatie[' + obj['id'] + ']" value="' + (obj['nacalculatie'] || '') + '" id="nacalculatie" autocomplete="off" onkeydown="return keyispressed(event,$(this));"></td>\n\
					<td width="10%"><input type="text" class="form-control" name="extra1[' + obj['id'] + ']" value="' + (obj['extra1'] || '') + '" id="extra1" autocomplete="off" style="min-width:50px;" data-toggle="tooltip" data-placement="top"></td>\n\
					<td width="75"><a href="#" class="btn btn-sm btn-grey deletecalcrow" style="height:34px;line-height:25px;"><i class="fa fa-trash"></i></a> <a href="#" class="btn btn-sm move" style="height:34px;line-height:25px;"><i class="fa fa-bars"></i></a></td>\n\
				</tr>'
            ).find('input.verkoop').blur();
        }
        $("tbody.ui-sortable").sortable(
            {
                placeholder: "dd-placeholder",
                handle: "a.move"
            }
        );
        calcTotal(editor);

    });


    $(document).ready(function () {

        $inputs = $('#tenderRows').find('select.tenderrowType');
        $inputs.each(function (e) {
            if ($(this).val() == '3') {
                $(this).closest('div.tenderrows.task-header').css('background', '#EEE');
            } else {
                $(this).closest('div.tenderrows.task-header').css('background', '');
            }
        });

        $(document).on("change", "select.tenderrowType", function (e) {
            $(this).closest(".taskForm").find(".saveTenderRow").trigger("click")
            if ($(this).val() == '3') {
                $(this).closest('div.tenderrows.task-header').css('background', '#EEE');
            } else {
                $(this).closest('div.tenderrows.task-header').css('background', '');
            }
        })

        tinymce.init({
            selector: '.editable.tenderHeader',
            <?php if ($user->getVar('clientId') == 1252) {
            ?>
            force_br_newlines: true,
            force_p_newlines: false,
            forced_root_block: '',
            <?php
            } ?>
            skin: "lightgray",
            inline: true,
            plugins: [
                'advlist autolink lists link charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime table contextmenu paste',
                'autoresize'
            ],
            menubar: false,
            toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link',
            setup: function (ed) {
                ed.on('blur', function (e) {
                    var content = {'content': ed.getContent()};
                    $('.loader-ajax-requests').fadeIn();
                    $.post('<?= BASE_URL; ?>update/?update=tender&tender=<?php echo $tender['tenderId'];?>&type=tenderHeader', content, function () {
                        $('.loader-ajax-requests').fadeOut();
                    });
                });
            }
        });
        tinymce.init({
            selector: '.editable.tenderFooter',
            <?php if ($user->getVar('clientId') == 1252) {
            ?>
            force_br_newlines: true,
            force_p_newlines: false,
            forced_root_block: '',
            <?php
            } ?>
            skin: "lightgray",
            inline: true,
            plugins: [
                'advlist autolink lists link charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime table contextmenu paste',
                'autoresize'
            ],
            menubar: false,
            toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link ',
            style_formats: [
                {title: 'Small', inline: 'small'},
            ],
            style_formats_merge: true,
            setup: function (ed) {
                ed.on('blur', function (e) {
                    var content = {'content': ed.getContent()};
                    $('.loader-ajax-requests').fadeIn();
                    $.post('<?= BASE_URL; ?>update/?update=tender&tender=<?php echo $tender['tenderId'];?>&type=tenderFooter', content, function () {
                        $('.loader-ajax-requests').fadeOut();
                    });
                });
            }
        });

        tinymce.init({
            selector: 'h2.editable',
            inline: true,
            toolbar: 'undo redo',
            menubar: false,
            paste_as_text: true,
            plugins: "paste",
            setup: function (ed) {
                ed.on('blur', function (e) {
                    var content = {'content': ed.getContent()};
                    $('.loader-ajax-requests').fadeIn();
                    $.post('<?= BASE_URL; ?>update/?update=tender&tender=<?php echo $tender['tenderId'];?>&type=tenderTitle', content, function () {
                        $('.loader-ajax-requests').fadeOut();
                    });
                });
            }
        });

        $('body').delegate('input.loadjson', 'input', function (event) {
            var selected = $(this);

            var keycode = event.which;
            var keys = [39, 9, 17, 16, 20, 18, 91, 13, 27, 32];
            var found = $.inArray(keycode, keys) > -1;
            if (selected.val().length >= 3) {
//    	if(selected.val().length >= 3 && found == false){
                var amountCalc = eval(selected.parent().next('td').find('input').val() * $('#amount').val());
                var amountParentCalc = eval($('.calculationAmount').val());
                var query = 'query=' + encodeURIComponent(selected.val()) + '&amount=' + amountCalc;
                var url = "/v2/tenders/json-products/";

                $.get(url, query, function (data) {
                    if ($('div.list_input').length == 0) {
                        selected.after('<div class="list_input"></div>');
                    } else {
                        $('div.list_input').html('');
                    }
                    $.each(data, function (i, item) {
                        $('div.list_input').append('<a href="#" rel="' + i + '" class="list_input_item">' + item.name + '</a>');
                    });
                    $('a.list_input_item').click(function (e) {
                        var item = $(this);

                        selected.val(item.html());
                        selected.parent().parent().find('#priceInkoop').val(data[item.attr('rel')].price_gross);
                        selected.parent().parent().find('#code').val(data[item.attr('rel')].id);
                        selected.parent().parent().find('#price').val(data[item.attr('rel')].price_net);
                        selected.parent().parent().find('#marge').val(data[item.attr('rel')].percentage);
                        selected.parent().parent().find('#extra1').val(data[item.attr('rel')].extra1);

                        calcTotal($('#calculationEditor'));
                        calcTotal($('#calculationTender'));

                        $('div.list_input').delay(500).remove();
                        return false;
                    });
                }, "json");
            }
        });

        <?php if ($user->getVar('clientId') != '1170' && $user->getVar('clientId') != '1004') {
        ?>
        $('body').delegate('input#amount', 'change', function (event) {
            var selected = $(this);

            var keycode = event.which;
            var keys = [39, 9, 17, 16, 20, 18, 91, 13, 27, 32];
            var found = $.inArray(keycode, keys) > -1;
            console.log(found);
            if (selected.val().length >= 1 && found == false) {
                var amountCalc = eval(selected.val());

                var query = selected.parent().prev('td').find('input').val();

                query = 'query=' + encodeURIComponent(query) + '&amount=' + amountCalc;
                var url = "/tenders/json-products/?query=";
                $.get(url, query, function (data) {
                    if (data[0]) {
                        selected.parent().parent().find('#priceInkoop').val(data[0].price_gross);
                        selected.parent().parent().find('#code').val(data[0].id);
                        selected.parent().parent().find('#price').val(data[0].price_net);
                        selected.parent().parent().find('#marge').val(data[0].percentage);
                        selected.parent().parent().find('#extra1').val(data[0].extra1);
                        calcTotal($('#calculationEditor'));
                        calcTotal($('#calculationTender'));
                    }
                }, "json");
            }
        });
        <?php
        } else {
        ?>
        $('body').delegate('input#amount', 'change', function (event) {
            var selected = $(this);
            var keycode = event.which;
            var keys = [39, 9, 17, 16, 20, 18, 91, 13, 27, 32];
            var found = $.inArray(keycode, keys) > -1;
            console.log(found);
            calcTotal($('#calculationEditor'));
            calcTotal($('#calculationTender'));
        });
        <?php
        }
        ?>



        $('a.addcalcrow').on('click', function (e) {
            e.preventDefault();
            $(this).closest('table').find('tbody').append(
                '<tr>\n\
                <td><input type="text" class="form-control loadjson" name="name[]" id="name" autocomplete="off"><input type="hidden" class="" name="code[]" id="code" value=""></td>\n\
                <td width="10%"><input type="text" class="form-control calculate" name="amountrow[]" id="amount" autocomplete="off"></td>\n\
                <td width="10%"><input type="text" class="form-control inkoop" name="priceInkoop[]" id="priceInkoop" autocomplete="off"></td>\n\
                <td width="10%"><input type="text" class="form-control verkoop" name="price[]" id="price" autocomplete="off"></td>\n\
                <td width="10%"><input type="text" class="form-control marge" name="marge[]" id="marge" autocomplete="off"></td>\n\
                <td width="10%"><input type="text" class="form-control" name="discount[]" id="discount" autocomplete="off" value="<?php echo $newDiscount;?>"></td>\n\
                <td width="10%"><input type="text" class="form-control" name="nacalculatie[]" id="nacalculatie" autocomplete="off"></td>\n\
                <td width="10%"><input type="text" class="form-control" name="extra1[]" id="extra1" autocomplete="off" style="min-width:50px;" data-toggle="tooltip" data-placement="top"></td>\n\
                <td width="75"><a href="#" class="btn btn-sm btn-grey deletecalcrow" style="height:34px;line-height:25px;"><i class="fa fa-trash"></i></a> <a href="#" class="btn btn-sm move ui-sortable-handle" style="height:34px;line-height:25px;"><i class="fa fa-bars"></i></a></td>\n\
                </tr>'
            );
        });

    });


</script>
<div class="modal fade calculationCreator" id="calculationTender" tabindex="-1" role="dialog" aria-labelledby="calculationTender">
    <div class="modal-dialog modal-lg" role="document">
        <form method="post" onkeypress="return event.keyCode != 13;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <input type="submit" class="pull-right normal-text" name="saveAsTemplate" value="<?php $translate->__('save as template', true); ?>">
                    <h4 class="modal-title" id="myModalLabel"><?php $translate->__('create calculation', true); ?></h4>
                </div>
                <div class="modal-body">
                    <form method="post" autocomplete="off" class="calculation" onkeypress="return event.keyCode != 13;">
                        <input type="hidden" value="<?php echo count($rows); ?>" name="tenderrowOrder" id="tenderrowOrder">
                        <div class="row">
                            <span class="col-sm-3 col-label"><?php $translate->__('description', true); ?></span>
                            <div class="col-sm-9"><input name="description" id="description" type="text" value="" class="form-control"></div>
                        </div>
                        <div class="row margin-tb-10">
                            <span class="col-sm-3 col-label"><?php $translate->__('number', true); ?></span>
                            <div class="col-sm-9"><input name="amount" id="amount" type="number" value="1" class="form-control calculationAmount" step="any"></div>
                        </div>
                        <?php if ($showBTW === true) {
                            ?>
                            <div class="row margin-tb-10">
                                <span class="col-sm-3 col-label"><?php $translate->__('VAT', true); ?></span>
                                <div class="col-sm-9"><input name="tenderrowSelected" id="tenderrowSelected" type="number" value="21" class="form-control" step="any"></div>
                            </div>
                            <?php
                        } ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th width=""><?php $translate->__('product', true); ?></th>
                                        <th width="10%"><?php $translate->__('number', true); ?></th>
                                        <th width="10%"><?php $translate->__('purchase', true); ?></th>
                                        <th width="10%"><?php $translate->__('sales', true); ?></th>
                                        <th width="10%"><?php $translate->__('margin', true); ?> (%)</th>
                                        <th width="10%"><?php $translate->__('discount', true); ?> (%)</th>
                                        <th width="10%"><?php $translate->__('nacalc.', true); ?></th>
                                        <th width="10%">&nbsp;</th>
                                        <th width="75">&nbsp;</th>
                                    </tr>
                                    </thead>
                                    <tbody class="rows ui-sortable">
                                    <tr>
                                        <td><input type="text" class="form-control loadjson" name="name[]" id="name" autocomplete="off"><input type="hidden" class="" name="code[]" id="code" value="">
                                        </td>
                                        <td><input type="text" class="form-control calculate" name="amountrow[]" id="amount" autocomplete="off"></td>
                                        <td><input type="text" class="form-control inkoop" name="priceInkoop[]" id="priceInkoop" autocomplete="off"></td>
                                        <td><input type="text" class="form-control verkoop" name="price[]" id="price" autocomplete="off"></td>
                                        <td><input type="text" class="form-control marge" name="marge[]" id="marge" autocomplete="off"></td>
                                        <td><input type="text" class="form-control" name="discount[]" id="discount" autocomplete="off" value="<?php echo $newDiscount; ?>"></td>
                                        <td><input type="text" class="form-control" name="nacalculatie[]" id="nacalculatie" autocomplete="off"></td>
                                        <td><input type="text" class="form-control" name="extra1[]" id="extra1" autocomplete="off" style="min-width:50px;" data-toggle="tooltip" data-placement="top">
                                        </td>
                                        <td><a href="#" class="btn btn-sm btn-grey deletecalcrow" style="height:34px;line-height:25px;"><i class="fa fa-trash"></i></a>
                                            <a href="#" class="btn btn-sm btn-grey move" style="height:34px;line-height:25px;"><i class="fa fa-bars"></i></a></td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    <?php // if ($user->getVar('clientId') == 1 || $user->getVar('clientId') == 1143){?>
                                    <tr>
                                        <!-- Inkoop/Verkoop/Marge -->
                                        <td>Totaal</td>
                                        <td></td>
                                        <td data-total="inkoop">0</td>
                                        <td data-total="verkoop">0</td>
                                        <td data-total="marge">0</td>
                                        <td data-total="korting">0</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <?php // }?>
                                    <tr>
                                        <td colspan="6"><a href="#" class="addcalcrow btn btn-primary"><i class="fa fa-plus"></i> <?php $translate->__('add row'); ?></a></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </form>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php $translate->__('cancel', true); ?></button>
                    <input type="submit" class="btn btn-primary" name="saveCalculationTender" value="<?php $translate->__('save', true); ?>">
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="calculationList" tabindex="-1" role="dialog" aria-labelledby="calculationList">
    <div class="modal-dialog modal-lg" role="document">
        <form method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php $translate->__('add calculation', true); ?></h4>
                </div>
                <div class="modal-body">
                    <?php
                    if (count($calculationsTemplates) >= 1) {
                        foreach ($calculationsTemplates as $calculationsTemplate) {
                            ?>
                            <div class="row border-bottom">
                                <div class="col-sm-11">
                                    <p class="blue"><?php echo htmlsafe($calculationsTemplate['description']); ?></p>
                                </div>
                                <div class="col-sm-1">
                                    <a href="<?= BASE_URL; ?>tenders/view/<?php echo $id; ?>/?addCalculationTemplate=<?php echo htmlsafe($calculationsTemplate['id']); ?>" class="btn btn-green"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                            <?php
                        } ?>
                        <?php
                    } else {
                        ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <p><?php $translate->__('no templates found', true); ?>.</p>
                                <p><?php $translate->__('to create a template, first add a new calculation', true); ?>.</p>
                            </div>
                        </div>
                        <?php
                    } ?>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php $translate->__('cancel', true); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade calculationEditer" id="calculationEditor" tabindex="-1" role="dialog" aria-labelledby="calculationTender">
    <div class="modal-dialog modal-lg" role="document">
        <form method="post" onkeypress="return event.keyCode != 13;">

            <input type="hidden" name="_action" value="edit">
            <input type="hidden" name="_calculationId" value="">

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <input type="submit" class="pull-right normal-text" name="saveAsTemplate" value="<?php $translate->__('save as template', true); ?>">
                    <h4 class="modal-title" id="myModalLabel"><?php $translate->__('edit calculation', true); ?></h4>
                </div>
                <div class="modal-body">
                    <form method="post" autocomplete="off" class="calculation" onkeypress="return event.keyCode != 13;">
                        <input type="hidden" value="<?php echo count($rows); ?>" name="tenderrowOrder" id="tenderrowOrder">
                        <div class="row">
                            <span class="col-sm-3 col-label"><?php $translate->__('description', true); ?></span>
                            <div class="col-sm-9"><input name="description" id="description" type="text" value="" class="form-control"></div>
                        </div>
                        <div class="row margin-tb-10">
                            <span class="col-sm-3 col-label"><?php $translate->__('number', true); ?></span>
                            <div class="col-sm-9"><input name="amount" id="amount" type="number" value="1" class="form-control calculationEditor" step="any"></div>
                        </div>
                        <?php if ($showBTW === true) {
                            ?>
                            <div class="row margin-tb-10">
                                <span class="col-sm-3 col-label"><?php $translate->__('VAT', true); ?></span>
                                <div class="col-sm-9"><input name="tenderrowSelected" id="tenderrowSelected" type="number" value="21" class="form-control" step="any"></div>
                            </div>
                            <?php
                        } ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th width=""><?php $translate->__('product', true); ?></th>
                                        <th width="10%"><?php $translate->__('number', true); ?></th>
                                        <th width="10%"><?php $translate->__('purchase', true); ?></th>
                                        <th width="10%"><?php $translate->__('sales', true); ?></th>
                                        <th width="10%"><?php $translate->__('margin', true); ?> (%)</th>
                                        <th width="10%"><?php $translate->__('discount', true); ?> (%)</th>
                                        <th width="10%"><?php $translate->__('nacalc.', true); ?></th>
                                        <th width="10%">&nbsp;</th>
                                        <th width="75">&nbsp;</th>
                                    </tr>
                                    </thead>
                                    <tbody class="rows ui-sortable">

                                    </tbody>
                                    <tfoot>
                                    <?php // if ($user->getVar('clientId') == 1 || $user->getVar('clientId') == 1143){?>
                                    <tr>
                                        <!-- Inkoop/Verkoop/Marge -->
                                        <td>Totaal</td>
                                        <td></td>
                                        <td data-total="inkoop">0</td>
                                        <td data-total="verkoop">0</td>
                                        <td data-total="marge">0</td>
                                        <td data-total="korting">0</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <?php // }?>
                                    <tr>
                                        <td colspan="6"><a href="#" class="addcalcrow btn btn-primary"><i class="fa fa-plus"></i> <?php $translate->__('add row'); ?></a></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </form>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php $translate->__('cancel', true); ?></button>
                    <input type="submit" class="btn btn-primary" name="saveCalculationTender" value="<?php $translate->__('save', true); ?>">
                </div>
            </div>
        </form>
    </div>
</div>
