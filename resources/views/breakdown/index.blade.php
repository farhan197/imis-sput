@extends('layouts.app')

@section('content')
<div class="panel panel-primary" id="app">
    <div class="panel-body">
        <h3 class="pull-left text-primary">BREAKDOWN <small>Manage</small></h3>
        <span class="pull-right" style="margin:15px 0 15px 10px;">
            @can('create', App\Breakdown::class)
            <a href="#" @click="add" class="btn btn-primary"><i class="icon-plus-circled"></i></a>
            @endcan
            @can('export', App\Breakdown::class)
            <a href="#" @click="openExportForm" class="btn btn-primary"><i class="icon-download"></i> EXPORT</a>
            @endcan
        </span>
        <table class="table table-striped table-hover " id="bootgrid" style="border-top:2px solid #ddd">
            <thead>
                <tr>
                    <th data-column-id="id" data-width="3%">ID</th>
                    <th data-column-id="unit">Unit</th>
                    <th data-column-id="unit_category">Unit Category</th>
                    <th data-column-id="breakdown_category">B/D Type</th>
                    <th data-column-id="breakdown_status">B/D Status</th>
                    <th data-column-id="location">Location</th>
                    <th data-column-id="hm" data-formatter="hm_km">KM/HM</th>
                    <th data-column-id="time_in">Time In</th>
                    <th data-column-id="time_out">Time Out</th>
                    <th data-column-id="duration" data-sortable="false">Duration</th>
                    <th data-column-id="diagnosa">Problem</th>
                    <th data-column-id="component_criteria">Component Criteria</th>
                    <th data-column-id="tindakan">Action</th>
                    <th data-column-id="wo_number">WO Number</th>
                    <!-- <th data-column-id="status" data-formatter="status">Closed</th> -->
                    @can('updateOrDelete', App\Breakdown::class)
                    <th data-column-id="commands"
                        data-formatter="commands"
                        data-sortable="false"
                        data-align="right"
                        data-header-align="right"></th>
                    @endcan
                </tr>
            </thead>
        </table>
    </div>

    @can('createOrUpdate', App\Breakdown::class)
    @include('breakdown._form')
    @endcan

    @can('export', App\Breakdown::class)
    @include('breakdown._form_export')
    @endcan

</div>
@endsection

