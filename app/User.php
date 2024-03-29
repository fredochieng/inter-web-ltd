<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

use DB;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

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
    // public function user_detail()
    // {
    //     return $this->hasOne('App\Model\UserDetail');
    // }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function getClients()
    {
        $data['clients'] = DB::table('users')
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
            ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id', '=', 'users_details.created_by')
            ->leftJoin('accounts', 'users.id', '=', 'accounts.user_id')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('user_pay_modes', 'users.id', '=', 'user_pay_modes.user_id')
            ->leftJoin('payment_methods', 'user_pay_modes.pay_mode_id', '=', 'payment_methods.method_id')
            ->leftJoin('banks', 'user_pay_modes.pay_bank_id', '=', 'banks.bank_id')
            ->where('model_has_roles.role_id', '=', '3')
            ->orderBy('users.id', 'asc')->get();
        return $data['clients'];
    }


    public static function find_client($client, $client_value)
    {


        $query = "SELECT ";
        $query .= "users.*, ";
        $query .= "accounts.*, ";
        $query .= "users_details.*, ";
        $query .= "FROM users ";
        $query .= "LFFT OUTER JOIN users_details ON users.id = users_details.user_id ";
        $query .= "LEFT OUTER JOIN accounts ON users.id = accounts.user_id ";

        $client_id = $client;
        $clients_value = $client_value;
        switch ($client_id) {
            case "phone_no":
                $phone_find = DB::table('users_details')->where(array('telephone' => $subscriber_value))->pluck('telephone')->all();
                if (count($phone_find) > 0) {
                    $phone_comma_separated_string = implode(",", $phone_find);
                } else {
                    $phone_comma_separated_string = 0;
                }
                $query .= "WHERE users IN({$phone_comma_separated_string}) ";
                break;

            case "account_no":
                $query .= "WHERE account_no='{$subscriber_value}' ";
                break;

            case "telephone":
                $query .= "WHERE telephone='{$subscriber_value}' ";
                break;
        }


        $data = DB::select($query);
        return $data;
    }

    public static function find_clients($request)
    {
        // $role_id = 3;
        $query = "SELECT ";
        $query .= "users.*";

        $query .= "FROM users ";

        $query .= "LEFT JOIN users_details ON users.id = users_details.user_id ";
        $query .= "LEFT JOIN accounts ON users.id = accounts.user_id ";

        // $query = "SELECT ";
        // $query .= "accounts.*";

        // $query .= "FROM accounts ";

        // $query .= "LEFT JOIN users ON accounts.user_id = users.id ";
        // $query .= "LEFT JOIN users_details ON users.id = users_details.user_id ";

        if (isset($_GET['find_client_by'])) {

            $find_by = $request->get('find_client_by');
            $find_by_value = $request->get('find_value');

            switch ($find_by) {
                case "id_no":
                    $query .= "WHERE id_no={$find_by_value} ";
                    break;

                case "name":
                    $query .= "WHERE name='{$find_by_value}' ";
                    break;

                case "account_no":
                    $query .= "WHERE account_no='{$find_by_value}' ";
                    break;

                case "telephone":
                    $query .= "WHERE telephone='{$find_by_value}' ";
                    break;
            }
        }

        $data = DB::select($query);
        return $data;
    }

    public static function get_telephone()
    {
        $telephone = DB::table('users_details')
            ->select(
                DB::raw('users_details.*')
            )
            ->get();

        return $telephone;
    }

    public static function getTotalCustomers()
    {
        $data['total_customers'] = DB::table('users')
            ->select(
                DB::raw('users.*'),
                DB::raw('model_has_roles.*')
            )
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where('model_has_roles.role_id', '=', '3')
            ->count();
        return $data['total_customers'];
    }

    public static function getTotalSecretaries()
    {
        $data['total_secretries'] = DB::table('users')
            ->select(
                DB::raw('users.*'),
                DB::raw('model_has_roles.*')
            )
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where('model_has_roles.role_id', '=', '2')
            ->count();
        return $data['total_secretries'];
    }

    public static function getSecretaries()
    {
        $data['secreatries'] = DB::table('users')
            ->select(
                DB::raw('users.id as sec_id'),
                DB::raw('users.*'),
                DB::raw('users_details.*'),
                DB::raw('model_has_roles.*')

            )
            ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id', '=', 'users_details.created_by')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where('model_has_roles.role_id', '=', '2')
            ->orderBy('users.id', 'asc')->get();

        return $data['secreatries'];
    }
}