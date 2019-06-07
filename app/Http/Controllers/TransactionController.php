public function SearchSubscriber(Request $request)
    {
        $per_page = $request->input('length', 10);
        $start = $request->input('start', 1);
        $page = (int)($start / $per_page);
        $query = Subs::query();
        $search_by = $request->input('search_by', "");
        $search = $request->input('search', "");
        if (!empty($search)) {
            if ($search_by == 'name') {
                $search_params = explode(" ", $search);
                foreach ($search_params as $search_param) {
                    $query->where('FIRSTNAME', 'LIKE', '%' . $search_param . '%')
                        ->orWhere('LASTNAME', 'LIKE', '%' . $search_param . '%');
                }
            } elseif ($search_by == 'account-number') {
                $query->where('SUBS', 'LIKE', '%' . $search);
            } elseif ($search_by == 'phone-number') {
                $query->where('SUBS', 'LIKE', '%' . $search);
            }
        }
        // $query = json_decode(json_encode($query), true);
        // print_pre($query , true);
        $subscribers = $query->paginate($per_page, ['SUBS',  'FIRSTNAME', 'LASTNAME', 'TITLE', 'CUSTSTATUS', 'TOWN' , 'FRANCH'], 'page',
        $page);
        // print_pre($subscribers , true);
        return response()->json(new DatatablePaginator($subscribers, $request->input('draw')));
    }

    public static function find_subscriber($subscriber , $sub_value){

        $query="SELECT ";
        $query.="subs.*, ";
        $query.="franch.name AS franch_name, franch.franch AS franch_id, ";
        $query.="citystat.town AS town_name, citystat.citystat AS city_id, citystat.county AS county, citystat.region AS region, ";
        $query.="T.english AS title, T.lists AS title_id, ";
        $query.="F.english AS bill_frequency_name, F.lists AS bill_freq_id, ";
        $query.="S.english AS customer_status, S.lists AS customer_status_id ";
        $query.="FROM subs ";

        $query.="LEFT OUTER JOIN franch ON subs.franch=franch.franch ";
        $query.="LEFT OUTER JOIN citystat ON subs.town=citystat.citystat ";
        $query.="LEFT OUTER JOIN lists AS T ON subs.title=T.lists ";
        $query.="LEFT OUTER JOIN lists AS F ON subs.billfreq=F.lists ";
        $query.="LEFT OUTER JOIN lists AS S ON subs.custstatus=S.lists ";



            $subscriber_id = $subscriber
            $subscriber_value = $sub_value;

            switch ($subscriber_id) {
                case "subscriber_id":
                    $query.="WHERE subs={$subscriber_value} ";
                    break;

                case "phone_no":
                    $phone_find=DB::table('phone')->where(array('phone'=>$subscriber_value,'phonetype'=>2030))->pluck('TABLEKEY')->all();
                    if(count($phone_find)>0){  $phone_comma_separated_string = implode (",", $phone_find); }else{ $phone_comma_separated_string =0;}
                     $query.="WHERE subs IN({$phone_comma_separated_string}) ";
                    break;
                    case "email":
                    $phone_find=DB::table('phone')->where(array('phone'=>$subscriber_value,'phonetype'=>1085))->pluck('TABLEKEY')->all();
                    if(count($phone_find)>0){  $phone_comma_separated_string = implode (",", $phone_find); }else{ $phone_comma_separated_string =0;}
                    $query.="WHERE subs IN({$phone_comma_separated_string}) ";
                    break;

                case "first_name":
                    $query.="WHERE firstname='{$subscriber_value}' ";
                    break;

                case "last_name":
                    $query.="WHERE lastname='{$subscriber_value}' ";
                    break;
            }
            // }

        $data = DB::select($query);
        return $data;

}
