@extends('layouts.app')

@section('content')

<div class="panel panel-primary" id="app">
    <div class="panel-body">
        <h3 class="pull-left text-primary">AUTHORIZATION <small>Manage</small></h3>
        @can('create', App\Authorization::class)
        <span class="pull-right" style="margin:15px 0 15px 10px;">
            <a href="#" @click="add" class="btn btn-primary"><i class="icon-plus-circled"></i></a>
        </span>
        @endcan
        <table class="table table-striped table-hover " id="bootgrid" style="border-top:2px solid #ddd">
            <thead>
                <tr>
                    <th data-column-id="id">ID</th>
                    <th data-column-id="user">User</th>
                    <th data-column-id="controller">Controller</th>
                    <th data-column-id="view" data-formatter="view">View</th>
                    <th data-column-id="create" data-formatter="create">Create</th>
                    <th data-column-id="update" data-formatter="update">Update</th>
                    <th data-column-id="delete" data-formatter="delete">Delete</th>
                    <th data-column-id="export" data-formatter="export">Export</th>
                    <th data-column-id="import" data-formatter="import">Import</th>
                    @can('updateOrDelete', App\Authorization::class)
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

    @can('createOrUpdate', App\Authorization::class)
    @include('authorization._form')
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
            modules: {!! json_encode(App\Authorization::getModule()) !!},
            users: {!! App\User::where('super_admin', 0)->orderBy('name', 'ASC')->selectRaw('name AS text, id AS id')->get() !!},
        },
        methods: {
            add: function() {
                this.formTitle = "ADD AUTHORIZATION";
                this.formData = {
                    view: 1, create: 1, update: 1, delete: 1,
                    export: 1, import: 1, dashboard: 1
                };
                this.formErrors = {};
                this.error = {};
                $('#modal-form').modal('show');
            },
            store: function() {
                block('form');
                var t = this;

                axios.post('{{url("authorization")}}', this.formData).then(function(r) {
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
                this.formTitle = "EDIT AUTHORIZATION";
                this.formErrors = {};
                this.error = {};

                axios.get('{{url("authorization")}}/' + id).then(function(r) {
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
                axios.put('{{url("authorization")}}/' + this.formData.id, this.formData).then(function(r) {
                    unblock('form');
                    $('#modal-form').modal('hide');
                    toastr["success"]("Data berhasil diupdate");
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
            delete: function(id) {
                bootbox.confirm({
                    title: "Konfirmasi",
                    message: "Anda yakin akan menghapus data ini?",
                    callback: function(r) {
                        if (r == true) {
                            axios.delete('{{url("authorization")}}/' + id)

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
                ajax: true, url: '{{url('authorization')}}',
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
                        return '@can("update", App\Authorization::class) <a href="#" class="btn btn-info btn-xs c-edit" data-id="'+row.id+'"><i class="icon-pencil"></i></a> @endcan' +
                            '@can("delete", App\Authorization::class) <a href="#" class="btn btn-danger btn-xs c-delete" data-id="'+row.id+'"><i class="icon-trash"></i></a> @endcan';
                    },
                    "view": function(column, row) {
                        return row.view
                            ? '<span class="label label-success">Y</span>'
                            : '<span class="label label-danger">N</span>';
                    },
                    "create": function(column, row) {
                        return row.create
                            ? '<span class="label label-success">Y</span>'
                            : '<span class="label label-danger">N</span>';
                    },
                    "update": function(column, row) {
                        return row.update
                            ? '<span class="label label-success">Y</span>'
                            : '<span class="label label-danger">N</span>';
                    },
                    "delete": function(column, row) {
                        return row.delete
                            ? '<span class="label label-success">Y</span>'
                            : '<span class="label label-danger">N</span>';
                    },
                    "export": function(column, row) {
                        return row.export
                            ? '<span class="label label-success">Y</span>'
                            : '<span class="label label-danger">N</span>';
                    },
                    "import": function(column, row) {
                        return row.import
                            ? '<span class="label label-success">Y</span>'
                            : '<span class="label label-danger">N</span>';
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
