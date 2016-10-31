<?php

namespace App\Http\Controllers;

use App\Department;
use App\Equipment;
use App\Expdetail;
use App\Feature;
use App\Group;
use App\Iddoc;
use App\Library\CarbonHelper;
use App\Library\TurkishChar;
use App\Manufacturing;
use App\Material;
use App\Personnel;
use App\Photo;
use App\Salary;
use App\Staff;
use App\Stock;
use App\Subcontractor;
use App\Subdetail;
use App\Submaterial;
use App\Tag;
use App\Wage;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Session;

class GuncelleController extends ManagementController
{
    //

    public function getIndex()
    {
        return view('landing.modify');
    }

    public function postDelPersonnel(Request $request)
    {
        Personnel::find($request->get('id'))->delete();
        return response('success', 200);
    }

    public function postUndoPersonnel(Request $request)
    {
        Personnel::onlyTrashed()->where('id', '=', $request->get('id'))->restore();
        return response('success', 200);
    }

    public function postModifyDepartment(Request $request)
    {
        $eq = Department::find($request->get("pk"));
        return $this->modifyEntry($eq, $request);
    }

    public function postModifyMaterial(Request $request)
    {
        $eq = Material::find($request->get("pk"));
        return $this->modifyEntry($eq, $request);
    }

    public function postModifyEquipment(Request $request)
    {
        $eq = Equipment::find($request->get("pk"));
        return $this->modifyEntry($eq, $request);
    }

    public function postModifyTag(Request $request)
    {
        $eq = Tag::find($request->get("pk"));
        return $this->modifyEntry($eq, $request);
    }

    public function postModifyStaff(Request $request)
    {
        $eq = Staff::find($request->get("pk"));
        return $this->modifyEntry($eq, $request);
    }

    public function postModifyManufacturing(Request $request)
    {
        $eq = Manufacturing::find($request->get("pk"));
        return $this->modifyEntry($eq, $request);
    }

    public function postModifySubmaterial(Request $request)
    {
        $eq = Submaterial::find($request->get("pk"));
        return $this->modifyEntry($eq, $request);
    }

    public function postModifyFeature(Request $request)
    {
        $eq = Feature::find($request->get("pk"));
        return $this->modifyEntry($eq, $request);
    }

    public function postModifyExpdetail(Request $request)
    {
        $eq = Expdetail::find($request->get("pk"));
        return $this->modifyEntry($eq, $request);
    }

    public function postModifyGroup(Request $request)
    {
        $eq = Group::find($request->get("pk"));
        return $this->modifyEntry($eq, $request);
    }

    public function postModifyStock(Request $request)
    {
        $eq = Stock::find($request->get("pk"));
        if (!empty($eq)) {
            if ($request->name == 'stockName') {
                if (empty($request->get("value"))) {
                    $eq->delete();
                } else {
                    $eq->name = TurkishChar::tr_up($request->get("value"));
                    $eq->save();
                }
            } elseif ($request->name == 'stockUnit') {
                $eq->unit = TurkishChar::tr_up($request->get("value"));
                $eq->save();
            } elseif ($request->name == 'stockTotal') {
                $eq->total = $request->get("value");
                $eq->save();
            }
        }
        return response('success', 200);
    }

    public function postDelSubcontractor(Request $request)
    {
        Subdetail::find($request->get('userDeleteIn'))->delete();
        Session::flash('flash_message', 'Alt yüklenici silindi');
        return redirect()->back();
    }

    private
    function modifyEntry($eq, $request)
    {
        if (!empty($eq)) {
            if (empty($request->get("value"))) {
                $eq->delete();
            } else {
                $eq->name = TurkishChar::tr_up($request->get("value"));
                $eq->save();
            }
        }
        return response(200);
    }

    public function getPersonelDuzenle(Personnel $personnel)
    {
        return view('landing.personnel', compact('personnel'));
    }

