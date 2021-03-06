@extends('layouts.app')

@section('content')

<div class="panel panel-primary" id="app">
    <div class="panel-body">
        <h3 class="pull-left text-primary">ABSENSI <small>Manage</small></h3>
        <span class="pull-right" style="margin:15px 0 15px 10px;">
            @can('create', App\Absensi::class)
            <a href="#" @click="add" class="btn btn-primary"><i class="icon-plus-circled"></i></a>
            @endcan
            @can('export', App\Absensi::class)
            <a href="#" @click="openExportForm" class="btn btn-primary"><i class="icon-download"></i> EXPORT</a>
            @endcan
        </span>
        <table class="table table-striped table-hover " id="bootgrid" style="border-top:2px solid #ddd">
            <thead>
                <tr>
                    <th data-column-id="id" data-width="3%">ID</th>
                    <th data-column-id="date">Date</th>
                    <th data-column-id="nrp">NRP</th>
                    <th data-column-id="name">Name</th>
                    <th data-column-id="department">Department</th>
                    <th data-column-id="position">Position</th>

                    <th data-column-id="shift"
                        data-sortable="false"
                        data-align="center"
                        data-header-align="center">Shift</th>

                    <th data-column-id="in"
                        data-align="center"
                        data-header-align="center">In</th>

                    <th data-column-id="out"
                        data-align="center"
                        data-header-align="center">Out</th>

                    @can('updateOrDelete', App\Absensi::class)
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

    @can('createOrUpdate', App\Absensi::class)
    @include('absensi._form')
    @endcan

    @can('export', App\Absensi::class)
    @include('absensi._form_export')
    @endcan

</div>

@endsection

@push('scripts')

<script type="text/javascript">

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
            employees: {!! App\Employee::selectRaw('id AS id, CONCAT(nrp, " - ", name) AS text')->orderBy('name', 'ASC')->get() !!},
        },
        methods: {
            openExportForm: function() {
                $('#modal-form-export').modal('show');
            },
            doExport: function() {
                // TODO: validate input first
                $('#modal-form-export').modal('hide');
                window.location = '{{url("absensi/export")}}?from=' + this.exportRange.from + '&to=' + this.exportRange.to;
            },
            add: function() {
                // reset the form
                this.formTitle = "ADD ABSENSI";
                this.formData = {
                    date: moment().format('YYYY-MM-DD'),
                    shift: moment().format('H') >= 6 && moment().format('H') <= 18 ? 1 : 2
                };
                this.formErrors = {};
                this.error = {};
                // open form
                $('#modal-form').modal('show');
            },
            store: function() {
                block('form');
                var t = this;

                axios.post('{{url("absensi")}}', this.formData).then(function(r) {
                    unblock('form');
                    $('#modal-form').modal('hide');
                    toastr["success"]("Data berhasil ditambahkan");
                    $('#bootgrid').bootgrid('reload');
                })

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
            edit: function(id) {
                var t = this;
                this.formTitle = "EDIT ABSENSI";
                this.formErrors = {};
                this.error = {};

                axios.get('{{url("absensi")}}/' + id).then(function(r) {
                    t.formData = r.data;
                    $('#modal-form').modal('show');
                })

                .catch(function(error) {
                    var error = error.response.data;
                    toastr["error"](error.message + ". " + error.file + ":" + error.line);
                });
            },
            update: function() {
                block('form');
                var t = this;
                axios.put('{{url("absensi")}}/' + this.formData.id, this.formData).then(function(r) {
                    unblock('form');
                    $('#modal-form').modal('hide');
                    toastr["success"]("Data berhasil diupdate");
                    $('#bootgrid').bootgrid('reload');
                })
                .catch(function(error) {
                    unblock('form');
                    if (error.response.status == 422) {
                        t.formErrors = error.response.data.errors;
                        t.error = {};
                    }

                    if (error.response.status == 500) {
                        t.error = error.response.data;
                        t.formErrors = {};
                    }
                });
            },
            delete: function(id) {
                bootbox.confirm({
                    title: "Konfirmasi",
                    message: "Anda yakin akan menghapus data ini?",
                    callback: function(r) {
                        if (r == true) {
                            axios.delete('{{url("absensi")}}/' + id)

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
                rowCount: [10,25,50,100],
                ajax: true, url: '{{url('absensi')}}',
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
                        return '@can("update", App\Absensi::class) <a href="#" class="btn btn-info btn-xs c-edit" data-id="'+row.id+'"><i class="icon-pencil"></i></a> @endcan' +
                            '@can("delete", App\Absensi::class) <a href="#" class="btn btn-danger btn-xs c-delete" data-id="'+row.id+'"><i class="icon-trash"></i></a> @endcan';
                    },
                }
            }).on("loaded.rs.jquery.bootgrid", function(e) {
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
