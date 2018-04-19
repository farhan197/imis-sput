@extends('layouts.app')

@section('content')

<div class="panel panel-primary" id="app">
    <div class="panel-body">
        <h3 class="pull-left text-primary text-primary">BREAKDOWN CAREGORIES <small>Manage</small></h3>
        <span class="pull-right" style="margin:15px 0 15px 10px;">
            <a href="#" @click="add" class="btn btn-primary"><i class="icon-plus-circled"></i></a>
        </span>
        <table class="table table-striped table-hover " id="bootgrid" style="border-top:2px solid #ddd">
            <thead>
                <tr>
                    <th data-column-id="id" data-width="3%">ID</th>
                    <th data-column-id="name">Code</th>
                    <th data-column-id="description_id" data-title="description_id">Description ID</th>
                    <th data-column-id="description_en" data-title="description_en">Description EN</th>
                    <th data-column-id="commands" data-width="5%"
                        data-formatter="commands"
                        data-sortable="false"
                        data-align="right"
                        data-header-align="right"></th>
                </tr>
            </thead>
        </table>
    </div>

    @include('breakdownCategory._form')

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
            error: {}
        },
        methods: {
            add: function() {
                // reset the form
                this.formTitle = "ADD BREAKDOWN CATEGORY";
                this.formData = {};
                this.formErrors = {};
                this.error = {};
                // open form
                $('#modal-form').modal('show');
            },
            store: function() {
                var t = this;
                axios.post('{{url("breakdownCategory")}}', this.formData).then(function(r) {
                    $('#modal-form').modal('hide');
                    $('#bootgrid').bootgrid('reload');
                })
                // validasi
                .catch(function(error) {
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
                this.formTitle = "EDIT BREAKDOWN CATEGORY";
                this.formErrors = {};
                this.error = {};

                axios.get('{{url("breakdownCategory")}}/' + id).then(function(r) {
                    t.formData = r.data;
                    $('#modal-form').modal('show');
                })

                .catch(function(error) {
                    if (error.response.status == 500) {
                        var error = error.response.data;
                        alert(error.message + ". " + error.file + ":" + error.line)
                    }
                });
            },
            update: function() {
                var t = this;
                axios.put('{{url("breakdownCategory")}}/' + this.formData.id, this.formData).then(function(r) {
                    $('#modal-form').modal('hide');
                    $('#bootgrid').bootgrid('reload');
                })
                // validasi
                .catch(function(error) {
                    if (error.response.status == 422) {
                        t.formErrors = error.response.data.errors;
                    }

                    if (error.response.status == 500) {
                        t.error = error.response.data;
                    }
                });
            },
            delete: function(id) {
                if (confirm('Anda yakin akan menghapus data ini?')) {
                    axios.delete('{{url("breakdownCategory")}}/' + id)

                    .then(function(r) {
                        if (r.data.status == true) {
                            $('#bootgrid').bootgrid('reload');
                        } else {
                            alert(r.data.message);
                        }
                    })

                    .catch(function(error) {
                        if (error.response.status == 500) {
                            var error = error.response.data;
                            alert(error.message + ". " + error.file + ":" + error.line)
                        }
                    });
                }
            },
        },
        mounted: function() {

            var t = this;

            var grid = $('#bootgrid').bootgrid({
                rowCount: [10,25,50,100],
                ajax: true, url: '{{url('breakdownCategory')}}',
                ajaxSettings: {method: 'GET', cache: false},
                searchSettings: { delay: 100, characters: 3 },
                templates: {
                    header: '<div id="@{{ctx.id}}" class="pull-right @{{css.header}}"><div class="actionBar"><p class="@{{css.search}}"></p><p class="@{{css.actions}}"></p></div></div>'
                },
                formatters: {
                    "commands": function(column, row) {
                        var t = t;
                        return '<a href="#" class="btn btn-info btn-xs c-edit" data-id="'+row.id+'"><i class="icon-pencil"></i></a> ' +
                            '<a href="#" class="btn btn-danger btn-xs c-delete" data-id="'+row.id+'"><i class="icon-trash"></i></a>';
                    }
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
