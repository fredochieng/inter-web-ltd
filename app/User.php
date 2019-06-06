<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use DB;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $table = 'users';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function getInactiveCustomers()
    {
        $data['inactive_clients'] = DB::table('users')
            ->select(
                DB::raw('users.*'),
                DB::raw('users_details.*'),
                DB::raw('users_details.created_by AS created_name'),
                DB::raw('accounts.*'),
                DB::raw('model_has_roles.*'),
                DB::raw('user_pay_modes.*'),
                DB::raw('payment_methods.*'),
                DB::raw('banks.*')


            )
            // ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id')
            ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id', '=', 'users_details.created_by')
            ->leftJoin('accounts', 'users.id', '=', 'accounts.user_id')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('user_pay_modes', 'users.id', '=', 'user_pay_modes.user_id')
            ->leftJoin('payment_methods', 'user_pay_modes.pay_mode_id', '=', 'payment_methods.method_id')
            ->leftJoin('banks', 'user_pay_modes.pay_bank_id', '=', 'banks.bank_id')
            ->where('model_has_roles.role_id', '=', '3')
            ->where('users.status', '=', 0)->orderBy('users.id', 'asc')->get();
        return $data['inactive_clients'];
    }

    public static function getActiveCustomers()
    {
        $data['active_clients'] = DB::table('users')
            ->select(
                DB::raw('users.*'),
                DB::raw('users_details.*'),
                DB::raw('users_details.created_by AS created_name'),
                DB::raw('accounts.*'),
                DB::raw('model_has_roles.*'),
                DB::raw('user_pay_modes.*'),
                DB::raw('payment_methods.*'),
                DB::raw('banks.*')


            )
            // ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id')
            ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id', '=', 'users_details.created_by')
            ->leftJoin('accounts', 'users.id', '=', 'accounts.user_id')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('user_pay_modes', 'users.id', '=', 'user_pay_modes.user_id')
            ->leftJoin('payment_methods', 'user_pay_modes.pay_mode_id', '=', 'payment_methods.method_id')
            ->leftJoin('banks', 'user_pay_modes.pay_bank_id', '=', 'banks.bank_id')
            ->where('model_has_roles.role_id', '=', '3')
            ->where('users.status', '=', 1)->orderBy('users.id', 'asc')->get();
        return $data['active_clients'];
    }

    public static function getTotalCustomers()
    {
        $data['total_customers'] = DB::table('users')->select(DB::raw('users.*'))->count();
        return $data['total_customers'];
    }

    // public static function getCustomerData()
    // {
    //     $data['customers'] = DB::table('users')
    //         ->select(
    //             DB::raw('users.*'),
    //             DB::raw('users_details.*'),
    //             DB::raw('accounts.*'),
    //             DB::raw('investments.*')
    //         )
    //         ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id')
    //         ->leftJoin('accounts', 'users.id', '=', 'accounts.user_id')
    //         ->leftJoin('investments', 'account.id', '=', 'investments._account_no_id')
    //         ->where('users.id', '=', '22')->first();
    //     return $data['customers'];
    // }

    public static function interest($investment = 60000, $year = 1, $rate = 15, $n = 1)
    {
        $investment = 60000;
        echo $investment;
        $year = 1;
        $rate = 15;
        $n = 1;
        $accummulated = 0;
        if ($year > 1) {
            $accummulated = interest($investment, $year - 1, $rate, $n);
        }
        $accummulated += $investment;
        echo "<br/>";
        $accummulated = $accummulated * pow(1 + $rate / (100 * $n), $n);
      return $accummulated;

    }
}
