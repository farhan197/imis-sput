@extends('layouts.app')

@section('content')

<div class="panel panel-primary" id="app">
    <div class="panel-body">
        <h3 class="pull-left text-primary">BARGINGS <small>Manage</small></h3>
        @can('create', App\Barging::class)
        <span class="pull-right" style="margin:15px 0 15px 10px;">
            <a href="#" @click="add" class="btn btn-primary"><i class="icon-plus-circled"></i></a>
        </span>
        @endcan
        <table class="table table-striped table-hover " id="bootgrid" style="border-top:2px solid #ddd">
            <thead>
                <tr>
                    <th data-column-id="id" data-width="3%">ID</th>
                    <th data-column-id="customer">Customer</th>
                    <th data-column-id="tugboat">Tugboat</th>
                    <th data-column-id="barge">Barge</th>
                    <th data-column-id="buyer">Buyer</th>
                    <th data-column-id="jetty">Jetty</th>
                    <th data-column-id="cargo" data-formatter="cargo"data-width="200px">Cargo</th>
                    <th data-column-id="volume">Volume</th>
                    <th data-column-id="start">Start</th>
                    <th data-column-id="stop">Stop</th>
                    <th data-column-id="duration">Duration</th>
                    <th data-column-id="status">Status</th>
                    <th data-column-id="description">Description</th>
                    @can('updateOrDelete', App\Barging::class)
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

    @can('createOrUpdate', App\Barging::class)
    @include('barging._form')
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
            jetties: {!! App\Jetty::selectRaw('id AS id, name AS text')->orderBy('name', 'ASC')->get() !!},
            buyers: {!! App\Buyer::selectRaw('id AS id, name AS text')->orderBy('name', 'ASC')->get() !!},
            tugboats: {!! App\Tugboat::selectRaw('id AS id, name AS text')->orderBy('name', 'ASC')->get() !!},
            barges: {!! App\Barge::selectRaw('id AS id, name AS text')->orderBy('name', 'ASC')->get() !!},
            customers: {!! App\Customer::selectRaw('id AS id, name AS text')->orderBy('name', 'ASC')->get() !!},
            seams: {!! App\Seam::selectRaw('id AS id, name AS text')->orderBy('name', 'ASC')->get() !!},
        },
        methods: {
            add: function() {
                // reset the form
                this.formTitle = "ADD BARGING";
                this.formData = {
                    start: moment().format('YYYY-MM-DD hh:mm'),
                    barging_material: [{
                        customer_id: 0,
                        material_type: 'h',
                        seam_id: 0,
                        volume: 0
                    }]
                };
                this.formErrors = {};
                this.error = {};
                // open form
                $('#modal-form').modal('show');
            },
            addCargo: function() {
                this.formData.barging_material.push({
                    customer_id: 0,
                    material_type: 'h',
                    seam_id: 0,
                    volume: 0
                });
            },

            delCargo: function(i) {
                var _this = this;

                // kalau belum ada di database langsung hapus aja ak masalah
                if (_this.formData.barging_material[i].id == undefined) {
                    _this.formData.barging_material.splice(i,1);
                    return;
                }

                // kalau sudah ada di database harus konfirmasi
                if (!confirm('Anda yakin?')) {
                    return;
                }

                axios.delete('{{url("bargingMaterial")}}/' + _this.formData.barging_material[i].id).then(function(r) {
                    _this.formData.barging_material.splice(i,1);
                })

                .catch(function(error) {
                    var error = error.response.data;
                    toastr["error"](error.message + ". " + error.file + ":" + error.line);
                });
            },

            store: function() {
                block('form');
                var t = this;

                axios.post('{{url("barging")}}', this.formData).then(function(r) {
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
                this.formTitle = "EDIT BARGING";
                this.formErrors = {};
                this.error = {};

                axios.get('{{url("barging")}}/' + id).then(function(r) {
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
                axios.put('{{url("barging")}}/' + this.formData.id, this.formData).then(function(r) {
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
                            axios.delete('{{url("barging")}}/' + id)

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
                ajax: true, url: '{{url('barging')}}',
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
                    commands: function(column, row) {
                        return '@can("update", App\Barging::class) <a href="#" class="btn btn-info btn-xs c-edit" data-id="'+row.id+'"><i class="icon-pencil"></i></a> @endcan' +
                            '@can("delete", App\Barging::class) <a href="#" class="btn btn-danger btn-xs c-delete" data-id="'+row.id+'"><i class="icon-trash"></i></a> @endcan';
                    },
                    cargo: function(c, r) {
                        var cargo = '';
                        r.barging_material.forEach(function(m) {
                            cargo += '[' + t.customers.filter(c => c.id === m.customer_id)[0].text;
                            cargo += m.material_type === 'h' ? ', HIGH' : ', LOW';
                            var seam = t.seams.filter(s => s.id === m.seam_id);
                            cargo += seam.length > 0 ? ', ' + seam[0].text : '';
                            cargo += ', ' + m.volume + 'T]<br />';
                        });

                        return cargo;
                    }
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