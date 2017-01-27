<?php

namespace App\Http\Controllers;

use App\Contract;
use App\Department;
use App\Equipment;
use App\Expdetail;
use App\File;
use App\Iddoc;
use App\Library\CarbonHelper;
use App\Manufacturing;
use App\Material;
use App\Personnel;
use App\Photo;
use App\Site;
use App\Staff;
use App\Stock;
use App\Subdetail;
use App\Tag;
use App\Wage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Http\Requests;

class EkleController extends ManagementController
{
    //
    public function getIndex()
    {
        return view('landing.insert');
    }

    public function postAddEquipment(Request $request)
    {
        $eq = Equipment::create($request->all());

        return !empty($eq) ? response()->json('success', 200) : response()->json('error', 400);
    }

    public function postAddTag(Request $request)
    {
        $eq = Tag::create($request->all());

        return !empty($eq) ? response()->json('success', 200) : response()->json('error', 400);
    }

    public function postAddStaff(Request $request)
    {
        $st = Staff::create($request->all());
        return !empty($st) ? response()->json('success', 200) : response()->json('error', 400);
    }

    public function postAddDepartment(Request $request)
    {
        $dt = Department::create($request->all());
        return !empty($dt) ? response()->json($dt, 200) : response()->json('error', 400);
    }

    public function postAddMaterial(Request $request)
    {
        $mt = Material::create($request->all());
        return !empty($mt) ? response()->json($mt, 200) : response()->json('error', 400);
    }

    public
    function getRetrieveStocks()
    {
        $resp_arr = [];
        foreach (Stock::all() as $stock) {
            array_push($resp_arr, [
                'name' => $stock->name,
                'total' => $stock->total,
                'unit' => $stock->unit
            ]);
        }
        return response($resp_arr, 200);
    }

    public
    function postAddStock(Request $request)
    {
        Stock::create($request->all());
        return response('success', 200);
    }

    public
    function getRetrieveManufacturings()
    {
        $resp_arr = [];
        foreach (Manufacturing::all() as $man) {
            array_push($resp_arr, $man->name);
        }
        return response($resp_arr, 200);
    }

    public
    function postAddManufacturing(Request $request)
    {
        Manufacturing::create($request->all());
        return response('success', 200);
    }

    public function getRetrieveExpdetail()
    {
        $resp_arr = [];

        foreach (Expdetail::all() as $exp) {
            $group = 'GENEL GİDERLER';
            switch ($exp->group) {
                case(2):
                    $group = 'SÖZLEŞME GİDERLERİ';
                    break;
                case(3):
                    $group = 'SARF MALZEME GİDERLERİ';
                    break;
                case(4):
                    $group = 'İNŞAAT MALZEME GİDERLERİ';
                    break;
                default:
                    break;
            }
            array_push($resp_arr, [
                'name' => $exp->name,
                'group' => $group
            ]);
        }
        return response($resp_arr, 200);
    }

    public function postAddExpenditure(Request $request)
    {
        Expdetail::create($request->all());
        return response('success', 200);
    }

