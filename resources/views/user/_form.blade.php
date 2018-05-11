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

                    <div class="form-group" :class="formErrors.name ? 'has-error' : ''">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="name">Name
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <input type="text" v-model="formData.name" class="form-control" placeholder="Name" required>
                            <span v-if="formErrors.name" class="help-block">@{{formErrors.name[0]}}</span>
                        </div>
                    </div>

                    <div class="form-group" :class="formErrors.email ? 'has-error' : ''">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="email">Email
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <input type="email" v-model="formData.email" class="form-control" placeholder="Email" required>
                            <span v-if="formErrors.email" class="help-block">@{{formErrors.email[0]}}</span>
                        </div>
                    </div>

                    <div class="form-group" :class="formErrors.password ? 'has-error' : ''">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="password">Password
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <input type="password" v-model="formData.password" class="form-control" placeholder="Password">
                            <span v-if="formErrors.password" class="help-block">@{{formErrors.password[0]}}</span>
                        </div>
                    </div>

                    <div class="form-group" :class="formErrors.password ? 'has-error' : ''">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="password_confirmation">Password Confirmation
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <input type="password" v-model="formData.password_confirmation" class="form-control" placeholder="Confirm Password">
                        </div>
                    </div>

                    <div class="form-group" :class="formErrors.super_admin ? 'has-error' : ''">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="super_admin">Super Admin
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="radio radio-inline radio-replace radio-success">
								<input type="radio" v-model="formData.super_admin" id="super_admin_yes" value="1">
								<label for="super_admin_yes">YES</label>
							</div>
                            <div class="radio radio-inline radio-replace radio-danger">
								<input type="radio" v-model="formData.super_admin" id="super_admin_no" value="0">
								<label for="super_admin_no">NO</label>
							</div>
                            <span v-if="formErrors.super_admin" class="help-block">@{{formErrors.super_admin[0]}}</span>
                        </div>
                    </div>

                    <div class="form-group" :class="formErrors.active ? 'has-error' : ''">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="active">Active
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="radio radio-inline radio-replace radio-success">
								<input type="radio" v-model="formData.active" id="active_yes" value="1">
								<label for="active_yes">YES</label>
							</div>
                            <div class="radio radio-inline radio-replace radio-danger">
								<input type="radio" v-model="formData.active" id="active_no" value="0">
								<label for="active_no">NO</label>
							</div>
                            <span v-if="formErrors.active" class="help-block">@{{formErrors.active[0]}}</span>
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
