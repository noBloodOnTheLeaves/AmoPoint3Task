<?php

namespace App\Http\Controllers\SiteVisit;

use App\Models\SiteVisit\SiteVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Stevebauman\Location\Facades\Location;

class SiteVisitController extends \App\Http\Controllers\Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $visits = SiteVisit::query()->get();
        return response()->json($visits);
    }

    public function save(Request $request)
    {
        /*ip, city , user_agent, site*/
        $ip = $request->ip();
        $insert = [
            'ip' => $ip,
            'city' => $this->getCityByIp($ip),
            'user_agent' => $request->userAgent(),
            'site' => $request->get('site') ?? 'site'
        ];
        if(!empty($insert['ip']) && !empty($insert['user_agent'])){
            if($this->saveVisit($insert)){
                return response()->json(['success' => true, 'data' => $insert],200);
            }else{
                return response()->json(['success' => false],422);
            }
        }

    }

    private function getCityByIp($ip): string
    {
        if ($position = Location::get()) {
            return $position->cityName;
        } else {
            $json     = file_get_contents("http://ipinfo.io/$ip/geo");
            $json     = json_decode($json, true);
            $city  = $json['city'];
            if(empty($city)){
                return '';
            }
        }
        return $city;
    }

    private function saveVisit(array $insert): bool
    {
        $save = SiteVisit::query()
            ->where('ip', $insert['ip'])
            ->where('user_agent', $insert['user_agent'])
            ->whereDate('created_at', Carbon::now()->format('Y-m-d'))
            ->firstOrCreate($insert);
        return !!$save;
    }
}