@push('scripts')
<script type="text/javascript">
$('.page-container').addClass('sidebar-collapsed');
const app = new Vue({
    el: '#app',
    data: {
        formData: {},
        formErrors: {},
        formTitle: '',
        error: {},
        exportRange: {
            from: '{{date("Y-m-d")}}',
            to: '{{date("Y-m-d")}}'
        },
        units: {!! App\Unit::selectRaw('id AS id, name AS text')->orderBy('name', 'ASC')->get() !!},
        component_criterias: {!!App\ComponentCriteria::selectRaw('id AS id, CONCAT(code, " - ", description) AS text')->orderBy('code', 'ASC')->get()!!},
        locations: {!! App\Location::selectRaw('id AS id, name AS text')->orderBy('name', 'ASC')->get() !!},
        breakdownStatuses: {!! \App\BreakdownStatus::selectRaw('id as id, CONCAT(code, " - ", description) AS text')->orderBy('code', 'ASC')->get() !!},
        breakdownCategories: {!! \App\BreakdownCategory::selectRaw('id AS id, CONCAT(name, " - ", description_en) AS text')->orderBy('name', 'ASC')->get() !!}
    },
    methods: {
        openExportForm: function() {
            $('#modal-form-export').modal('show');
        },
        doExport: function() {
            // TODO: validate input first
            $('#modal-form-export').modal('hide');
            window.location = '{{url("breakdown/export")}}?from=' + this.exportRange.from + '&to=' + this.exportRange.to;
        },
        add: function() {
            this.formTitle = "ADD BREAKDOWN";
            this.formData = {};
            this.formData.time_in = '{{date("Y-m-d H:i")}}';
            this.formErrors = {};
            this.error = {};
            $('#modal-form').modal('show');
        },
        store: function() {
            block('form');
            var _this = this;
            axios.post('{{url("breakdown")}}', _this.formData).then(function(r) {
                unblock('form');
                $('#modal-form').modal('hide');
                toastr["success"]("Data berhasil ditambahkan");
                $('#bootgrid').bootgrid('reload');
            })

            .catch(function(error) {
                unblock('form');
                if (error.response.status == 422) {
                    _this.formErrors = error.response.data.errors;
                    _this.error = {};
                }

                if (error.response.status == 500) {
                    _this.error = error.response.data;
                    _this.formErrors = {};
                }
            });
        },
        edit: function(id) {
            var t = this;
            this.formTitle = "EDIT BREAKDOWN";
            this.formErrors = {};
            this.error = {};

            axios.get('{{url("breakdown")}}/' + id).then(function(r) {
                t.formData = r.data;
                $('#modal-form').modal('show');
            })

            .catch(function(error) {
                if (error.response.status == 500) {
                    var error = error.response.data;
                    toastr["error"](error.message + ". " + error.file + ":" + error.line)
                }
            });
        },
        update: function() {
            block('form');
            var t = this;
            axios.put('{{url("breakdown")}}/' + this.formData.id, this.formData).then(function(r) {
                unblock('form');
                $('#modal-form').modal('hide');
                toastr["success"]("Data berhasil diupdate");
                $('#bootgrid').bootgrid('reload');
            })
            // validasi
            .catch(function(error) {
                unblock('form');
                if (error.response.status == 422) {
                    t.formErrors = error.response.data.errors;
                }

                if (error.response.status == 500) {
                    t.error = error.response.data;
                }
            });
        },
        delete: function(id) {
            bootbox.confirm({
                title: "Konfirmasi",
                message: "Anda yakin akan menghapus data ini?",
                callback: function(r) {
                    if (r == true) {
                        axios.delete('{{url("breakdown")}}/' + id)

                        .then(function(r) {
                            if (r.data.success == true) {
                                toastr["success"]("Data berhasil dihapus");
                                $('#bootgrid').bootgrid('reload');
                            } else {
                                toastr["error"]("Data gagal dihapus. " + r.data.message);
                            }
                        })

                        .catch(function(error) {
                            if (error.response.status == 500) {
                                var error = error.response.data;
                                toastr["error"](error.message + ". " + error.file + ":" + error.line)
                            }
                        });
                    }
                }
            });
        },
    },
    mounted: function() {
        var t = this;
        var grid = $('#bootgrid').bootgrid({
            statusMapping: {
                0: "danger",
                1: "success"
            },
            rowCount: [10,25,50,100],
            ajax: true, url: '{{url('breakdown')}}',
            ajaxSettings: {
                method: 'GET', cache: false,
                statusCode: {
                    500: function(e) {
                        var error = JSON.parse(e.responseText);
                        toastr["error"](error.message + ". " + error.file + ":" + error.line)
                    }
                }
            },
            searchSettings: { delay: 100, characters: 3 },
            templates: {
                header: '<div id="@{{ctx.id}}" class="pull-right @{{css.header}}"><div class="actionBar"><p class="@{{css.search}}"></p><p class="@{{css.actions}}"></p></div></div>'
            },
            formatters: {
                "commands": function(column, row) {
                    var btn = '@can("update", App\Breakdown::class) <a href="#" class="btn btn-info btn-xs c-edit" data-id="'+row.id+'"><i class="icon-pencil"></i></a> @endcan';

                    if (row.update_pcr_time == null) {
                        btn += '@can("delete", App\Breakdown::class) <a href="#" class="btn btn-danger btn-xs c-delete" data-id="'+row.id+'"><i class="icon-trash"></i></a> @endcan';
                    }

                    return btn;
                },
                "status": function(column, row) {
                    return row.status
                        ? '<span class="label label-success">Y</span>'
                        : '<span class="label label-danger">N</span>';
                },
                "hm_km": function(column, row) {
                    return row.km + "/" + row.hm;
                },
            }
        }).on("loaded.rs.jquery.bootgrid", function() {
            grid.find(".c-delete").on("click", function(e) {
                t.delete($(this).data("id"));
            });

            grid.find(".c-edit").on("click", function(e) {
                t.edit($(this).data("id"));
            });
        });

    }
});
</script>
@endpush
