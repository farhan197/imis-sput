<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Authorization;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Absensi' => 'App\Policies\AbsensiPolicy',
        'App\Area' => 'App\Policies\AreaPolicy',
        'App\Asset' => 'App\Policies\AssetPolicy',
        'App\AssetLocation' => 'App\Policies\AssetLocationPolicy',
        'App\AssetStatus' => 'App\Policies\AssetStatusPolicy',
        'App\AssetTaking' => 'App\Policies\AssetTakingPolicy',
        'App\Authorization' => 'App\Policies\AuthorizationPolicy',
        'App\Barge' => 'App\Policies\BargePolicy',
        'App\Barging' => 'App\Policies\BargingPolicy',
        'App\Breakdown' => 'App\Policies\BreakdownPolicy',
        'App\BreakdownCategory' => 'App\Policies\BreakdownCategoryPolicy',
        'App\BreakdownStatus' => 'App\Policies\BreakdownStatusPolicy',
        'App\Buyer' => 'App\Policies\BuyerPolicy',
        'App\ComponentCriteria' => 'App\Policies\ComponentCriteriaPolicy',
        'App\Customer' => 'App\Policies\CustomerPolicy',
        'App\Contractor' => 'App\Policies\ContractorPolicy',
        'App\DailyCheckSetting' => 'App\Policies\DailyCheckSettingPolicy',
        'App\Department' => 'App\Policies\DepartmentPolicy',
        'App\DefaultMaterial' => 'App\Policies\DefaultMaterialPolicy',
        'App\Dormitory' => 'App\Policies\DormitoryPolicy',
        'App\DormitoryReservation' => 'App\Policies\DormitoryReservationPolicy',
        'App\Egi' => 'App\Policies\EgiPolicy',
        'App\Employee' => 'App\Policies\EmployeePolicy',
        'App\FuelTank' => 'App\Policies\FuelTankPolicy',
        'App\FuelTankTera' => 'App\Policies\FuelTankTeraPolicy',
        'App\FlowMeter' => 'App\Policies\FlowMeterPolicy',
        'App\FuelRefill' => 'App\Policies\FuelRefillPolicy',
        'App\Hopper' => 'App\Policies\JettyPolicy',
        // 'App\Jabatan' => 'App\Policies\JabatanPolicy',
        'App\Jetty' => 'App\Policies\JettyPolicy',
        'App\Location' => 'App\Policies\LocationPolicy',
        'App\MaterialStock' => 'App\Policies\MaterialStockPolicy',
        'App\Meal' => 'App\Policies\MealPolicy',
        'App\MealLocation' => 'App\Policies\MealLocationPolicy',
        'App\Office' => 'App\Policies\OfficePolicy',
        'App\Owner' => 'App\Policies\OwnerPolicy',
        'App\Pitstop' => 'App\Policies\PitstopPolicy',
        'App\PortActivity' => 'App\Policies\PortActivityPolicy',
        'App\Position' => 'App\Policies\PositionPolicy',
        'App\Prajob' => 'App\Policies\PrajobPolicy',
        'App\ProductivityPlan' => 'App\Policies\ProductivityPlanPolicy',
        'App\RunningText' => 'App\Policies\RunningTextPolicy',
        'App\Subcont' => 'App\Policies\SubcontPolicy',
        'App\SubcontUnit' => 'App\Policies\SubcontUnitPolicy',
        'App\Sadp' => 'App\Policies\SadpPolicy',
        'App\Seam' => 'App\Policies\SeamPolicy',
        'App\StaffCategory' => 'App\Policies\StaffCategoryPolicy',
        'App\StopWorkingPrediction' => 'App\Policies\StopWorkingPredictionPolicy',
        'App\StockArea' => 'App\Policies\AreaPolicy',
        'App\StockDumping' => 'App\Policies\StockDumpingPolicy',
        'App\SupervisingPrediction' => 'App\Policies\SupervisingPredictionPolicy',
        'App\TerminalAbsensi' => 'App\Policies\TerminalAbsensiPolicy',
        'App\Tugboat' => 'App\Policies\TugboatPolicy',
        'App\Unit' => 'App\Policies\UnitPolicy',
        'App\UnitCategory' => 'App\Policies\UnitCategoryPolicy',
        'App\User' => 'App\Policies\UserPolicy',
        'App\WarningPart' => 'App\Policies\WarningPartPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if ($user->super_admin) {
                return true;
            }
        });

        // Register controller yang bukan resource disini
        Gate::define('view-leadtime-breakdown-unit', function($user) {
            return Authorization::where('controller', 'LeadTimeBreakdownUnit')
                ->where('user_id', $user->id)
                ->where('view', 1)->count();
        });

        Gate::define('view-leadtime-daily-check', function($user) {
            return Authorization::where('controller', 'LeadTimeDailyCheck')
                ->where('user_id', $user->id)
                ->where('view', 1)->count();
        });

        Gate::define('view-breakdown-pcr', function($user) {
            return Authorization::where('controller', 'BreakdownPcr')
                ->where('user_id', $user->id)
                ->where('view', 1)->count();
        });

        Gate::define('update-breakdown-pcr', function($user) {
            return Authorization::where('controller', 'BreakdownPcr')
                ->where('user_id', $user->id)
                ->where('update', 1)->count();
        });

        Gate::define('view-hcgs', function($user) {
            return Authorization::where('controller', 'Hcgs')
                ->where('user_id', $user->id)
                ->where('view', 1)->count();
        });

        Gate::define('view-backup', function($user) {
            return Authorization::where('controller', 'Backup')
                ->where('user_id', $user->id)
                ->where('view', 1)->count();
        });

        Gate::define('export-backup', function($user) {
            return Authorization::where('controller', 'Backup')
                ->where('user_id', $user->id)
                ->where('export', 1)->count();
        });

        Gate::define('import-backup', function($user) {
            return Authorization::where('controller', 'Backup')
                ->where('user_id', $user->id)
                ->where('import', 1)->count();
        });

        Gate::define('create-backup', function($user) {
            return Authorization::where('controller', 'Backup')
                ->where('user_id', $user->id)
                ->where('create', 1)->count();
        });

        Gate::define('delete-backup', function($user) {
            return Authorization::where('controller', 'Backup')
                ->where('user_id', $user->id)
                ->where('delete', 1)->count();
        });
    }
}
