<?php

use App\Models\Grade;
use App\Models\Language;
use App\Models\Settings;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;


if (! function_exists('getYouTubeVideoId')) {
    function getYouTubeVideoId($url)
    {
        // Parse the URL to get its components
        $urlComponents = parse_url($url);
        // Check if the host is YouTube
        if (
            isset($urlComponents['host']) &&
            (strpos($urlComponents['host'], 'youtube.com') !== false ||
                strpos($urlComponents['host'], 'youtu.be') !== false)
        ) {
            // Check if the URL contains a 'v' query parameter (standard YouTube URL)
            if (isset($urlComponents['query'])) {
                parse_str($urlComponents['query'], $queryParams);
                if (isset($queryParams['v'])) {
                    return $queryParams['v'];
                }
            }

            // Check if the URL is a shortened YouTube URL (youtu.be)
            if (isset($urlComponents['path']) && strpos($urlComponents['host'], 'youtu.be') !== false) {
                return ltrim($urlComponents['path'], '/');
            }
        }

        // Return null if the URL is not a valid YouTube URL or does not contain a video ID
        return null;
    }
}

if (! function_exists('settingByType')) {
    function settingByType($type)
    {
        return Settings::where('type', $type)->value("message");
    }
}

if (! function_exists('loadTenantMainAsset')) {
    function loadTenantMainAsset($key, $defaultValue = null)
    {

        $setting = Cache::remember("tenant_app_settings", now()->addDay(), function () {
            return Settings::whereIn('type', ['logo1', 'logo2', 'favicon'])->pluck("message", 'type')->toArray();
        });

        if (isset($setting[$key]) && !empty($setting[$key])) {
            return $setting[$key];
        }

        return $defaultValue;
    }
}
if (! function_exists('getSettings')) {
    function getSettings($type = '')
    {
        $settingList = [];
        if (empty($type)) {
            $setting = Settings::get();
        } else {
            $setting = Settings::where('type', $type)->get();
        }
        foreach ($setting as $row) {
            $settingList[$row->type] = $row->message;
        }
        return $settingList;
    }
}
if (! function_exists('get_language')) {
    function get_language()
    {
        return Language::get();
    }
}
if (! function_exists('getTimeFormat')) {
    function getTimeFormat()
    {
        $timeFormat = array();
        $timeFormat['h:i a'] = 'h:i a - ' . date('h:i a');
        $timeFormat['h:i A'] = 'h:i A - ' . date('h:i A');
        $timeFormat['H:i'] = 'H:i - ' . date('H:i');
        return $timeFormat;
    }
}

if (! function_exists('getDateFormat')) {

    function getDateFormat()
    {
        $dateFormat = array();
        $dateFormat['d/m/Y'] = 'd/m/Y - ' . date('d/m/Y');
        $dateFormat['m/d/Y'] = 'm/d/Y - ' . date('m/d/Y');
        $dateFormat['Y/m/d'] = 'Y/m/d - ' . date('Y/m/d');
        $dateFormat['Y/d/m'] = 'Y/d/m - ' . date('Y/d/m');
        $dateFormat['m-d-Y'] = 'm-d-Y - ' . date('m-d-Y');
        $dateFormat['d-m-Y'] = 'd-m-Y - ' . date('d-m-Y');
        $dateFormat['Y-m-d'] = 'Y-m-d - ' . date('Y-m-d');
        $dateFormat['Y-d-m'] = 'Y-d-m - ' . date('Y-d-m');
        $dateFormat['F j, Y'] = 'F j, Y - ' . date('F j, Y');
        $dateFormat['jS F Y'] = 'jS F Y - ' . date('jS F Y');
        $dateFormat['l jS F'] = 'l jS F - ' . date('l jS F');
        $dateFormat['d M, y'] = 'd M, y - ' . date('d M, y');
        return $dateFormat;
    }
}

function getTimezoneList()
{
    static $timezones = null;

    if ($timezones === null) {
        $list = DateTimeZone::listAbbreviations();
        $idents = DateTimeZone::listIdentifiers();

        $data = $offset = $added = array();
        foreach ($list as $abbr => $info) {
            foreach ($info as $zone) {
                if (! empty($zone['timezone_id']) and ! in_array($zone['timezone_id'], $added) and in_array($zone['timezone_id'], $idents)) {
                    $z = new DateTimeZone($zone['timezone_id']);
                    $c = new DateTime('', $z);
                    $zone['time'] = $c->format('H:i a');
                    $offset[] = $zone['offset'] = $z->getOffset($c);
                    $data[] = $zone;
                    $added[] = $zone['timezone_id'];
                }
            }
        }

        array_multisort($offset, SORT_ASC, $data);
        $i = 0;
        $temp = array();
        foreach ($data as $key => $row) {
            $temp[0] = $row['time'];
            $temp[1] = formatOffset($row['offset']);
            $temp[2] = $row['timezone_id'];
            $timezones[$i++] = $temp;
        }
    }
    return $timezones;
}

if (! function_exists('formatOffset')) {
    function formatOffset($offset)
    {
        $hours = $offset / 3600;
        $remainder = $offset % 3600;
        $sign = $hours > 0 ? '+' : '-';
        $hour = (int) abs($hours);
        $minutes = (int) abs($remainder / 60);

        if ($hour == 0 and $minutes == 0) {
            $sign = ' ';
        }
        return $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0');
    }
}
if (! function_exists('flattenMyModel')) {
    function flattenMyModel($model)
    {
        $modelArr = $model->toArray();
        $data = [];
        array_walk_recursive($modelArr, function ($item, $key) use (&$data) {
            $data[$key] = $item;
        });
        return $data;
    }
}

if (! function_exists('findExamGrade')) {
    function findExamGrade($percentage)
    {
        $grades = Grade::get();
        if (sizeof($grades)) {
            foreach ($grades as $row) {
                if (floor($percentage) >= $row['starting_range'] && floor($percentage) <= $row['ending_range']) {
                    return $row->grade;
                }
            }
        } else {
            return '';
        }
    }
}

if (! function_exists('resizeImage')) {
    function resizeImage($image)
    {
        return Image::make($image)->encode($image->getClientOriginalExtension())->save(null, 50);
    }
}

if (! function_exists('convertDateFormat')) {
    function convertDateFormat($dateString, $format = 'Y-m-d H:i:s')
    {
        try {
            return (new DateTime($dateString))->format($format);
        } catch (Exception $e) {
            // Handle the exception if needed or return the original date string
            return $dateString;
        }
    }
}
if (! function_exists('isRouteActive')) {
    function isRouteActive($routeName, $activeClassName = 'active')
    {
        return request()->routeIs($routeName) ? $activeClassName : '';
    }
}