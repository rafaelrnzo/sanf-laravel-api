<?php

namespace NbsPhp\Core\Commands;

use Illuminate\Console\Command;
use NbsPhp\Core\Models\AuthRbacModel;
use NbsPhp\Core\Models\UserStatusAbstractModel;

class ReloadUserPermissionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:reload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reload User Permission';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = AuthRbacModel::where('status_id', UserStatusAbstractModel::STATUS_ACTIVE)->with('roles')->has('roles')->get();
        foreach ($users as $user) {
            $permissons = collect();
            foreach ($user->roles as $role) {
                $permissonKeys = collect($role->permissions)->pluck('key');
                $permissons = $permissons->merge($permissonKeys);
            }
            $user->permissions = $permissons;
            $user->save();
        }
    }
}
