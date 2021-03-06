<div id="modal-form" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width:750px;">
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
                            <input type="text" v-model="formData.name" class="form-control" placeholder="Name">
                            <span v-if="formErrors.name" class="help-block">@{{formErrors.name[0]}}</span>
                        </div>
                    </div>

                    <div class="form-group" :class="formErrors.capacity ? 'has-error' : ''">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="capacity">Capacity (Ton)
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <input type="number" v-model="formData.capacity" class="form-control" placeholder="Capacity">
                            <span v-if="formErrors.capacity" class="help-block">@{{formErrors.capacity[0]}}</span>
                        </div>
                    </div>

                    <div class="form-group" :class="formErrors.group ? 'has-error' : ''">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="group">Group
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <input type="text" v-model="formData.group" class="form-control" placeholder="Group">
                            <span v-if="formErrors.group" class="help-block">@{{formErrors.group[0]}}</span>
                        </div>
                    </div>

                    <div class="form-group" :class="formErrors.description ? 'has-error' : ''">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="description">Description
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <input type="text" v-model="formData.description" class="form-control" placeholder="Description">
                            <span v-if="formErrors.description" class="help-block">@{{formErrors.description[0]}}</span>
                        </div>
                    </div>

                    <table class="table table-striped table-hover" style="margin-bottom:0;border-top:2px solid #ddd;">
                        <thead>
                            <tr>
                                <th>Stock Area</th>
                                <th>Capacity (Ton)</th>
                                <th>Jetty</th>
                                <th>Hopper</th>
                                <th>Position</th>
                                <th>Order</th>
                                <th class="text-right">
                                    <a href="#" @click="addStockArea" class="btn btn-primary"><i class="icon-plus"></i></a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(s,i) in formData.stock_area">
                                <td>
                                    <input type="hidden" v-model="formData.stock_area[i].id">
                                    <input type="text" class="form-control" v-model="formData.stock_area[i].name">
                                </td>
                                <td>
                                    <input type="number" class="form-control" v-model="formData.stock_area[i].capacity">
                                </td>
                                <td>
                                    <select2 :options="jetties" v-model="formData.stock_area[i].jetty_id" data-placeholder="Jetty">
                                    </select2>
                                </td>
                                <td>
                                    <select2 :options="hoppers" v-model="formData.stock_area[i].hopper_id" data-placeholder="Hopper">
                                    </select2>
                                </td>
                                <td>
                                    <select class="form-control" v-model="formData.stock_area[i].position" name="">
                                        <option value="l">Left</option>
                                        <option value="r">Right</option>
                                        <option value="c">Center</option>
                                        <option value="o">Outside</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control" v-model="formData.stock_area[i].order">
                                </td>
                                <td class="text-right">
                                    <a href="#" @click="delStockArea(i)" class="btn btn-danger"><i class="icon-trash"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>

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
