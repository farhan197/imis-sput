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

                    <div class="form-group" :class="formErrors.unit_id ? 'has-error' : ''">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="unit_id">Unit
                        </label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <select2 :options="units" v-model="formData.unit_id" data-placeholder="Unit">
                            </select2>
                            <span v-if="formErrors.unit_id" class="help-block">@{{formErrors.unit_id[0]}}</span>
                        </div>
                    </div>

                    <div class="form-group" :class="formErrors.location_id ? 'has-error' : ''">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="location_id">Location
                        </label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <select2 :options="locations" v-model="formData.location_id" data-placeholder="Location">
                            </select2>
                            <span v-if="formErrors.location_id" class="help-block">@{{formErrors.location_id[0]}}</span>
                        </div>
                    </div>

                    <div class="form-group" :class="formErrors.shift ? 'has-error' : ''">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="shift">Shift
                        </label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <div class="radio radio-inline radio-replace radio-success">
								<input type="radio" v-model="formData.shift" id="shift_1" value="1">
								<label for="shift_1">1</label>
							</div>
                            <div class="radio radio-inline radio-replace radio-success">
								<input type="radio" v-model="formData.shift" id="shift_2" value="2">
								<label for="shift_2">2</label>
							</div>
                            <span v-if="formErrors.shift" class="help-block">@{{formErrors.shift[0]}}</span>
                        </div>
                    </div>

                    <div class="form-group" :class="formErrors.time_in ? 'has-error' : ''">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="time_in">Time In
                        </label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <vue-datetimepicker data-date-format="YYYY-MM-DD HH:mm" placeholder="Time In" v-model="formData.time_in">
                            </vue-datetimepicker>
                            <span v-if="formErrors.time_in" class="help-block">@{{formErrors.time_in[0]}}</span>
                        </div>
                    </div>

                    <div v-show="formData.id != undefined" class="form-group" :class="formErrors.time_out ? 'has-error' : ''">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="time_out">Time Out
                        </label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <vue-datetimepicker data-date-format="YYYY-MM-DD HH:mm" placeholder="Time Out" v-model="formData.time_out">
                            </vue-datetimepicker>
                            <span v-if="formErrors.time_out" class="help-block">@{{formErrors.time_out[0]}}</span>
                        </div>
                    </div>

                    <div class="form-group" :class="formErrors.hm ? 'has-error' : ''">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="hm">HM
                        </label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <input type="number" v-model="formData.hm" class="form-control" placeholder="HM">
                            <span v-if="formErrors.hm" class="help-block">@{{formErrors.hm[0]}}</span>
                        </div>
                    </div>

                    <div v-if="formData.id != undefined" class="form-group" :class="formErrors.description ? 'has-error' : ''">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="description">Description
                        </label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <textarea v-model="formData.description" class="form-control" placeholder="Description" rows="3"></textarea>
                            <span v-if="formErrors.description" class="help-block">@{{formErrors.description[0]}}</span>
                        </div>
                    </div>

                    <div v-if="formData.id != undefined" class="form-group" :class="formErrors.status ? 'has-error' : ''">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="status">Close
                        </label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <div class="radio radio-inline radio-replace radio-success">
								<input type="radio" v-model="formData.status" id="yes" value="1">
								<label for="yes">YES</label>
							</div>
                            <div class="radio radio-inline radio-replace radio-danger">
								<input type="radio" v-model="formData.status" id="no" value="0">
								<label for="no">NO</label>
							</div>
                            <span v-if="formErrors.status" class="help-block">@{{formErrors.status[0]}}</span>
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
