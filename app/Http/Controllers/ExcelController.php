<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class ExcelController extends Controller
{

    /**

     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View

     */
    public function index()
    {
        $data = DB::table('files')->orderBy('Id', 'ASC')->paginate(20);
        return view('home', compact('data'));

    }

    public function search(Request $request)
    {
        $scr_name_real_checkbox = $request->query("scr_name_real_checkbox");
        $scr_name_real_input = $request->query("scr_name_real_input");

        $external_partner_checkbox = $request->query("external_partner_checkbox");
        $external_partner_input = $request->query("external_partner_input");

        $connection_no_checkbox = $request->query("connection_no_checkbox");
        $connection_no_input = $request->query("connection_no_input");

        $data = DB::table('files');
        if ($scr_name_real_checkbox == "true") {
            $data->where('Scr_name_real', 'like', '%' . $scr_name_real_input . '%');
        }
        if ($external_partner_checkbox == "true") {
            $data->where('External_partner', 'like', '%' . $external_partner_input . '%');
        }
        if ($connection_no_checkbox == "true") {
            $data->where('Connection_no', 'like', '%' . $connection_no_input . '%');
        }

        $data = $data->paginate(20)->appends(request()->query());
        return view('home', compact('data', 'scr_name_real_checkbox', 'scr_name_real_input', 'external_partner_checkbox', 'external_partner_input', 'connection_no_checkbox', 'connection_no_input'));
    }

    /**

     * @param Request $request

     * @return \Illuminate\Http\RedirectResponse

     * @throws \Illuminate\Validation\ValidationException

     * @throws \PhpOffice\PhpSpreadsheet\Exception

     */

    public function importData(Request $request)
    {

        $this->validate($request, [

            'uploaded_file' => 'required|file:csv,xls,xlsx',

        ]);

        $the_file = $request->file('uploaded_file');

        try {

            $spreadsheet = IOFactory::load($the_file->getRealPath());

            $sheet = $spreadsheet->getActiveSheet();

            $row_limit = $sheet->getHighestDataRow();

            $column_limit = $sheet->getHighestDataColumn();

            $row_range = range(2, $row_limit);

            $column_range = range('S', $column_limit);

            $startcount = 2;

            $data = array();

            foreach ($row_range as $row) {
                $values = [
                    'Date' => $sheet->getCell('A' . $row)->getValue(),
                    'Time' => $sheet->getCell('B' . $row)->getValue(),
                    'Duration' => $sheet->getCell('C' . $row)->getValue(),
                    'LCRNo' => $sheet->getCell('D' . $row)->getValue(),
                    'External_partner' => $sheet->getCell('E' . $row)->getValue(),
                    'External_name' => $sheet->getCell('F' . $row)->getValue(),
                    'Scr_no_invoice' => $sheet->getCell('G' . $row)->getValue(),
                    'Scr_name_invoice' => $sheet->getCell('H' . $row)->getValue(),
                    'Scr_no_real' => $sheet->getCell('I' . $row)->getValue(),
                    'Scr_name_real' => $sheet->getCell('J' . $row)->getValue(),
                    'Connection_no' => $sheet->getCell('K' . $row)->getValue(),
                    'Charges' => $sheet->getCell('L' . $row)->getValue(),
                    'Direction' => $sheet->getCell('M' . $row)->getValue(),
                    'Bill_type' => $sheet->getCell('N' . $row)->getValue(),
                    'Call_type' => $sheet->getCell('O' . $row)->getValue(),
                    'Proj' => $sheet->getCell('P' . $row)->getValue(),
                    'HotId' => $sheet->getCell('Q' . $row)->getValue(),
                ];

                $files = DB::table('files');
                foreach ($values as $key => $value) {
                    $files->where($key, '=', $value);
                }
                $result = $files->count();

                if ($result == 0) {
                    $data[] = $values;
                }

                $startcount++;

            }

            foreach (array_chunk($data, 1000) as $t) {
                DB::table('files')->insert($t);
            }

        } catch (Exception $e) {

            $error_code = $e->errorInfo[1];

            return back()->withErrors('There was a problem uploading the data!');

        }

        return back()->withSuccess('Great! Data has been successfully uploaded.');

    }

    /**

     * @param $customer_data

     */

    public function ExportExcel($customer_data)
    {

        ini_set('max_execution_time', 0);

        ini_set('memory_limit', '4000M');

        try {

            $spreadSheet = new Spreadsheet();

            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);

            $spreadSheet->getActiveSheet()->fromArray($customer_data);

            $Excel_writer = new Xls($spreadSheet);

            header('Content-Type: application/vnd.ms-excel');

            header('Content-Disposition: attachment;filename="ExportedData.xls"');

            header('Cache-Control: max-age=0');

            ob_end_clean();

            $Excel_writer->save('php://output');

            exit();

        } catch (Exception $e) {

            return;

        }

    }

    public function exportData()
    {

        $data = DB::table('files')->orderBy('Id', 'ASC')->get();
        $data_array = [];
        foreach ($data as $data_item) {

            $data_row = '';
            $data_row = [
                'Date' => $data_item->Date,
                'Time' => $data_item->Time,
                'Duration' => $data_item->Duration,
                'LCRNo' => $data_item->LCRNo,
                'External_partner' => $data_item->External_partner,
                'External_name' => $data_item->External_name,
                'Scr_no_invoice' => $data_item->Scr_no_invoice,
                'Scr_name_invoice' => $data_item->Scr_name_invoice,
                'Scr_no_real' => $data_item->Scr_no_real,
                'Scr_name_real' => $data_item->Scr_name_real,
                'Connection_no' => $data_item->Connection_no,
                'Charges' => $data_item->Charges,
                'Direction' => $data_item->Direction,
                'Bill_type' => $data_item->Bill_type,
                'Call_type' => $data_item->Call_type,
                'Proj' => $data_item->Proj,
                'HotId' => $data_item->HotId,
            ];
            array_push($data_array, $data_row);
        }

        $this->ExportExcel($data_array);

    }

}
