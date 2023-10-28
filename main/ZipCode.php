<?php
function GetCoordinatesByZIP($zipcode) {
    $userAgent = "MyGeocodingApp/1.0 (PHP)";
    $apiUrl = "https://nominatim.openstreetmap.org/search?format=json&limit=1&q={$zipcode}";
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $apiUrl);
    curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($curl);
    curl_close($curl);
    
    $data = json_decode($response);
    
    if (!empty($data) && is_array($data) && count($data) > 0) {
        $firstResult = $data[0];
        if (isset($firstResult->lat) && isset($firstResult->lon)) {
            $latitude = $firstResult->lat;
            $longitude = $firstResult->lon;
            return [$latitude, $longitude];
        }
    }
    
    return null;
}

function GetDistanceBetweenCoordinates($Coordinate1, $Coordinate2){
    $distance = (6371 * 3.1415926 * sqrt(
        ($Coordinate1[0] - $Coordinate2[0])
        * ($Coordinate1[0] - $Coordinate2[0])
        + cos($Coordinate1[0] / 57.29578)
        * cos($Coordinate2[0] / 57.29578)
        * ($Coordinate1[1] - $Coordinate2[1])
        * ($Coordinate1[1] - $Coordinate2[1])
    ) / 180);

    return $distance;
}

function GetDistanceBetweenZIPCodes($Zip1, $Zip2){
    $Coordinate1 = GetCoordinatesByZIP($Zip1);
    $Coordinate2 = GetCoordinatesByZIP($Zip2);

    return GetDistanceBetweenCoordinates($Coordinate1, $Coordinate2);
}
echo GetDistanceBetweenZIPCodes("7364ac", "7361ap");
?>