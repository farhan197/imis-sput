@extends('layouts.app')

@section('content')

<div class="row" id="app">
    <div class="col-md-3">
        @if (!auth()->user()->customer_id)
        <select2 :options="customers" v-model="customer_id" data-placeholder="All Customer" data-allow-clear="true">
        </select2>
        <br>
        <br>
        @endif

        @if (!auth()->user()->contractor_id)
        <select2 :options="contractors" v-model="contractor_id" data-placeholder="All Contractor" data-allow-clear="true">
        </select2>
        <br>
        <br>
        @endif

        @if (!auth()->user()->customer_id)
        <material-stock-summary
            :customer="customer_id"
            :contractor="contractor_id"
            :group="'customer_id'"
            :header="'SUMMARY BY CUSTOMER'"
            :entity="'Customer'"></material-stock-summary>
        @endif

        @if (!auth()->user()->contractor_id)
        <material-stock-summary
            :customer="customer_id"
            :contractor="contractor_id"
            :group="'contractor_id'"
            :header="'SUMMARY BY CONTRACTOR'"
            :entity="'Contractor'"></material-stock-summary>
        @endif

        {{-- <material-stock-summary
            :customer="customer_id"
            :contractor="contractor_id"
            :group="'material_type'"
            :header="'SUMMARY BY MATERIAL TYPE'"
            :entity="'Material Type'"></material-stock-summary> --}}

        <material-stock-summary
            :customer="customer_id"
            :contractor="contractor_id"
            :group="'seam_id'"
            :header="'SUMMARY BY SEAM'"
            :entity="'Seam'"></material-stock-summary>

        {{-- <material-stock-summary
            :customer="customer_id"
            :contractor="contractor_id"
            :group="'area_id'"
            :header="'SUMMARY BY BLOCK AREA'"
            :entity="'Block Area'"></material-stock-summary> --}}

        {{-- <material-stock-summary
            :customer="customer_id"
            :contractor="contractor_id"
            :group="'stock_area_id'"
            :header="'SUMMARY BY STOCK AREA'"
            :entity="'Stock Area'"></material-stock-summary> --}}
    </div>
    <div class="col-md-9">
        <div class="panel panel-primary">
            <div class="panel-body">
                <h3 class="pull-left text-primary">STOCK BALANCED <small>Manage</small></h3>
                <span class="pull-right" style="margin:15px 0 15px 10px;">
                    @can('create', App\MaterialStock::class)
                    <a href="#" @click="add" class="btn btn-primary"><i class="icon-plus-circled"></i></a>
                    @endcan
                    @can('export', App\MaterialStock::class)
                    <a href="{{url('materialStock/export')}}" target="_blank" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> EXPORT</a>
                    @endcan
                </span>
                <table class="table table-striped table-hover " id="bootgrid" style="border-top:2px solid #ddd">
                    <thead>
                        <tr>
                            <th data-column-id="id" data-width="3%">ID</th>
                            <th data-column-id="dumping_date">Dumping Date</th>
                            <th data-column-id="material_type" data-formatter="material_type">Material Type</th>
                            <th data-column-id="seam">Seam</th>
                            @if (!auth()->user()->customer_id)
                            <th data-column-id="customer">Customer</th>
                            @endif
                            @if (!auth()->user()->contractor_id)
                            <th data-column-id="contractor">Contractor</th>
                            @endif
                            <th data-column-id="area">Area</th>
                            <th data-column-id="stock_area">Stock Area</th>

                            <th data-column-id="volume"
                                data-formatter="volume"
                                data-align="right"
                                data-header-align="right">Volume (TON)</th>

                            <th data-column-id="age"
                                data-sortable="false"
                                data-align="right"
                                data-header-align="right">Age</th>

                            @can('updateOrDelete', App\MaterialStock::class)
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

            @can('createOrUpdate', App\MaterialStock::class)
            @include('materialStock._form')
            @endcan

        </div>
    </div>
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
        customer_id: '{{auth()->user()->customer_id}}',
        contractor_id: '{{auth()->user()->contractor_id}}',
        stock_areas: {!! App\StockArea::selectRaw('stock_areas.id AS id, CONCAT(areas.name, " - ", stock_areas.name) AS text')
            ->join('areas', 'areas.id', '=', 'stock_areas.area_id')
            ->orderBy('areas.name', 'ASC')->get() !!},
        seams: {!! App\Seam::selectRaw('id AS id, name AS text')->orderBy('name', 'ASC')->get() !!},
        customers: {!! App\Customer::selectRaw('id AS id, name AS text')->orderBy('name', 'ASC')->get() !!},
        contractors: {!! App\Contractor::selectRaw('id AS id, name AS text')->orderBy('name', 'ASC')->get() !!},
    },
    methods: {
        formatNumber: function(v) {
            return parseFloat(v)
                .toFixed(0)
                .toString()
                .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        add: function() {
            // reset the form
            this.formTitle = "ADD MATERIAL STOCK BALANCED";
            this.formData = {
                customer_id: '{{auth()->user()->customer_id}}',
            };
            this.formErrors = {};
            this.error = {};
            // open form
            $('#modal-form').modal('show');
        },
        store: function() {
            block('form');
            var t = this;
            axios.post('{{url("materialStock")}}', this.formData).then(function(r) {
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
            this.formTitle = "EDIT MATERIAL STOCK BALANCED";
            this.formErrors = {};
            this.error = {};

            axios.get('{{url("materialStock")}}/' + id).then(function(r) {
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
            axios.put('{{url("materialStock")}}/' + this.formData.id, this.formData).then(function(r) {
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
                        axios.delete('{{url("materialStock")}}/' + id)

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
                0: "default",
                1: "default"
            },
            rowCount: [10,25,50,100],
            ajax: true, url: '{{url('materialStock')}}',
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
                    // return '@can("update", App\MaterialStock::class) <a href="#" class="btn btn-info btn-xs c-edit" data-id="'+row.id+'"><i class="icon-pencil"></i></a> @endcan';
                    return '@can("update", App\MaterialStock::class) <a href="#" class="btn btn-info btn-xs c-edit" data-id="'+row.id+'"><i class="icon-pencil"></i></a> @endcan' +
                        '@can("delete", App\MaterialStock::class) <a href="#" class="btn btn-danger btn-xs c-delete" data-id="'+row.id+'"><i class="icon-trash"></i></a> @endcan';
                },
                material_type: function(c, r) {
                    return r.material_type == 'l' ? 'LOW' : 'HIGH';
                },
                volume: function(c, r) {
                    return t.formatNumber(r.volume);
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
