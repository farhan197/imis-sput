@extends('layouts.app')

@section('content')
<div class="row" id="app">
<div class="panel panel-primary">
    <div class="panel-heading">
        DAILY BREADOWN REPORT
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Unit</th>
                    <th>Unit Category</th>
                    <th>B/D Type</th>
                    <th>B/D Status</th>
                    <th>Location</th>
                    <th>HM</th>
                    <th>KM</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Duration</th>
                    <th>Problem</th>
                    <th>Component Criteria</th>
                    <th>Tindakan</th>
                    <th>WO Number</th>
                    @can('updateOrDelete', App\Breakdown::class)
                    <th>Action</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                <tr v-for="b in breakdowns">
                    <td></td>
                    <td>@{{b.unit}}</td>
                    <td>@{{b.unit_category}}</td>
                    <td>@{{b.breakdown_category}}</td>
                    <td>@{{b.breakdown_status}}</td>
                    <td>@{{b.location}}</td>
                    <td>@{{b.hm}}</td>
                    <td>@{{b.km}}</td>
                    <td>@{{b.time_in}}</td>
                    <td>@{{b.time_out}}</td>
                    <td></td>
                    <td>@{{b.diagnosa}}</td>
                    <td>@{{b.component_criteria}}</td>
                    <td>@{{b.tindakan}}</td>
                    <td>@{{b.wo_number}}</td>
                    @can('updateOrDelete', App\Breakdown::class)
                    <td>
                        <a href="#" @click="edit(b.id)" class="btn btn-primary btn-xs"><i class="icon icon-pencil"></i></a>
                    </td>
                    @endcan
                </tr>
            </tbody>
        </table>
    </div>

    @can('createOrUpdate', App\Breakdown::class)
    @include('breakdown._form_pcr')
    @endcan

</div>
@endsection

@push('scripts')
<script type="text/javascript">

const app = new Vue({
    el: '#app',
    data: {
        breakdowns: [],
        formData: {},
        formErrors: {},
        error: {},
    },
    methods: {
        getData: function() {
            var _this = this;
            axios.get('{{url("breakdown/pcr")}}').then(function(r) {
                _this.breakdowns = r.data;
            })

            .catch(function(error) {
                var error = error.response.data;
                toastr["error"](error.message + ". " + error.file + ":" + error.line)
            });

            setTimeout(_this.getData, 3000);
        },
        edit: function(id) {
            var _this = this;
            this.formErrors = {};
            this.error = {};

            axios.get('{{url("breakdown")}}/' + id).then(function(r) {
                _this.formData = r.data;
                $('#modal-form').modal('show');
            })

            .catch(function(error) {
                var error = error.response.data;
                toastr["error"](error.message + ". " + error.file + ":" + error.line)
            });
        },
        update: function() {
            block('form');
            var _this = this;
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
                    _this.formErrors = error.response.data.errors;
                }

                else {
                    _this.error = error.response.data;
                }
            });
        },
    },
    mounted: function() {
        this.getData();
    }
});

</script>
@endpush
