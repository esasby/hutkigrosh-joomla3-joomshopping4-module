<div id="hutkigrosh">
    <div id="completion_text">
        <?php use esas\hutkigrosh\lang\TranslatorJoom;

        echo $completion_text ?>
    </div>
    <div id="hutkigrosh_buttons" class="">
        <?php if ($configurationWrapper->isWebpayButtonEnabled()) { ?>
            <?php if ($webpay_status and $webpay_status == 'payed') { ?>
            <div class="alert alert-info"
                 id="hutkigrosh_message"><?= TranslatorJoom::translate('hutkigrosh_webpay_msg_payed') ?></div>
        <?php } elseif ($webpay_status and $webpay_status == 'failed') { ?>
            <div class="alert alert-error"
                 id="hutkigrosh_message"><?= TranslatorJoom::translate('hutkigrosh_webpay_msg_failed') ?></div>
        <?php } ?>
            <div id="webpay">
                <?php echo $webpay_form ?>
            </div>
            <script type="text/javascript" src="http://ajax.microsoft.com/ajax/jQuery/jquery-1.11.0.min.js"></script>
            <script>
                var webpay_form_button = $('#webpay input[type="submit"]');
                webpay_form_button.addClass('btn btn-success');
            </script>
        <?php } ?>
        <?php if ($configurationWrapper->isAlfaclickButtonEnabled()) { ?>
            <div id="alfaclick">
                <input type="hidden" value="<?= $alfaclick_billID ?>" id="billID"/>
                <input type="tel" maxlength="20" value="<?= $alfaclick_phone ?>" id="phone"/>
                <a class="btn btn-success"
                   id="alfaclick_button"><?= TranslatorJoom::translate('hutkigrosh_alfaclick_label') ?></a>
            </div>
            <script type="text/javascript" src="http://ajax.microsoft.com/ajax/jQuery/jquery-1.11.0.min.js"></script>
            <script>
                var webpay_form_button = $('#webpay input[type="submit"]');
                webpay_form_button.addClass('btn btn-success');
                jQuery(document).ready(function ($) {
                    $('#alfaclick_button').click(function () {
                        jQuery.post('<?= $alfaclick_url ?>',
                            {
                                phone: $('#phone').val(),
                                billid: $('#billID').val()
                            }
                        ).done(function (result) {
                            if (result.trim() == 'ok') {
                                $('#hutkigrosh_message').remove();
                                $('#hutkigrosh_buttons').before('<div class="alert alert-info" id="hutkigrosh_message"><?= TranslatorJoom::translate("hutkigrosh_alfaclick_msg_success") ?></div>');
                            } else {
                                $('#hutkigrosh_message').remove();
                                $('#hutkigrosh_buttons').before('<div class="alert alert-error" id="hutkigrosh_message"><?= TranslatorJoom::translate("hutkigrosh_alfaclick_msg_unsuccess") ?></div>');
                            }
                        })
                    })
                });
            </script>
        <?php } ?>
    </div>
</div>
