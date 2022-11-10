<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Support\Facades\DB;
use stdClass;

class ChartController extends Controller
{
    public function raport()
    {
        $chartData = $this->prepareBarChart();
        return view('raports', compact('chartData'));

    }

    public function raportLine()
    {
        $chartData = $this->prepareLineChart();
        return view('raports', compact('chartData'));
    }

    public function colorArray()
    {
        $COLOR_ARRAY = ['#FF6633', '#FFB399', '#FF33FF', '#FCBA03', '#00B3E6',
            '#E6B333', '#3366E6', '#999966', '#99FF99', '#B34D4D',
            '#80B300', '#809900', '#E6B3B3', '#6680B3', '#66991A',
            '#FF99E6', '#CCFF1A', '#FF1A66', '#E6331A', '#33FFCC',
            '#66994D', '#B366CC', '#4D8000', '#B33300', '#CC80CC',
            '#66664D', '#991AFF', '#E666FF', '#4DB3FF', '#1AB399',
            '#E666B3', '#33991A', '#CC9999', '#B3B31A', '#00E680',
            '#4D8066', '#809980', '#E6FF80', '#1AFF33', '#999933',
            '#FF3380', '#CCCC00', '#66E64D', '#4D80CC', '#9900B3',
            '#E64D66', '#4DB380', '#FF4D4D', '#99E6E6', '#6666FF'];
        return $COLOR_ARRAY;
    }

    public function sumTime($time_array)
    {
        $sum = strtotime('00:00:00');
        $totaltime = 0;

        foreach ($time_array as $element) {
            $timeinsec = strtotime($element) - $sum;
            $totaltime = $totaltime + $timeinsec;
        }

        $h = intval($totaltime / 3600);
        $totaltime = $totaltime - ($h * 3600);
        $m = intval($totaltime / 60);
        $s = $totaltime - ($m * 60);
        return "$h:$m:$s";
    }

    public function averageTime($total, $count, $rounding = 0)
    {
        $total = explode(":", strval($total));
        if (count($total) !== 3) {
            return false;
        }

        $sum = $total[0] * 60 * 60 + $total[1] * 60 + $total[2];
        $average = $sum / (float) $count;
        $hours = sprintf("%02d", (floor($average / 3600)));
        $minutes = sprintf("%02d", floor(fmod($average, 3600) / 60));
        $seconds = sprintf("%02d", number_format(fmod(fmod($average, 3600), 60), (int) $rounding));
        return $hours . ":" . $minutes . ":" . $seconds;
    }

    public function filteredUsersSet($data)
    {
        $users_data = [];
        $users_names = [];
        foreach ($data as $data_item) {
            $data_row = [
                'date' => $data_item->Date,
                'name' => $data_item->Scr_name_real,
            ];
            $user_name = $data_item->Scr_name_real;
            array_push($users_names, $user_name);
            array_push($users_data, $data_row);
        }
        $users_set = array_unique($users_names);
        $filtered_user_set = [];
        foreach ($users_set as $user) {
            $is_valid = true;
            if (str_contains($user, "Pbx")) {
                $is_valid = false;
            };
            if (str_contains($user, "user")) {
                $is_valid = false;
            };
            if (str_contains($user, "Waiting")) {
                $is_valid = false;
            };
            if (str_contains($user, "VoiceMail")) {
                $is_valid = false;
            };
            if (str_contains($user, "wybierz")) {
                $is_valid = false;
            };
            if (str_contains($user, "Fax")) {
                $is_valid = false;
            };
            if (str_contains($user, "-")) {
                $is_valid = false;
            };
            if ($is_valid) {
                array_push($filtered_user_set, $user);
            };
        }
        return $filtered_user_set;
    }

    public function _group_by($array, $key)
    {
        $return = array();
        foreach ($array as $val) {
            $return[$val[$key]][] = $val["time"];
        }
        return $return;
    }