    public function postAddPersonnel(Request $request)
    {
        $this->validate($request, [
            'tck_no' => 'required | size:11',
            'name' => 'required',
            'contract' => 'required',
            'iddoc' => 'required'
        ]);
        $per_arr = $request->all();
        if (!empty($request->get("iban"))) {
            $per_arr["iban"] = preg_replace("/\\s+/ ", "", $request->get("iban"));
        }
        $personnel = Personnel::create($per_arr);

        if (isset($per_arr["wage"])) {
            $per_arr["wage"] = str_replace(",", ".", $request->get("wage"));
        } else {
            $per_arr["wage"] = 1.0;
        }

        $wage = Wage::create([
            'wage' => $per_arr["wage"],
            'since' => Carbon::parse($personnel->created_at)->toDateString()]);
        $wage->personnel()->associate($personnel);
        $wage->save();

        $directory = public_path() . '/uploads/' . uniqid(rand(), true);
        $contract_file = $this->uploadFile($request->file("contract"), $directory);
        $success = true;
        if (is_int($contract_file) && strpos('format', $contract_file) !== false) {
            Session::flash('flash_message_error', 'İzin verilmeyen dosya formatı. İlgili personel için dosyaları güncelleyin!');
            $success = false;
        }
        $contract = Contract::create([
            'contract_date' => $request->get('contract_date'),
            'contract_start_date' => $request->get('contract_start_date'),
            'contract_end_date' => $request->get('contract_end_date'),
        ]);
        if ($contract_file)
            $contract->file()->save($contract_file);
        else {
            Session::flash('flash_message_error', 'Dosya yükleme sırasında hata oluştu. İlgili personel için dosyaları güncelleyin!');
            $success = false;
        }

        if (!empty($request->file("documents"))) {
            foreach ($request->file("documents") as $file) {
                $db_file = $this->uploadFile($file, $directory);
                if (is_int($db_file) && strpos('format', $db_file) !== false) {
                    Session::flash('flash_message_error', 'İzin verilmeyen dosya formatı. İlgili personel için dosyaları güncelleyin!');
                    $success = false;
                }
                if ($db_file) {
                    $photo = Photo::create();
                    $photo->file()->save($db_file);
                    $personnel->photo()->save($photo);
                }
            }
        }
        $id_file = $this->uploadFile($request->file("iddoc"), $directory);
        if (is_int($id_file) && strpos('format', $id_file) !== false) {
            Session::flash('flash_message_error', 'İzin verilmeyen dosya formatı. İlgili personel için dosyaları güncelleyin!');
            $success = false;
        }
        $iddoc = new Iddoc();
        $iddoc->save();
        if ($id_file) {
            $iddoc->file()->save($id_file);

        } else {
            Session::flash('flash_message_error', 'Dosya yükleme sırasında hata oluştu. İlgili personel için dosyaları güncelleyin!');
            $success = false;
        }

        $personnel->contract()->save($contract);
        $personnel->iddoc()->save($iddoc);
        (new Site)->personnel()->save($personnel);
        if (isset($request->exit_date) && !empty($request->exit_date)) {
            $contract = $personnel->contract;
            $contract->exit_date = CarbonHelper::getMySQLDate($request->exit_date);
            $contract->save();
        }
        if ($success)
            Session::flash('flash_message', 'Personel eklendi');
        return redirect()->back();
    }

    public function postAddSubcontractor(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'address' => 'required',
            'city_id' => 'required',
            'official' => 'required',
            'title' => 'required',
            'mobile_code_id' => 'required',
            'mobile' => 'required | size:7',
            'email' => 'email',
            'tax_number' => 'required'
        ]);
        $sub_arr = $request->all();
        foreach ($sub_arr as $v => $k) {
            if (empty($k)) {
                unset($sub_arr[$v]);
            }
        }

        $subcontractor = Subdetail::create($sub_arr);

        Session::flash('flash_message', "Alt yüklenici ($subcontractor->name) kaydı oluşturuldu");

        return redirect()->back();

    }

    public function getRetrieveStaffs()
    {
        $staffs = Staff::allStaff();
        $staff_arr = [];

        foreach ($staffs as $staff) {
            array_push($staff_arr, ['name' => $staff->staff, 'department' => \App\Library\TurkishChar::tr_up($staff->department->department)]);
        }

        return response($staff_arr, 200);
    }

    public function getRetrieveDepartments()
    {
        return response(Department::all()->toArray(), 200);
    }

    public function getRetrieveMaterials()
    {
        return response(Material::all()->toArray(), 200);
    }

    public function getRetrieveEquipments()
    {
        return response(Equipment::all()->toArray(), 200);
    }

    public function getRetrieveTags()
    {
        return response(Tag::all()->toArray(), 200);
    }

    public
    function postAddSubmaterial(Request $request)
    {

        $material = Material::find($request->get("material"));
        $names = preg_replace('/(\s+;\s|\s+;|;\s+)/i', ';', $request->get("name"));
        $names = explode(';', $names);

        foreach ($names as $name) {
            $submaterial = $request->get("is_sm") == 1 ? new Submaterial() : new Feature();
            $submaterial->name = TurkishChar::tr_up($name);
            $submaterial->material()->associate($material);
            $submaterial->save();
        }
        Session::flash('flash_message', 'Bağlantılı malzeme eklendi');
        return redirect()->back();
    }

    private function uploadFile($file, $directory = null)
    {
        if (empty($directory)) {
            $directory = public_path() . '/uploads/' . uniqid(rand(), true);
        }
        $filename = $file->getClientOriginalName();

        $mime = $file->getMimeType();
        $UPLOADABLE_FILE_TYPES = ['application/pdf', 'image/png', 'image/jpeg'];
        if (in_array($mime, $UPLOADABLE_FILE_TYPES)) {
            if ($file->move($directory, $filename))

                return File::create([
                    "name" => $filename,
                    "path" => $directory
                ]);
        }

        return null;
    }

}
