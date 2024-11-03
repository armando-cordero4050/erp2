<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="mtop15 preview-top-wrapper">
    <div class="row">
        <div class="col-sm-3">h
            <div class="mbot30">
                <div class="invoice-html-logo">
                    <?php echo get_dark_company_logo(); ?>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="top" data-sticky data-sticky-class="preview-sticky-header">
        <div class="container preview-sticky-container">
            <div class="sm:tw-flex tw-justify-between -tw-mx-4">
                <div class="sm:tw-self-end">
                    <h3 class="bold tw-my-0 invoice-html-number">
                        <span class="sticky-visible hide tw-mb-2">
                            <?php echo format_invoice_number($invoice->id); ?>
                        </span>
                    </h3>
                    <span class="invoice-html-status">
                        <?php echo format_invoice_status($invoice->status, '', true); ?>
                    </span>
                </div>
                <div class="tw-flex tw-items-end tw-space-x-2 tw-mt-3 sm:tw-mt-0">
                    <?php if (is_client_logged_in() && has_contact_permission('invoices')) { ?>
                        <a href="<?php echo site_url('clients/invoices/'); ?>"
                           class="btn btn-default action-button go-to-portal">
                            <?php echo _l('client_go_to_dashboard'); ?>
                        </a>
                    <?php } ?>
                    <?php echo form_open($this->uri->uri_string()); ?>
                    <button type="submit" name="invoicepdf" value="invoicepdf" class="btn btn-default action-button">
                        <i class='fa-regular fa-file-pdf'></i>
                        <?php echo _l('clients_invoice_html_btn_download'); ?>
                    </button>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<div class="panel_s tw-mt-6">
    <div class="panel-body">
        <?php if (is_invoice_overdue($invoice)) { ?>
            <div class="col-md-10 col-md-offset-1 tw-mb-5">
                <div class="alert alert-danger text-center">
                    <p class="tw-font-medium">
                        <?php echo _l('overdue_by_days', get_total_days_overdue($invoice->duedate)) ?>
                    </p>
                </div>
            </div>
        <?php } ?>
        <div class="col-md-10 col-md-offset-1">
            <div class="row mtop20">
                <div class="col-md-6 col-sm-6 transaction-html-info-col-left">
                    <h4 class="tw-font-semibold tw-text-neutral-700 invoice-html-number">
                        <?php echo format_invoice_number($invoice->id); ?>
                    </h4>
                    <address class="invoice-html-company-info tw-text-neutral-500 tw-text-normal">
                        <?php echo format_organization_info(); ?>
                    </address>
                    <?php hooks()->do_action('after_left_panel_invoicehtml', $invoice); ?>
                </div>
                <div class="col-sm-6 text-right transaction-html-info-col-right">
                    <span class="tw-font-medium tw-text-neutral-700 invoice-html-bill-to">
                        <?php echo _l('invoice_bill_to'); ?>
                    </span>
                    <address class="invoice-html-customer-billing-info tw-text-neutral-500 tw-text-normal">
                        <?php echo format_customer_info($invoice, 'invoice', 'billing'); ?>
                    </address>
                    <!-- shipping details -->
                    <?php if ($invoice->include_shipping == 1 && $invoice->show_shipping_on_invoice == 1) { ?>
                        <span class="tw-font-medium tw-text-neutral-700 invoice-html-ship-to">
                        <?php echo _l('ship_to'); ?>
                    </span>
                        <address class="invoice-html-customer-shipping-info tw-text-neutral-500 tw-text-normal">
                            <?php echo format_customer_info($invoice, 'invoice', 'shipping'); ?>
                        </address>
                    <?php } ?>
                    <p class="invoice-html-date tw-mb-0 tw-text-normal">
                        <span class="tw-font-medium tw-text-neutral-700">
                            <?php echo _l('invoice_data_date'); ?>
                        </span>
                        <?php echo _d($invoice->date); ?>
                    </p>
                    <?php if (!empty($invoice->duedate)) { ?>
                        <p class="invoice-html-duedate tw-mb-0 tw-text-normal">
                        <span class="tw-font-medium tw-text-neutral-700">
                            <?php echo _l('invoice_data_duedate'); ?>
                        </span>
                            <?php echo _d($invoice->duedate); ?>
                        </p>
                    <?php } ?>
                    <?php if ($invoice->sale_agent != 0 && get_option('show_sale_agent_on_invoices') == 1) { ?>
                        <p class="invoice-html-sale-agent tw-mb-0 tw-text-normal">
                            <span class="tw-font-medium tw-text-neutral-700"><?php echo _l('sale_agent_string'); ?>:</span>
                            <?php echo get_staff_full_name($invoice->sale_agent); ?>
                        </p>
                    <?php } ?>
                    <?php if ($invoice->project_id != 0 && get_option('show_project_on_invoice') == 1) { ?>
                        <p class="invoice-html-project tw-mb-0 tw-text-normal">
                            <span class="tw-font-medium tw-text-neutral-700"><?php echo _l('project'); ?>:</span>
                            <?php echo get_project_name_by_id($invoice->project_id); ?>
                        </p>
                    <?php } ?>
                    <?php $pdf_custom_fields = get_custom_fields('invoice', ['show_on_pdf' => 1, 'show_on_client_portal' => 1]);
                    foreach ($pdf_custom_fields as $field) {
                        $value = get_custom_field_value($invoice->id, $field['id'], 'invoice');
                        if ($value == '') {
                            continue;
                        } ?>
                        <p class="tw-mb-0 tw-text-normal">
                            <span class="tw-font-medium tw-text-neutral-700"><?php echo $field['name']; ?>: </span>
                            <?php echo $value; ?>
                        </p>
                        <?php
                    } ?>
                    <?php hooks()->do_action('after_right_panel_invoicehtml', $invoice); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <?php
                        $items = get_items_table_data($invoice, 'invoice');
                        echo $items->table();
                        ?>
                    </div>
                </div>
                <div class="col-md-6 col-md-offset-6">
                    <table class="table text-right tw-text-normal">
                        <tbody>
                        <tr id="subtotal">
                            <td>
                                <span class="bold tw-text-neutral-700"><?php echo _l('invoice_subtotal'); ?></span>
                            </td>
                            <td class="subtotal">
                                <?php echo app_format_money($invoice->subtotal, $invoice->currency_name); ?>
                            </td>
                        </tr>
                        <?php if (is_sale_discount_applied($invoice)) { ?>
                            <tr>
                                <td>
                                    <span class="bold tw-text-neutral-700"><?php echo _l('invoice_discount'); ?>
                                        <?php if (is_sale_discount($invoice, 'percent')) { ?>
                                            (<?php echo app_format_number($invoice->discount_percent, true); ?>%)
                                        <?php } ?></span>
                                </td>
                                <td class="discount">
                                    <?php echo '-' . app_format_money($invoice->discount_total, $invoice->currency_name); ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php
                        foreach ($items->taxes() as $tax) {
                            echo '<tr class="tax-area"><td class="bold !tw-text-neutral-700">' . $tax['taxname'] . ' (' . app_format_number($tax['taxrate']) . '%)</td><td>' . app_format_money($tax['total_tax'], $invoice->currency_name) . '</td></tr>';
                        }
                        ?>
                        <?php if ((int)$invoice->adjustment != 0) { ?>
                            <tr>
                                <td>
                                    <span class="bold tw-text-neutral-700">
                                        <?php echo _l('invoice_adjustment'); ?>
                                    </span>
                                </td>
                                <td class="adjustment">
                                    <?php echo app_format_money($invoice->adjustment, $invoice->currency_name); ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td>
                                <span class="bold tw-text-neutral-700"><?php echo _l('invoice_total'); ?></span>
                            </td>
                            <td class="total">
                                <?php echo app_format_money($invoice->total, $invoice->currency_name); ?>
                            </td>
                        </tr>
                        <?php if (count($invoice->payments) > 0 && get_option('show_total_paid_on_invoice') == 1) { ?>
                            <tr>
                                <td>
                                    <span class="bold tw-text-neutral-700">
                                        <?php echo _l('invoice_total_paid'); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo '-' . app_format_money(sum_from_table(db_prefix() . 'invoicepaymentrecords', ['field' => 'amount', 'where' => ['invoiceid' => $invoice->id]]), $invoice->currency_name); ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (get_option('show_credits_applied_on_invoice') == 1 && $credits_applied = total_credits_applied_to_invoice($invoice->id)) { ?>
                            <tr>
                                <td>
                                    <span class="bold tw-text-neutral-700"><?php echo _l('applied_credits'); ?></span>
                                </td>
                                <td>
                                    <?php echo '-' . app_format_money($credits_applied, $invoice->currency_name); ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (get_option('show_amount_due_on_invoice') == 1 && $invoice->status != Invoices_model::STATUS_CANCELLED) { ?>
                            <tr>
                                <td>
                                    <span
                                            class="<?php echo $invoice->total_left_to_pay > 0 ? 'text-danger ' : ''; ?> bold">
                                        <?php echo _l('invoice_amount_due'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="<?php echo $invoice->total_left_to_pay > 0 ? 'text-danger' : ''; ?>">
                                        <?php echo app_format_money($invoice->total_left_to_pay, $invoice->currency_name); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php if (get_option('total_to_words_enabled') == 1) { ?>
                    <div class="col-md-12 text-center invoice-html-total-to-words">
                        <p class="tw-font-medium">
                            <?php echo _l('num_word'); ?>:<span class="tw-text-neutral-500">
                            <?php echo $this->numberword->convert($invoice->total, $invoice->currency_name); ?>
                        </span>
                        </p>
                    </div>
                <?php } ?>
                <?php if (count($invoice->attachments) > 0 && $invoice->visible_attachments_to_customer_found == true) { ?>
                    <div class="clearfix"></div>
                    <div class="invoice-html-files">
                        <div class="col-md-12">
                            <hr/>
                            <p><b><?php echo _l('invoice_files'); ?></b></p>
                        </div>
                        <?php foreach ($invoice->attachments as $attachment) {
                            // Do not show hidden attachments to customer
                            if ($attachment['visible_to_customer'] == 0) {
                                continue;
                            }
                            $attachment_url = site_url('download/file/sales_attachment/' . $attachment['attachment_key']);
                            if (!empty($attachment['external'])) {
                                $attachment_url = $attachment['external_link'];
                            } ?>
                            <div class="col-md-12 mbot10">
                                <div class="pull-left">
                                    <i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i>
                                </div>
                                <a href="<?php echo $attachment_url; ?>"><?php echo $attachment['file_name']; ?></a>
                            </div>
                            <?php
                        } ?>
                    </div>
                <?php } ?>
                <?php if (!empty($invoice->clientnote)) { ?>
                    <div class="col-md-12 invoice-html-note">
                        <p>
                            <b><?php echo _l('invoice_note'); ?></b>
                        </p>
                        <div class="tw-text-neutral-500 tw-mt-2.5">
                            <?php echo $invoice->clientnote; ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if (!empty($invoice->terms)) { ?>
                    <div class="col-md-12 invoice-html-terms-and-conditions">
                        <hr/>
                        <p>
                            <b>
                                <?php echo _l('terms_and_conditions'); ?>
                            </b>
                        </p>
                        <div class="tw-text-neutral-500 tw-mt-2.5">
                            <?php echo $invoice->terms; ?>
                        </div>
                    </div>
                <?php } ?>
                <div class="col-md-12">
                    <hr/>
                </div>
            </div>
        </div>
    </div>
</div>