    public function prepareLineChart()
    {
        $colors = $this->colorArray();
        $data = DB::table('files')->orderBy('Id', 'ASC')->get();
        $filtered_user_set = $this->filteredUsersSet($data);
        $users_data = new stdClass();
        foreach ($filtered_user_set as $user_name) {
            //create an empty array for future purposes
            $users_data->$user_name = new stdClass();
        }

        foreach ($filtered_user_set as $user_name) {
            $user_data = [];
            foreach ($data as $record) {
                $data_row =
                    [
                    "date" => $record->Date,
                    "time" => $record->Duration,
                ];
                $record_user_name = $record->Scr_name_real;
                if ($user_name == $record_user_name) {
                    array_push($user_data, $data_row);
                }
            };

            $user_grouped_data = $this->_group_by($user_data, 'date');
            $user_formatted_data = new stdClass();
            foreach ($user_grouped_data as $date => $grouped_data_array) {
                $user_records_sum = $this->sumTime($grouped_data_array);
                $user_number_of_calls = count($grouped_data_array);
                $data_row = [
                    "avg_time" => $this->averageTime($user_records_sum, $user_number_of_calls),
                    "number_of_calls" => $user_number_of_calls,
                ];
                $user_formatted_data->$date = $data_row;
            }
            $users_data->$user_name = $user_formatted_data;
        }

        $chart_datasets = [];
        $chart_dataset_color_id = 0;
        $chart_labels = [];

        foreach ($users_data as $user_name => $user_data) {

            $chart_data_set = new stdClass();
            $chart_data_set->label = $user_name;
            $chart_data_set->borderColor = $colors[$chart_dataset_color_id];
            $chart_data_set->backgroundColor = $colors[$chart_dataset_color_id];

            $user_calls = $this->mapToLineChartDataSet($user_data);
            $date_labels = array_column($user_calls, 'x');
            $chart_labels = array_merge($chart_labels, $date_labels);
            $chart_data_set->data = $user_calls;
            array_push($chart_datasets, $chart_data_set);

            $chart_dataset_color_id++;
        }

        $chart = new stdClass();
        $chart->type = "line";
        $chart->data = new stdClass();
        $chart->data->datasets = $chart_datasets;
        $chart_labels_set = array_unique($chart_labels);
        usort($chart_labels_set, function ($a, $b) {
            return $this->compareDates($a, $b);
        });
        $chart->data->labels = array_values($chart_labels_set);

        return $chart;

    }

    public function compareDates($date_string_a, $date_string_b)
    {
        $date_a = new DateTime($date_string_a);
        $date_b = new DateTime($date_string_b);

        if ($date_a == $date_b) {
            return 0;
        }
        return $date_a < $date_b ? -1 : 1;
    }

    public function formatDate($date)
    {
        $arr = explode(".", $date);
        $year = $arr[2];
        $month = $arr[1];
        $day = $arr[0];
        return $year . "-" . $month . "-" . $day;
    }

    public function mapToLineChartDataSet($user_data_set)
    {
        $formatted_data = [];

        foreach ($user_data_set as $date => $value) {
            $data_row = new stdClass();
            $data_row->x = $this->formatDate($date);
            $data_row->y = $value["number_of_calls"];
            $data_row->t = $value["avg_time"];
            array_push($formatted_data, $data_row);
        }
        usort($formatted_data, function ($a, $b) {
            return $this->compareDates($a->x, $b->x);
        });
        return $formatted_data;
    }

    public function prepareBarChart()
    {
        $data = DB::table('files')->orderBy('Id', 'ASC')->get();

        $users_data = [];
        $users_names = [];
        foreach ($data as $record) {
            $record_data = [
                'Duration' => $record->Duration,
                'Name' => $record->Scr_name_real,
            ];
            $record_user_name = $record->Scr_name_real;
            array_push($users_names, $record_user_name);
            array_push($users_data, $record_data);
        }
        $filtered_user_set = $this->filteredUsersSet($data);

        $users_numbers_of_calls = array_count_values($users_names);
        $users_connections_length = new stdClass();
        $users_apperances = new stdClass();
        foreach ($filtered_user_set as $user_name) {
            $users_connections_length->$user_name = array();
        }
        foreach ($users_numbers_of_calls as $user_name => $number_of_calls) {
            $users_apperances->$user_name = $number_of_calls;
        }

        foreach ($filtered_user_set as $user_name) {
            $user_all_records = [];
            $user_how_many_records = 0;
            foreach ($data as $record) {
                $record_duration = $record->Duration;
                $record_user_name = $record->Scr_name_real;
                if ($user_name == $record_user_name) {
                    $user_how_many_records++;
                    array_push($user_all_records, $record_duration);
                }
            }
            $user_all_records_sum = $this->sumTime($user_all_records);
            $users_connections_length->$user_name = $this->averageTime($user_all_records_sum, $user_how_many_records);
        }

        $chart_data = [];
        foreach ($filtered_user_set as $user_name) {
            $how_many_calls = $users_apperances->$user_name;
            $total_time = $users_connections_length->$user_name;
            $data_row = [
                "x" => $user_name,
                "y" => $how_many_calls,
                "average_time" => $total_time,

            ];
            array_push($chart_data, $data_row);
        }
        $chart = new stdClass();
        $chart->type = "bar";
        $chart->data = new stdClass();
        $chart->data->labels = array_column($chart_data, 'x');
        $chart_datasets = [];
        $data_set = (object) [
            'backgroundColor' => $this->colorArray(),
            'data' => array_column($chart_data, 'y'),
        ];
        array_push($chart_datasets, $data_set);
        $chart->data->datasets = $chart_datasets;
        $chart->data->time = array_column($chart_data, 'average_time');

        return $chart;
    }
}
