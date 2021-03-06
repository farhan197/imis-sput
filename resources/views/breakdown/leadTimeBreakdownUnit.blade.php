@extends('layouts.app')

@section('content')
<div class="row" id="app">
    <div class="col-md-3">
        <div class="panel minimal panel-default">
			<div class="panel-heading clearfix">
				<div class="panel-title text-primary">REMARK UNIT BY TYPE</div>
			</div>
			<table class="table table-striped table-bordered">
			    <thead>
			        <tr>
			            <th>TYPE</th>
			            <th class="text-center">READY</th>
			            <th class="text-center">B/D</th>
			        </tr>
			    </thead>
                <tbody>
                    <tr v-for="i in remarkUnitByType">
                        <td>@{{i.category}}</td>
                        <td class="text-center">@{{i.ready}}</td>
                        <td class="text-center">@{{i.breakdown}}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th>TOTAL</th>
                        <th class="text-center">@{{totalReady}}</th>
                        <th class="text-center">@{{totalBreakdown}}</th>
                    </tr>
                </tfoot>
			</table>
		</div>

        <div class="panel minimal panel-default">
			<div class="panel-heading clearfix">
				<div class="panel-title text-primary">UNIT YANG BARU READY</div>
			</div>
			<table class="table table-striped table-bordered">
                <tbody>
                    <tr v-for="u in unitready">
                        <td>@{{u.unit}}</td>
                        <td class="text-right">@{{u.ready_time}}</td>
                    </tr>
                </tbody>
			</table>
		</div>

        <div class="panel minimal panel-default">
			<div class="panel-heading clearfix">
				<div class="panel-title text-primary">SERVICE PLAN</div>
			</div>
			<table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th width="50%">TODAY</th>
                        <th width="50%">TOMORROW</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div v-for="p in todayPlan">
                                @{{p.name}}
                            </div>
                        </td>
                        <td>
                            <div v-for="p in tomorrowPlan">
                                @{{p.name}}
                            </div>
                        </td>
                    </tr>
                </tbody>
			</table>
		</div>

    </div>
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="pull-right">
                    <input type="text" v-model="searchPhrase" class="form-control" placeholder="Search">
                </div>
                <span class="text-primary">STATUS & LEAD TIME B/D UNIT</span>
                <div class="clearfix">

                </div>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>UNIT</th>
                        <th>TYPE</th>
                        <th>B/D TYPE</th>
                        <th>B/D STATUS</th>
                        <th>LOKASI B/D</th>
                        <th>HM/KM</th>
                        <th>PROBLEM</th>
                        <th>TIME IN</th>
                        <th class="text-right">DOWN TIME</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(b, i) in breakdowns" :class="rowClass[b.breakdown_category]">
                        <td>@{{i+1}}</td>
                        <td>@{{b.unit}}</td>
                        <td>@{{b.unit_category}}</td>
                        <td>@{{b.breakdown_category}}</td>
                        <td>@{{b.breakdown_status}}</td>
                        <td>@{{b.location}}</td>
                        <td>@{{b.hm}}/@{{b.km}}</td>
                        <td>@{{b.diagnosa}}</td>
                        <td>@{{b.time_in}}</td>
                        <td class="text-right">@{{b.downtime}}</td>
                    </tr>
                </tbody>
            </table>
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
        breakdowns: [],
        totalReady: 0,
        totalBreakdown: 0,
        remarkUnitByType: [],
        todayPlan: [],
        tomorrowPlan: [],
        unitready: [],
        searchPhrase: '',
        rowClass: {
            ICM: 'danger',
            USM: 'warning',
            SCM: 'info',
            TRM: 'warning',
        }
    },
    methods: {
        getData: function() {
            var _this = this;
            axios.get('{{url("leadTimeBreakdownUnit")}}?searchPhrase=' + _this.searchPhrase).then(function(r) {
                _this.breakdowns = r.data;

            })

            .catch(function(error) {
                var error = error.response.data;
                toastr["error"](error.message + ". " + error.file + ":" + error.line)
            });

            setTimeout(_this.getData, 3000);
        },
        getTodayPlan: function() {
            var _this = this;
            axios.get('{{url("dailyCheckSetting/todayPlan")}}').then(function(r) {
                _this.todayPlan = r.data;
            })

            .catch(function(error) {
                var error = error.response.data;
                toastr["error"](error.message + ". " + error.file + ":" + error.line)
            });
        },
        getTomorrowPlan: function() {
            var _this = this;
            axios.get('{{url("dailyCheckSetting/tomorrowPlan")}}').then(function(r) {
                _this.tomorrowPlan = r.data;
            })

            .catch(function(error) {
                var error = error.response.data;
                toastr["error"](error.message + ". " + error.file + ":" + error.line)
            });
        },
        getDataRemarkUnitByType: function() {
            var _this = this;
            axios.get('{{url("unit/remarkUnitByType")}}').then(function(r) {
                _this.remarkUnitByType = r.data;
                _this.totalReady = 0;
                _this.totalBreakdown = 0;

                r.data.forEach(function(b) {
                    _this.totalReady += b.ready;
                    _this.totalBreakdown += b.breakdown;
                });
            })

            .catch(function(error) {
                var error = error.response.data;
                toastr["error"](error.message + ". " + error.file + ":" + error.line)
            });

            setTimeout(_this.getDataRemarkUnitByType, 5000);
        },
        getUnitReady: function() {
            var _this = this;
            axios.get('{{url("breakdown/getUnitReady")}}').then(function(r) {
                _this.unitready = r.data;
            })

            .catch(function(error) {
                var error = error.response.data;
                toastr["error"](error.message + ". " + error.file + ":" + error.line)
            });

            setTimeout(_this.getUnitReady, 5000);
        },
    },
    mounted: function() {
        this.getData();
        this.getUnitReady();
        this.getTodayPlan();
        this.getTomorrowPlan();
        this.getDataRemarkUnitByType();
    }
});

</script>
@endpush
