@extends('layouts.app')

@section('content')
<div class="panel panel-primary" id="app">
    <div class="panel-body">
        <h3 class="pull-left text-primary">EMPLOYEE <small>Manage</small></h3>
        <span class="pull-right" style="margin:15px 0 15px 10px;">
            @can('create', App\Employee::class)
            <a href="#" @click="add" class="btn btn-primary"><i class="icon-plus-circled"></i></a>
            @endcan
            @can('export', App\Employee::class)
            <a href="{{url('employee/export')}}" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> EXPORT</a>
            @endcan
            @can('export', App\Employee::class)
            <a href="{{url('employee/generateNameTag')}}" class="btn btn-primary" target="_blank"><i class="icon-credit-card"></i> Generate Name Tag</a>
            @endcan
        </span>
        <table class="table table-striped table-hover " id="bootgrid" style="border-top:2px solid #ddd">
            <thead>
                <tr>
                    <th data-column-id="id" data-width="3%">ID</th>
                    <th data-column-id="name">Name</th>
                    <th data-column-id="nrp">NRP</th>
                    <th data-column-id="department">Department</th>
                    <th data-column-id="position">Position</th>
                    <th data-column-id="employer">Employer</th>
                    <th data-column-id="office">Office</th>
                    <th data-column-id="dormitory">Dormitory</th>
                    <th data-column-id="room">Room</th>
                    <!-- <th data-column-id="status">Status</th> -->
                    @can('updateOrDelete', App\Employee::class)
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

    @can('createOrUpdate', App\Employee::class)
    @include('employee._form')
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
        positions: {!!App\Position::selectRaw('id AS id, name AS text')->orderBy('name', 'ASC')->get()!!},
        employers: {!!App\Owner::selectRaw('id AS id, name AS text')->orderBy('name', 'ASC')->get()!!},
        offices: {!!App\Office::selectRaw('id AS id, name AS text')->orderBy('name', 'ASC')->get()!!},
        departments: {!!App\Department::selectRaw('id AS id, name AS text')->orderBy('name', 'ASC')->get()!!},
    },
    methods: {
        add: function() {
            // reset the form
            this.formTitle = "ADD EMPLOYEE";
            this.formData = {};
            this.formErrors = {};
            this.error = {};
            // open form
            $('#modal-form').modal('show');
        },
        store: function() {
            block('form');
            var t = this;
            axios.post('{{url("employee")}}', this.formData).then(function(r) {
                unblock('form');
                $('#modal-form').modal('hide');
                toastr["success"]("Data berhasil ditambahkan");
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
        edit: function(id) {
            var t = this;
            this.formTitle = "EDIT EMPLOYEE";
            this.formErrors = {};
            this.error = {};

            axios.get('{{url("employee")}}/' + id).then(function(r) {
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
            axios.put('{{url("employee")}}/' + this.formData.id, this.formData).then(function(r) {
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
                        axios.delete('{{url("employee")}}/' + id)

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
        generateNameTag: function(id) {
            window.open('{{url("employee/generateNameTag")}}/' + id, '_blank');
        }
    },
    mounted: function() {

        var t = this;

        var grid = $('#bootgrid').bootgrid({
            statusMapping: {
                0: "danger",
                1: "default"
            },
            rowCount: [10,25,50,100],
            ajax: true, url: '{{url('employee')}}',
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
                    return '@can("update", App\Employee::class) <a href="#" class="btn btn-info btn-xs c-edit" data-id="'+row.id+'"><i class="icon-pencil"></i></a> @endcan' +
                        '@can("delete", App\Employee::class) <a href="#" class="btn btn-danger btn-xs c-delete" data-id="'+row.id+'"><i class="icon-trash"></i></a> @endcan' + '@can("export", App\Employee::class) <a href="#" class="btn btn-success btn-xs c-name-tag" data-id="'+row.id+'"><i class="icon-credit-card"></i></a> @endcan';
                },
            }
        }).on("loaded.rs.jquery.bootgrid", function() {
            grid.find(".c-delete").on("click", function(e) {
                t.delete($(this).data("id"));
            });

            grid.find(".c-edit").on("click", function(e) {
                t.edit($(this).data("id"));
            });

            grid.find(".c-name-tag").on("click", function(e) {
                t.generateNameTag($(this).data("id"));
            });
        });

    }
});
</script>
@endpush
