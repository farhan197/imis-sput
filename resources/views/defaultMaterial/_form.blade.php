<div id="modal-form" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal" role="form" @submit.prevent="formData.id == undefined ? store : update">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@{{formTitle}}</h4>
                </div>
                <div class="modal-body">

                    <div class="alert alert-danger" v-if="error.message">
                        @{{error.message}}<br>
                        @{{error.file}}:@{{error.line}}
                    </div>

                    <div class="form-group" :class="formErrors.customer_id ? 'has-error' : ''">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="customer_id">Customer
                        </label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <select2 :options="customers" v-model="formData.customer_id" data-placeholder="Customer">
                            </select2>
                            <span v-if="formErrors.customer_id" class="help-block">@{{formErrors.customer_id[0]}}</span>
                        </div>
                    </div>

                    <div class="form-group" :class="formErrors.contractor_id ? 'has-error' : ''">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="contractor_id">Contractor
                        </label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <select2 :options="contractors" v-model="formData.contractor_id" data-placeholder="Contractor">
                            </select2>
                            <span v-if="formErrors.contractor_id" class="help-block">@{{formErrors.contractor_id[0]}}</span>
                        </div>
                    </div>

                    <div class="form-group" :class="formErrors.material_type ? 'has-error' : ''">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="material_type">Material Type
                        </label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <div class="radio radio-inline radio-replace radio-success">
								<input type="radio" v-model="formData.material_type" id="material_type_hi" value="h">
								<label for="material_type_hi">HIGH</label>
							</div>
                            <div class="radio radio-inline radio-replace radio-danger">
								<input type="radio" v-model="formData.material_type" id="material_type_lo" value="l">
								<label for="material_type_lo">LOW</label>
							</div>
                            <span v-if="formErrors.material_type" class="help-block">@{{formErrors.material_type[0]}}</span>
                        </div>
                    </div>

                    <div class="form-group" :class="formErrors.seam_id ? 'has-error' : ''">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="seam_id">Seam
                        </label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <select2 :options="seams" v-model="formData.seam_id" data-placeholder="Seam">
                            </select2>
                            <span v-if="formErrors.seam_id" class="help-block">@{{formErrors.seam_id[0]}}</span>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" v-if="formData.id == undefined" class="btn btn-primary" @click="store"><i class="fa fa-floppy-o"></i> Save</button>
                    <button type="submit" v-if="formData.id != undefined" class="btn btn-primary" @click="update"><i class="fa fa-floppy-o"></i> Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