    public function postModifyPersonnel(Request $request)
    {
        $per_arr = $request->all();
        unset($per_arr["_token"]);
        unset($per_arr["exit_date"]);
        unset($per_arr["wage"]);
        unset($per_arr["id"]);
        unset($per_arr["contract"]);
        unset($per_arr["iddoc"]);
        unset($per_arr["documents"]);
        if (!empty($request->get("iban"))) {
            $per_arr["iban"] = preg_replace("/\\s+/ ", "", $request->get("iban"));
        }

        $per = Personnel::find($request->get("id"));
        if (empty($per->contract)) {
            $contract = new Contract();
            $contract->save();
            $per->contract()->save($contract);
        }
        if (isset($request->exit_date) && !empty($request->exit_date)) {
            $contract = $per->contract;
            $contract->exit_date = CarbonHelper::getMySQLDate($request->exit_date);
            $contract->save();
        }
        foreach ($per_arr as $k => $v) {
            $per->$k = $v;
        }
        $per->save();

        $directory = public_path() . '/uploads/' . uniqid(rand(), true);
        $success = true;
        if ($request->hasFile("contract")) {
            $contract_file = $this->uploadFile($request->file("contract"), $directory);
            if (is_int($contract_file) && strpos('format', $contract_file) !== false) {
                Session::flash('flash_message_error', 'İzin verilmeyen dosya formatı. İlgili personel için dosyaları güncelleyin!');
                $success = false;
            }
            $contract = $per->contract;
            $contract->file()->save($contract_file);
        }

        if ($request->hasFile("documents")) {
            foreach ($request->file("documents") as $file) {

                $db_file = $this->uploadFile($file, $directory);
                if (is_int($db_file) && strpos('format', $db_file) !== false) {
                    Session::flash('flash_message_error', 'İzin verilmeyen dosya formatı. İlgili personel için dosyaları güncelleyin!');
                    $success = false;
                }
                if ($db_file) {
                    $photo = Photo::create();
                    $photo->file()->save($db_file);
                    $per->photo()->save($photo);
                }
            }
        }

        if ($request->hasFile("iddoc")) {
            $id_file = $request->file('iddoc');
            $db_file = $this->uploadFile($id_file, $directory);
            if (is_int($db_file) && strpos('format', $db_file) !== false) {
                Session::flash('flash_message_error', 'İzin verilmeyen dosya formatı. İlgili personel için dosyaları güncelleyin!');
                $success = false;
            }
            if ($db_file) {
                if (!empty($per->iddoc()->first())) {
                    $iddoc = $per->iddoc()->first();
                } else {
                    $iddoc = Iddoc::create();
                    $iddoc->personnel()->associate($per);
                    $iddoc->save();
                }
                $iddoc->file()->save($db_file);
            }

        }
        if ($success)
            Session::flash('flash_message', 'Personel güncellendi');
        return redirect()->back();

    }

    public function postDeletePersonnelFiles(Request $request)
    {
        Photo::find($request->get("fileid"))->delete();
        return response('success', 200);
    }

    public function postRetrieveWages(Request $request)
    {
        $personnel = Personnel::find($request->pid);
        $resp_arr = [];
        foreach ($personnel->wage()->orderBy('since', 'DESC')->get() as $wage) {
            array_push($resp_arr, [
                'id' => $wage->id,
                'since' => CarbonHelper::getTurkishDate($wage->since),
                'wage' => str_replace('.', ',', $wage->wage)
            ]);
        }
        return response($resp_arr, 200);
    }

    public function postAddWage(Request $request)
    {
        $personnel = Personnel::find($request->pid);
        $wage = new Wage();
        $wage->wage = $request->wage;
        $wage->since = CarbonHelper::getMySQLDate($request->since);
        $wage->personnel()->associate($personnel);
        $wage->save();
        return response('success', 200);

    }

    public function postDelWage(Request $request)
    {
        Wage::find($request->id)->delete();
        return response('success', 200);
    }

    public function postRetrieveSalaries(Request $request)
    {
        $personnel = Personnel::find($request->pid);
        $resp_arr = [];
        foreach ($personnel->salary()->orderBy('since', 'DESC')->get() as $salary) {
            array_push($resp_arr, [
                'id' => $salary->id,
                'since' => CarbonHelper::getTurkishDate($salary->since),
                'salary' => str_replace('.', ',', $salary->amount)
            ]);
        }
        return response($resp_arr, 200);
    }

    public function postAddSalary(Request $request)
    {
        $personnel = Personnel::find($request->pid);
        $salary = new Salary();
        $salary->amount = $request->amount;
        $salary->since = CarbonHelper::getMySQLDate($request->since);
        $salary->personnel()->associate($personnel);
        $salary->save();
        return response('success', 200);

    }

    public function postDelSalary(Request $request)
    {
        Salary::find($request->id)->delete();
        return response('success', 200);
    }

    public function postUpdateSubcontractor(Request $request)
    {
        $subcontractor = Subcontractor::find($request->id);
        $personnel = Personnel::find($request->pid);
        $subcontractor->personnel()->save($personnel);
        return response('success', 200);
    }


    public function getAltyukleniciDuzenle(Subdetail $subdetail)
    {
        return view('landing.subcontractor', compact('subdetail'));
    }

    public function postModifySubcontractor(Request $request)
    {
        $sub_arr = $request->all();
        $sub = Subdetail::find($request->get('id'));
        unset($sub_arr["_token"]);
        unset($sub_arr["id"]);
        foreach ($sub_arr as $k => $v) {
            $sub->$k = $v;
        }
        $sub->save();

        Session::flash('flash_message', 'Alt yüklenici güncellendi');
        return redirect()->back();

    }

}
