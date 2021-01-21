<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class MapService
{
    public function isValidAddress($coordinates, $address)
    {
        $response = Http::get(env('GEOCODING_API_URL')
            .'/json?latlng='
            .$coordinates
            .'&result_type=administrative_area_level_5|sublocality&key='
            .env('GMAP_API_KEY')
        );

        if ($response->status() === 200) {
            if ($response->json('status') === 'OK' AND $response->json('results')[0]['formatted_address'] === $address) {
                $serviceArea = implode('|', config('system.service_area'));

                foreach ($response->json('results')[0]['address_components'] AS $addressComponent) {
                    if (in_array('administrative_area_level_2', $addressComponent['types'])
                        AND preg_match('/'.$serviceArea.'/i', $addressComponent['long_name'])
                    ) {
                        return true;
                    }
                }

                return false;
            }
        } else {
            return false;
        }
    }

    public function getDistanceInKm($origin, $destination)
    {
        $response = Http::get(env('DISTANCEMATRIX_API_URL')
            .'/json?origins='.$origin
            .'&destinations='.$destination
            .'&key='.env('GMAP_API_KEY')
        );

        if ($response->status() === 200 AND $response->json('status') === 'OK') {
            return [true, round($response->json('rows')[0]['elements'][0]['distance']['value'] / 1000)];
        } else {
            return [false, 'Unable to get distance between coordinates.'];
        }
    }
}