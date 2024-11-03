<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="fel_cancelation_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title">Anular Factura Certificada</span>
                </h4>
            </div>
            <?php echo form_open('admin/fel/cancel', ['id' => 'cancelation_form']); ?>
            <div class="modal-body">
                <div class="row">
                    <?php echo form_hidden('id',  $invoice->id); ?>
                    <div class="col-md-12">
                        <?php echo render_textarea('motivo', 'Motivo de la cancelacion', ''); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Anular Factura</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
