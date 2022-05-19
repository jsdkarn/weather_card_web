<?php
$isHas = true;

require_once './environment.php';

if (isset($_GET['latitude']) && isset($_GET['longitude'])) {
    $data = getHourlyAPIData($_GET['latitude'], $_GET['longitude'], 1);

    $condConst = array(
        1 => array("ท้องฟ้าแจ่มใส", "Clear", "./icon/cloudySun.php"),
        2 => array("มีเมฆบางส่วน", "Partly-cloudy", "./icon/cloudySun.php"),
        3 => array("เมฆเป็นส่วนมาก", "Cloudy", "./icon/cloudy.php"),
        4 => array("มีเมฆมาก", "Overcast", "./icon/cloudy.php"),
        5 => array("ฝนตกเล็กน้อย", "Light-rain", "./icon/rainy.php"),
        6 => array("ฝนปานกลาง", "Moderate-rain", "./icon/cloudyLightning.php"),
        7 => array("ฝนตกหนัก", "Heavy-rain", "./icon/cloudyLightning.php"),
        8 => array("ฝนฟ้าคะนอง", "Thunderstorm", "./icon/cloudRainLightning.php"),
        9 => array("อากาศหนาวจัด", "Very-cold", "./icon/snowy.php"),
        10 => array("อากาศหนาว", "Cold", "./icon/sunnyWind.php"),
        11 => array("อากาศเย็น", "Cool", "./icon/sunnyWind.php"),
        12 => array("อากาศร้อนจัด", "Very-hot", "./icon/sunny.php")
    );

    if ($data != NULL) {
        $isHas = true;
        $forecasts = $data->forecasts;
        $weatherCond = $forecasts[0]->data->cond;
        $rain = $forecasts[0]->data->rain;
        $humidity = $forecasts[0]->data->rh;
        $temp = $forecasts[0]->data->tc;
        $condPath = $condConst[$weatherCond][2];
        $condName = $condConst[$weatherCond][0];
        $picUrl = getPhotoUrl($condConst[$weatherCond][1]);
        $time = DateThai($forecasts[0]->time);
    } else {
        $isHas = false;
    }
}

function getPhotoUrl($keyword)
{
    $clientId = $_ENV["CLIENT_ID"];
    $url = "https://api.unsplash.com/search/photos/?page=1&per_page=5&query=$keyword-sky&client_id=$clientId";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $headers = array(
        "Accept: application/json"
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $resp = curl_exec($curl);
    curl_close($curl);
    $response = json_decode($resp);
    $index = rand(0, 4);
    $data = $response->results[$index];
    if (!empty($data)) {
        return $data->urls->regular;
    } else {
        return NULL;
    }
}
function DateThai($strDate)
{
    date_default_timezone_set('Asia/Bangkok');
    $strYear = date("Y", strtotime($strDate)) + 543;
    $strMonth = date("n", strtotime($strDate));
    $strDay = date("j", strtotime($strDate));
    $strHour = date("H", strtotime($strDate));
    $strMinute = date("i", strtotime($strDate));
    $strSeconds = date("s", strtotime($strDate));
    $strGMT = date("P", strtotime($strDate));
    $strMonthCut = array("", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.");
    $strMonthThai = $strMonthCut[$strMonth];
    return "$strDay $strMonthThai $strYear เวลา $strHour:$strMinute";
}
function getHourlyAPIData($lat, $lon, $duration)
{
    $url = "https://data.tmd.go.th/nwpapi/v1/forecast/location/hourly/at?lat=$lat&lon=$lon&fields=tc,rh,rain,cond&$duration";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $token = $_ENV["TOKEN"];
    $headers = array(
        "Accept: application/json",
        "Authorization: Bearer $token",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    curl_close($curl);
    $response = json_decode($resp);
    $data = $response->WeatherForecasts[0];
    if (!empty($data)) {
        return $data;
    } else {
        return NULL;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Weather</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <div class="row justify-content-center  align-items-center" style="height: 100vh;">
            <div class="col-12 col-md-6">
                <?php if ($isHas) { ?>
                    <div class="card rounded-4" style="background: #000; color:#fff; opacity: 0.7;">
                        <div class="card-body">
                            <div class="row justify-content-end">
                                <div class="col-12 col-md-6">
                                    <div class="row justify-content-start align-items-center">
                                        <div class="col-12 col-md-4">
                                            <?php include $condPath; ?>
                                        </div>
                                        <div class="col-12 col-md-8">
                                            <p><?= $condName ?></p>
                                        </div>
                                    </div>
                                    <p>ปริมาณฝนรายชั่วโมง <?= $rain ?> mm</p>
                                    <p>ความชื้นสัมพัทธ์ที่ระดับพื้นผิว <?= $humidity ?> %</p>
                                </div>
                                <div class="col-12 col-md-6  align-self-center">
                                    <p style="text-align:center" class="temp"><?= floor($temp) ?> ℃</p>
                                </div>
                                <div class="col-12 align-self-center">
                                    <div style="text-align:end;">ข้อมูล <?= $time ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <h1>No location</h1>
                <?php } ?>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://momentjs.com/downloads/moment.js"></script>
        <script>
            $(document).ready(function() {
                // history.replaceState(null, document.querySelector("title").innerText, window.location.pathname)
            });
        </script>
</body>
<style>
    @media screen and (max-width: 600px) {
        p {
            font-size: 15px;
        }

        p.temp {
            font-size: 30px;
        }
    }

    @media screen and (min-width: 600px) {
        p {
            font-size: 18px;
        }

        p.temp {
            font-size: 50px;
        }
    }

    body {
        background-size: cover;
        background-repeat: no-repeat;
        background-image: url(<?= $picUrl ?>);
    }

    * {
        margin: 0;
        padding: 0;
    }

    .element {
        height: 50px;
        width: 50px;
        margin: 10px;
    }

    .elements {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;

    }

    .white {
        fill: #FFFFFF
    }

    .gray {
        fill: #E0E0E0
    }

    .yellow {
        fill: #FFEB3B
    }
</style>

</html>