<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-2 sm:tw-mb-4">
                    <div class="tw-items-start">
                        <a href="<?php echo admin_url('invoices#' . $invoice->id); ?>" class="btn btn-primary">
                            <?php echo format_invoice_number($invoice->id); ?>
                        </a>
                        <?php if (empty($invoice->fel_numero)) { ?>
                            <a href="<?php echo admin_url('fel/stamp/' . $invoice->id); ?>" class="btn btn-success">
                                Certificar
                            </a>
                        <?php } ?>
                    </div>
                    <div>
                        <a href="#fel_cancelation_modal" data-toggle="modal" class="btn btn-danger">
                            Anular
                        </a>
                    </div>
                </div>
                <div class="alert alert-info">
                    <?php if (isset($stamp_message)) {
                        echo '<p>' . $stamp_message . '</p>';
                    } if (isset($stamp_message_detail)) {
                        echo '<p>' . $stamp_message_detail . '</p>';
                    } ?>
                    <!-- <pre lang="xml" class="mt-5">
                        <?php
                            /*if (isset($invoice_xml)) {
                                echo htmlentities($invoice_xml);
                            } else {
                                echo print_r($invoice);
                            }*/
                        ?>
                    </pre> -->
                </div>
                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php $this->load->view('template/felinvoiceview.php'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('modal/fel_invoice_cancel.php'); ?>
<?php init_tail(); ?>
</body>

</html>
