<?php

namespace App\Http\Controllers;

use App\Account;
use App\Contract;
use App\Cost;
use App\Demand;
use App\Expense;
use App\Fee;
use App\File;
use App\Inmaterial;
use App\Library\CarbonHelper;
use App\Library\TurkishChar;
use App\Library\Weather;
use App\Manufacturing;
use App\Material;
use App\Meal;
use App\Module;
use App\Outmaterial;
use App\Overtime;
use App\Personnel;
use App\Photo;
use App\Pwunit;
use App\Receipt;
use App\Report;
use App\Shift;
use App\Site;
use App\Http\Requests;
use App\Subcontractor;
use App\Swunit;
use App\Wage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


class TekilController extends Controller
{
    //


    public function getIndex(Site $site, Module $modules)
    {
        return view('tekil/main', compact('site', 'modules'));
    }

    public function getGunlukRapor(Site $site, Module $modules)
    {
        $report = new Report;

        if (empty($report->where('created_at', Carbon::now()->toDateString())->where('site_id', $site->id)->first())) {
            $report->site_id = $site->id;
            $report->save();

        } else {
            $report = $report->where('created_at', Carbon::now()->toDateString())->where('site_id', $site->id)->first();
        }
        if (session()->has('report')) {
            $report = session()->get('report');
        }
        $yesterdays_report = $site->report()->where('created_at', Carbon::yesterday()->toDateString())->first();
        if (is_null($yesterdays_report->weather)) {
            $wt = new Weather(1);
            $yesterdays_report->weather = $wt->getDescription();
            $yesterdays_report->temp_min = $wt->getMin();
            $yesterdays_report->temp_max = $wt->getMax();
            $yesterdays_report->wind = $wt->getWind();
            $yesterdays_report->degree = $wt->getDirection();
            $yesterdays_report->save();
        }

        return view('tekil/daily', compact('site', 'modules', 'report'));
    }


    public function getMalzemeTalep(Site $site, Module $modules)
    {
        if (session()->has("material_array")) {
            $material_array = session("material_array");
            return view('tekil/demand', compact('site', 'modules', 'material_array'));
        } else
            return view('tekil/demand', compact('site', 'modules'));
    }

    public function postAddMaterials(Request $request)
    {
        $material_array = $request->get("materials");
        return redirect()->back()->with("material_array", $material_array);
    }

    public function postDemandMaterials(Request $request, Site $site)
    {
        $demand = new Demand;
        $demand->demand = $site->job_name . "_" . Carbon::now('Europe/Istanbul')->format('mdY');
        $demand->save();
        foreach ($request->get("materials") as $mat) {
            $my_mat = Material::find($mat);
            $my_mat->demands()->attach($demand->id, ["quantity" => $request->get("quantity")[$mat - 1],
                "unit" => $request->get("unit")[$mat - 1],]);
        }
        Session::flash('flash_message', 'Malzeme talep formu oluşturuldu');
        return redirect()->back();
    }

    public function postSelectDate(Request $request, Site $site)
    {
        $data = ["date" => $request->get("date")];
        $report_date = $data["date"];
        $sql_date = CarbonHelper::getMySQLDate($report_date);

        if (!empty(Report::where('created_at', $sql_date)->where('site_id', $site->id)->first())) {
            $report = Report::where('created_at', $sql_date)->where('site_id', $site->id)->first();
            return redirect()->back()->with(["data" => $data, "report" => $report]);

        } else {
            $report = Report::where('created_at', Carbon::now()->toDateString())->where('site_id', $site->id)->first();
            Session::flash('flash_message_error', 'Belirtilen tarihe ait rapor bulunamadı');
            return redirect()->back()->with(["report" => $report]);
        }

    }

    public function postSaveStaff(Site $site, Request $request)
    {
//        Main contractor
        $personnel_arr = $this->getDistinct($request->get("staffs"));

        $report = Report::find($request->get("report_id"));

        $report->staff()->detach();
        for ($i = 0; $i < sizeof($personnel_arr); $i++) {
            $per = Personnel::find($personnel_arr[$i]);
            $this->createShiftsMealsFromPerRepSite($per, $site, $report);

            /*
             * quantity nasıl bulunur
             * Bu personelin staff'ı ilgili raporda ekliyse onun quantity'si alınır
             * yoksa quantity 1 olur
             */
            $quantity = 1;
            if (!is_null($report->staff()->where('staff_id', $per->staff_id)->first())) {
                $quantity = (int)$report->staff()->where('staff_id', $per->staff_id)->first()->pivot->quantity + 1;
            }
            $report->staff()->detach($per->staff_id);
            $report->staff()->attach($per->staff_id, ["quantity" => $quantity]);
        }
        Session::flash('flash_message', 'İlgili personel eklendi');
        return redirect()->back();
    }

    public function postDetachStaff(Site $site, Request $request)
    {
//        Main contractor
        $per = Personnel::find($request->get("staffid"));
        $report = Report::find($request->get("report_id"));
        $quantity = (int)$report->staff($per->staff_id)->first()->pivot->quantity - 1;
        $report->staff()->detach($per->staff_id);
        if ($quantity > 0) {
            $report->staff()->attach($per->staff_id, ["quantity" => $quantity]);
        }
        $this->deleteShiftsMealsFromPerRepSite($per, $site, $report);
        return response('success', 200);
    }

    public function postAddManagementStaffs(Site $site, Request $request)
    {
        $report = Report::find($request->get("report_id"));
        $main_staff_arr = $request->get("main-staffs");
        $q_arr = $request->get("main-staff-quantity");
        $exists = false;
        for ($i = 0; $i < sizeof($main_staff_arr); $i++) {
            switch ($main_staff_arr[$i]) {
                case 0:
                    $report->management_staff = $q_arr[$i];
                    $exists = true;
                    break;
                case 1:
                    $report->employer_staff = $q_arr[$i];
                    $exists = true;
                    break;
                case 2:
                    $report->building_control_staff = $q_arr[$i];
                    $exists = true;
                    break;
                case 3:
                    $report->isg_staff = $q_arr[$i];
                    $exists = true;
                    break;
            }
        }
        if ($exists) {
            $report->save();
        }
        Session::flash('flash_message', 'Personel kaydı başarılı');
        return redirect()->back();
    }

    public function postDeleteManagementStaff(Request $request)
    {
        $report = Report::find($request->get("reportid"));
        $column_name = $request->get("column");
        $report->$column_name = "0";
        $report->save();
        return response()->json('success', 200);
    }

    public function postSaveSubcontractorStaff(Site $site, Request $request)
    {
//        Daily report subcontractor staff
        $report = Report::find($request->get("report_id"));
//        Get subcontractor personnel
        $personnel_arr = $this->getDistinct($request->get("substaffs"));

        for ($i = 0; $i < sizeof($personnel_arr); $i++) {
            $per = Personnel::find($personnel_arr[$i]);
            $this->createShiftsMealsFromPerRepSite($per, $site, $report);
            $subcontractor = $per->personalize;
            $quantity = 1;

            if ($report->hasSubstaff($per->staff_id, $subcontractor->id)) {
                $quantity = (int)$report->substaff()->where('substaff_id', $per->staff_id)->where('subcontractor_id', $subcontractor->id)
                        ->first()->pivot->quantity + 1;
                $report->detachSubstaff($per->staff_id, $subcontractor->id, $report->id);
            }
            $report->substaff()->attach($per->staff_id, [
                "quantity" => $quantity,
                "subcontractor_id" => $subcontractor->id]);
        }
        Session::flash('flash_message', 'Alt yüklenici personel kaydı başarılı');
        return redirect()->back();
    }

    public function postDeleteReportSubcontractor(Site $site, Request $request)
    {
        $per = Personnel::find($request->get("staffid"));
        $report = Report::find($request->get("report_id"));
        $subcontractor = Subcontractor::find($request->get("subcontractorid"));
        $quantity = (int)$report->substaff()->where('substaff_id', $per->staff_id)->where('subcontractor_id', $subcontractor->id)
                ->first()->pivot->quantity - 1;
        $report->detachSubstaff($per->staff_id, $subcontractor->id, $report->id);
        if ($quantity > 0) {
            $report->substaff()->attach($per->staff_id, [
                "quantity" => $quantity,
                "subcontractor_id" => $subcontractor->id]);
        }
        $this->deleteShiftsMealsFromPerRepSite($per, $site, $report);
        return response('success', 200);
    }


    public function postSaveEquipment(Request $request)
    {

        $eq_arr = $request->get("equipments");
        $present_arr = $request->get("equipment-present");
        $working_arr = $request->get("equipment-working");
        $broken_arr = $request->get("equipment-broken");

        $report = Report::find($request->get("report_id"));
        $report->equipment()->detach();
        for ($i = 0; $i < sizeof($eq_arr); $i++) {
            if (!strlen($present_arr[$i]) == 0 || !strlen($working_arr[$i]) == 0 || !strlen($broken_arr[$i]) == 0) {
                $report->equipment()->attach($eq_arr[$i], [
                    "present" => $present_arr[$i],
                    "working" => $working_arr[$i],
                    "broken" => $broken_arr[$i],
                ]);
                Session::flash('flash_message', 'İlgili ekipman eklendi');
            }
        }
        return redirect()->back();
    }

    public function postDetachEquipment(Request $request)
    {
//        Main contractor
        $equipment_id = $request->get("equipmentid");
        $report = Report::find($request->get("report_id"));
        $report->equipment()->detach($equipment_id);
        return response()->json('success', 200);
    }


    public function postSaveWorkDone(Request $request)
    {
        $report = Report::find($request->get("report_id"));
        $subcontractor_ids = $request->get("subcontractors");
        $i = 0;
        if (!is_null($subcontractor_ids)) {
            foreach ($subcontractor_ids as $subcontractor_id) {
                $swunit = new Swunit();

                if (is_null($report->swunit()
                    ->where('subcontractor_id', $subcontractor_id)->first())
                ) {

                    $swunit = $swunit->create([
                        "subcontractor_id" => $subcontractor_id,
                        "report_id" => $report->id,
                        "quantity" => $request->get("subcontractor_quantity")[$i],
                        "unit" => $request->get("subcontractor_unit")[$i],
                        "works_done" => $request->get("subcontractor_work_done")[$i],
                        "planned" => $request->get("subcontractor_planned")[$i],
                        "done" => $request->get("subcontractor_done")[$i],
                    ]);
                } else {
                    $swunit = $report->swunit()
                        ->where('subcontractor_id', $subcontractor_id)->first();
                    $swunit->quantity = $request->get("subcontractor_quantity")[$i];
                    $swunit->unit = $request->get("subcontractor_unit")[$i];
                    $swunit->works_done = $request->get("subcontractor_work_done")[$i];
                    $swunit->planned = $request->get("subcontractor_planned")[$i];
                    $swunit->done = $request->get("subcontractor_done")[$i];
                    $swunit->save();
                }
                $i++;
                Session::flash('flash_message', 'İlgili çalışan birim eklendi');
            }
        }


        $staff_ids = $request->get("staffs");
        $i = 0;
        if (!is_null($staff_ids)) {
            foreach ($staff_ids as $staff_id) {
                $pwunit = new Pwunit();

                if (is_null($report->pwunit()->
                where('staff_id', $staff_id)->first())
                ) {

                    $pwunit = $pwunit->create([
                        "staff_id" => $staff_id,
                        "report_id" => $report->id,
                        "quantity" => $request->get("staff_quantity")[$i],
                        "unit" => $request->get("staff_unit")[$i],
                        "works_done" => $request->get("staff_work_done")[$i],
                        "planned" => $request->get("staff_planned")[$i],
                        "done" => $request->get("staff_done")[$i],
                    ]);
                } else {
                    $pwunit = $report->pwunit()->
                    where('staff_id', $staff_id)->first();
                    $pwunit->quantity = $request->get("staff_quantity")[$i];
                    $pwunit->unit = $request->get("staff_unit")[$i];
                    $pwunit->works_done = $request->get("staff_work_done")[$i];
                    $pwunit->planned = $request->get("staff_planned")[$i];
                    $pwunit->done = $request->get("staff_done")[$i];
                    $pwunit->save();
                }
                $i++;
                Session::flash('flash_message', 'İlgili çalışan birim eklendi');
            }
        }

        return redirect()->back();
    }

    public function postDeleteSwunit(Request $request)
    {
        Swunit::destroy($request->get('swid'));
        return response()->json('success', 200);
    }

    public function postDeletePwunit(Request $request)
    {
        Pwunit::destroy($request->get('pwid'));
        return response()->json('success', 200);
    }

    public function postSaveIncomingMaterial(Request $request)
    {
        $in_arr = $request->get("inmaterials");

        $report = Report::find($request->get("report_id"));
        $report_id = $report->id;
        $i = 0;
        foreach ($report->inmaterial()->get() as $inmat) {
            $inmat->delete();
        }
        foreach ($in_arr as $mat_id) {
            $inmaterial = Inmaterial::firstOrNew([
                "material_id" => $mat_id,
                "report_id" => $report_id]);
            $inmaterial->quantity = str_replace(",", ".", str_replace(".", "", $request->get("inmaterial-quantity")[$i]));
            $inmaterial->unit = $request->get("inmaterial-unit")[$i];
            $inmaterial->coming_from = $request->get("inmaterial-from")[$i];
            $inmaterial->explanation = $request->get("inmaterial-explanation")[$i];
            $inmaterial->save();
            $i++;
            Session::flash('flash_message', 'Gelen malzeme tablosu güncellendi');
        }


        return redirect()->back();
    }

    public function postDeleteInmaterial(Request $request)
    {
        Inmaterial::find($request->get("inmaterialid"))->delete();
        return response()->json('success', 200);
    }

    public function postSaveOutgoingMaterial(Request $request)
    {
        $in_arr = $request->get("outmaterials");

        $report = Report::find($request->get("report_id"));
        $report_id = $report->id;
        $i = 0;
        foreach ($report->outmaterial()->get() as $inmat) {
            $inmat->delete();
        }
        foreach ($in_arr as $mat_id) {
            $outmaterial = Outmaterial::firstOrNew(["material_id" => $mat_id,
                "report_id" => $report_id]);

            $outmaterial->quantity = str_replace(",", ".", str_replace(".", "", $request->get("outmaterial-quantity")[$i]));
            $outmaterial->unit = $request->get("outmaterial-unit")[$i];
            $outmaterial->coming_from = $request->get("outmaterial-from")[$i];
            $outmaterial->explanation = $request->get("outmaterial-explanation")[$i];
            $outmaterial->save();
            $i++;
            Session::flash('flash_message', 'Giden malzeme tablosu güncellendi');
        }


        return redirect()->back();
    }

    public function postDeleteOutmaterial(Request $request)
    {
        Outmaterial::find($request->get("outmaterialid"))->delete();
        return response()->json('success', 200);
    }

    public function postSelectIsWorking(Request $request)
    {
        $report = Report::find($request->get("report_id"));
        $report->is_working = $request->get("is_working");
        $report->save();
        Session::flash('flash_message', 'Şantiye çalışma durumu güncellendi');
        return redirect()->back();
    }

    public function postSaveFiles(Request $request)
    {
        $report = Report::find($request->get("report_id"));
        $db_file = $this->uploadFile($request->file("file"));

        if ($db_file) {
            if ($request->get("type") == 0) {
                $photo = Photo::create();
                $report->photo()->save($photo);
                $photo->file()->save($db_file);
            } else {
                $receipt = Receipt::create();
                $report->receipt()->save($receipt);
                $receipt->file()->save($db_file);
            }
            return response()->json(['success' => 200,
                'id' => $db_file->id,
                'rid' => $report->id], 200);
        } else {
            return response()->json('error', 400);
        }

    }

    public function postDeleteFiles(Request $request)
    {
        $db_file = File::find($request->get("fileid"));

        $op_success = $db_file->fileable()->delete() && unlink($db_file->path . DIRECTORY_SEPARATOR . $db_file->name) && $db_file->delete();
        if ($op_success) {
            return response()->json('success', 200);
        } else {
            return response()->json('error', 400);
        }
    }

    public function patchLockReport(Request $request)
    {
        $report = Report::find($request->get("report_id"));
        $report->is_locked = $request->get("lock");
        $report->save();
        return redirect()->back();
    }

    public function postSaveShiftsMeals(Site $site, Request $request)
    {
        $personnel = [];
        $k = 0;
        foreach ($request->get("personnel") as $per) {
            if ($request->get("overtime_arr")[$k] == 0) {
                Session::flash('flash_message_error', 'İlgili personel için mesai girmelisiniz');
                return redirect()->back();
            }
            if (in_array($per, $personnel)) {
                Session::flash('flash_message_error', 'Aynı personeli iki kere ekleyemezsiniz');
                return redirect()->back();
            } else {
                array_push($personnel, $per);
            }
            $k++;
        }
        $overtime_ids = $request->get("overtimes"); //overtime ids from overtimes table
        $overtimes = $request->get("overtime_arr"); //overtimes
        $meals = $request->get("meals_arr");
        $report_id = $request->get("report_id");

        for ($i = 0; $i < sizeof($personnel); $i++) {
            $my_arr = ['personnel_id' => $personnel[$i],
                'report_id' => $report_id,
                'site_id' => $site->id];

            $overtime = Overtime::find($overtime_ids[$i]);
            $shift = Shift::firstOrNew($my_arr);
            $shift->hour = $overtimes[$i];
            $shift->overtime()->associate($overtime);
            $shift->save();

            $meal = Meal::firstOrNew($my_arr);
            $meal->meal = $meals[$i];
            $meal->save();

        }

        Session::flash('flash_message', 'Puantaj ve Yemek tablosu güncellendi');
        return redirect()->back();
    }

//    END OF GUNLUK RAPOR PAGE


    //  TAŞERON CARİ HESAP PAGE AND RELATED OPERATIONS
    public function getAltYukleniciCariHesap(Site $site, Module $modules)
    {
        return view('tekil/subcontractor-account', compact('site', 'modules'));
    }

    public function getAltYukleniciDuzenle(Site $site, Module $modules, $id)
    {
        if (is_null($site->subcontractor()->find($id))) {
            return redirect()->back();
        }
        $subcontractor = Subcontractor::find($id);
        $costs = $subcontractor->cost()->additionalCosts(15);
        return view('tekil/subcontractor-edit', compact('subcontractor', 'site', 'modules', 'costs'));
    }

    public function postAddSubcontractor(Request $request, Site $site)
    {
        $subcontractor_ids = $request->get("subcontractors");
        foreach ($subcontractor_ids as $subcontractor_id) {
            if (!is_null($site->subcontractor()->onlyTrashed()->where('subcontractors.id', $subcontractor_id)->first())) {
                $site->subcontractor()->onlyTrashed()->where('subcontractors.id', $subcontractor_id)->first()->restore();
            } elseif (!$site->hasSubcontractor($subcontractor_id)) {
                Subcontractor::create([
                    'subdetail_id' => $subcontractor_id,
                    'site_id' => $site->id]);
            }
        }
        Session::flash('flash_message', "Alt yüklenici seçimleri güncellendi");
        return redirect()->back();

    }

    public function patchDelSubcontractor(Site $site)
    {
        $site->subcontractor()->delete();
        Session::flash('flash_message', "Taşeron kayıtları güncellendi");
        return redirect()->back();
    }

    public function postUpdateSubcontractor(Request $request)
    {
        $has_error = false;
        $subcontractor = Subcontractor::find($request->get('sub-id'));
        $sub_name = $subcontractor->subdetail->name;
        $subcontractor->manufacturing()->detach();
        if (!empty($request->get('manufacturings'))) {
            foreach ($request->get('manufacturings') as $man_id) {
                $subcontractor->manufacturing()->attach($man_id);
            }
        }
        $subcontractor->price = str_replace(",", ".", str_replace(".", "", $request->get('price')));
        $subcontractor->save();
        $contract = Contract::firstOrCreate(['contract_date' => CarbonHelper::getMySQLDate($request->get('contract_date')),
            'contract_start_date' => CarbonHelper::getMySQLDate($request->get('contract_start_date')),
            'contract_end_date' => CarbonHelper::getMySQLDate($request->get('contract_end_date'))]);
        $subcontractor->contract()->save($contract);


        if ($request->file("contractToUpload")) {
            $file = $request->file("contractToUpload");

            if (!empty($contract->file)) {
                $db_file = $contract->file;

                if (unlink($db_file->path . DIRECTORY_SEPARATOR . $db_file->name)) {

                    $directory = $db_file->path;
                    $filename = $file->getClientOriginalName();

                    if ($file->move($directory, $filename)) {

                        $db_file->name = $filename;
                        $db_file->save();
                    } else {
                        $has_error = true;
                    }
                } else {
                    $has_error = true;
                }
            } else {
                $directory = public_path() . '/uploads/' . uniqid(rand(), true);
                $filename = $file->getClientOriginalName();

                $upload_success = $file->move($directory, $filename);

                if ($upload_success) {
                    $db_file = File::create([
                        "name" => $filename,
                        "path" => $directory
                    ]);

                    $contract->file()->save($db_file);
                } else {
                    $has_error = true;
                }
            }
        }
        if ($has_error) {
            Session::flash('flash_message_error', "Dosya yüklenirken hata oluştu");
        } else {
            Session::flash('flash_message', "Alt yüklenici ($sub_name) kaydı güncellendi");
        }
        return redirect()->back();
    }

    public function postUpdateFee(Request $request)
    {
        $my_arr = $this->trCurrencyFormatter($request->all());

        $fee = Fee::firstOrNew(['subcontractor_id' => $request->get("subcontractor_id")]);
        foreach ($my_arr as $key => $value) {
            $fee->$key = $value;
        }
        $fee->save();

        Session::flash('flash_message', 'Bilgiler kaydedildi');

        return redirect()->back();

    }

    public function postUpdateCost(Request $request)
    {
        $explain = $request->get("explanation");
        if (isset($explain) && strlen($explain) == 0) {
            Session::flash('flash_message_error', 'Ek ödemelerin açıklama kısmı boş olamaz');
            $this->validate($request, [
                "explanation" => "required"
            ]);
        }
        $my_arr = $this->trCurrencyFormatter($request->all());
        $my_arr["pay_date"] = CarbonHelper::getMySQLDate($request->get("pay_date"));
        Cost::create($my_arr);
        Session::flash('flash_message', 'Bilgiler eklendi');

        return redirect()->back();
    }

    public function postSaveSubcontractorFiles(Request $request)
    {
        $subcontractor = Subcontractor::find($request->get('sub_id'));
        $db_file = $this->uploadFile($request->file("file"));

        if (!empty($db_file)) {
            $photo = Photo::create();
            $photo->file()->save($db_file);
            $subcontractor->photo()->save($photo);
        }


        if ($db_file && $photo) {
            return response()->json(['id' => $photo->id], 200);
        } else {
            return response()->json('error', 400);
        }
    }

    public function postDeleteSubcontractorFiles(Request $request)
    {
        Photo::find($request->get("fileid"))->delete();
        return response('success', 200);
    }

    public function postAddSubcontractorPersonnel(Request $request)
    {
        $this->validate($request, [
            'tck_no' => 'required | size:11',
            'name' => 'required',
            'contract' => 'required',
            'wage' => 'required'
        ]);
        $per_arr = $request->all();
        $per_arr["wage"] = str_replace(",", ".", $request->get("wage"));
        if (!empty($request->get("iban"))) {
            $per_arr["iban"] = preg_replace("/\\s+/ ", "", $request->get("iban"));
        }
        $personnel = Personnel::create($per_arr);
        $wage = Wage::create([
            'wage' => $per_arr["wage"],
            'since' => Carbon::parse($personnel->created_at)->toDateString()]);
        $wage->personnel()->associate($personnel);
        $wage->save();
        $directory = public_path() . '/uploads/' . uniqid(rand(), true);
        $contract_file = $this->uploadFile($request->file("contract"), $directory);
        $contract = Contract::create([
            'contract_date' => $request->get('contract_date'),
            'contract_start_date' => $request->get('contract_start_date'),
            'contract_end_date' => $request->get('contract_end_date'),
        ]);
        $contract->file()->save($contract_file);

        if (!empty($request->file("documents"))) {
            foreach ($request->file("documents") as $file) {
                $db_file = $this->uploadFile($file, $directory);

                if ($db_file) {
                    $photo = Photo::create();
                    $photo->file()->save($db_file);
                    $personnel->photo()->save($photo);
                }
            }
        }

        $personnel->contract()->save($contract);
        $subcontractor = Subcontractor::find($request->get('subcontractor_id'));
        $subcontractor->personnel()->save($personnel);

        Session::flash('flash_message', 'Personel eklendi');
        return redirect()->back();
    }


//  END OF TAŞERON CARİ HESAP PAGE


    public function getIsMakineleri(Site $site, Module $modules)
    {
        return view('tekil/equipments', compact('site', 'modules'));
    }

    public function postEditEquipments(Site $site, Request $request)
    {
        $site->equipment()->detach();

        foreach ($request->get("equipments") as $equipment) {
            $site->equipment()->attach($equipment);
        }

        Session::flash('flash_message', "Şantiye iş makineleri güncellendi");
        return redirect()->back();
    }

// START OF KASA PAGE
    public function getKasa(Site $site, Module $modules)
    {
        return view('tekil/account', compact('site', 'modules'));
    }

    public function postAddExpense(Request $request)
    {
        $this->validate($request, [
            'exp_date' => 'required',
            'account_id' => 'required',
            'buyer' => 'required',
            'definition' => 'required',
            'type' => 'required',
        ]);

        Expense::create([
            'exp_date' => CarbonHelper::getMySQLDate($request->get("exp_date")),
            'account_id' => $request->get("account_id"),
            'buyer' => $request->get("buyer"),
            'definition' => $request->get("definition"),
            'expense' => $request->get("expense"),
            'income' => $request->get("income"),
            'type' => $request->get("type"),
        ]);
        return response('success', 200);
    }

    public function getExpenses(Site $site)
    {

        $account = $site->account;
        $co = $account->card_owner;
        $expense_arr = [];
        foreach ($account->expense()->get() as $expense) {
            array_push($expense_arr, [
                'date' => CarbonHelper::getTurkishDate($expense->exp_date),
                'definition' => $expense->definition,
                'buyer' => $expense->buyer,
                'type' => $expense->type,
                'income' => $expense->income,
                'expense' => $expense->expense,
                'card_owner' => $co]);

        }


        return isset($expense_arr) ? response()->json($expense_arr, 200) : response()->json('error', 400);
    }

//    END OF KASA PAGE

    public function postCheckTck(Request $request)
    {
        if (is_null(Personnel::where('tck_no', $request->get('tck_no'))->first())) {
            return response()->json('unique', 200);
        } else {
            return response()->json('found!', 200);
        }

    }


//    START OF PUANTAJ PAGE

    public function getPuantaj(Site $site, Module $modules)
    {
        return view('tekil/shift', compact('site', 'modules'));
    }

    public function postOvertimes(Request $request)
    {
        /**
         * site'in ve taşeronunun personeli lazım
         * günler lazım
         * personelin o rapor için shift->overtime->name kısaltılacak
         * personelin o rapor için shift->overtime->multiplier'ı lazım
         */
        $site = Site::find($request->get('sid'));
        $response_arr["personnel"][0]["name"] = "Garden İnşaat";
        $all_personnel = Personnel::sitePersonnel()->get();
        for ($i = 0; $i < sizeof($all_personnel); $i++) {
            $response_arr["personnel"][$i + 1] = $all_personnel[$i]->toArray();
        }
        $i = sizeof($response_arr["personnel"]);
        foreach ($site->subcontractor()->get() as $subcont) {
            $response_arr["personnel"][$i]["name"] = $subcont->subdetail->name;
            $i++;
            foreach ($subcont->personnel()->get() as $per) {
                $response_arr["personnel"][$i] = $per->toArray();
                $i++;
                $all_personnel->push($per);
            }
        }

        $reports = $site->report()->where('created_at', '>=', $request->get('start_date'))->where('created_at', '<=', $request->get('end_date'))->get();
        $group_indexes = [];
        $my_days_format = sizeof($reports) > 25 ? 'd.m' : 'd';
        $days = [];
        $weekends = [];

        foreach ($reports as $rep) {
            $is_weekend = (date('N', strtotime($rep->created_at)) >= 6) ? 1 : 0;
            array_push($weekends, $is_weekend);
            array_push($days, Carbon::parse($rep->created_at)->format($my_days_format));
        }
        for ($i = 0; $i < sizeof($response_arr["personnel"]); $i++) {
            if (isset($response_arr["personnel"][$i]["tck_no"])) {
                $response_arr["personnel"][$i]["name"] = TurkishChar::tr_camel($response_arr["personnel"][$i]["name"]);
            } else {
                $response_arr["personnel"][$i]["name"] = TurkishChar::tr_up($response_arr["personnel"][$i]["name"]) ;
                array_push($group_indexes, $i);
            }
        }
        $i = 0;
        $j = 0;
        foreach ($all_personnel as $per) {
            $shift_type = [];
            $shift_multiplier = 0;
            $overtime = 0;
            $pntj_total = 0;
            $wage_total = 0;
            $per_wage = Wage::where('personnel_id', $per->id)->orderBy('since', 'DESC')
                ->where('since', '<=', Carbon::parse($rep->created_at)->toDateString())->first();
            foreach ($reports as $rep) {

                if (!is_null($rep->shift()->where('personnel_id', $per->id)->first())) {
                    $shift = $rep->shift()->where('personnel_id', $per->id)->first();
                    if (!is_null($shift->overtime()->first())) {
                        $words = preg_split("/\\s+/", $shift->overtime->name);
                        $acronym = "";

                        foreach ($words as $w) {
                            $acronym .= TurkishChar::tr_up(mb_substr($w, 0, 1, 'UTF-8'));
                        }
                        array_push($shift_type, $acronym);
                        $shift_multiplier = (double)$shift->overtime->multiplier;
                        if (is_null($shift->hour) || empty($shift->hour) || $shift->hour == 999) {
                            $overtime = 0;
                        } else {
                            $overtime = (double)$shift->hour;
                        }
                    } else {
                        array_push($shift_type, 'ÇY');
                    }
                } else {
                    array_push($shift_type, 'ÇY');
                }
                if ($overtime > 0) {
                    if (is_null($per_wage)) {
                        $wage_total = 0;
                    } else {
                        $wage_total += ($shift_multiplier * $overtime) + (double)$per_wage->wage;
                    }
                    $pntj_total += $shift_multiplier * $overtime;
                } else {
                    if (is_null($per_wage)) {
                        $wage_total = 0;
                    } else {
                        $wage_total += $shift_multiplier * (double)$per_wage->wage;
                    }
                    $pntj_total += $shift_multiplier;
                }
            }
            if ($j < sizeof($group_indexes) && $i == $group_indexes[$j]) {
                $i++;
                $j++;
            }
            $response_arr["personnel"][$i]["type"] = $shift_type;
            $response_arr["personnel"][$i]["wage"] = $wage_total;
            $response_arr["personnel"][$i]["puantaj"] = $pntj_total;
            $i++;
        }
        $response_arr["days"] = $days;
        $response_arr["weekends"] = $weekends;
        for($i = 0; $i<sizeof($group_indexes); $i++){
            $wage_total = 0;
            $pntj_total = 0;
            for($j = $group_indexes[$i]+1; $j<sizeof($response_arr["personnel"]); $j++){
                if($i+1<sizeof($group_indexes) && $group_indexes[$i+1] == $j){
                    break;
                }
                $wage_total += $response_arr["personnel"][$j]["wage"];
                $pntj_total += $response_arr["personnel"][$j]["puantaj"];
            }
            for ($k = 0; $k<sizeof($reports); $k++){
                $response_arr["personnel"][$group_indexes[$i]]["type"][$k] = "";
            }
            $response_arr["personnel"][$group_indexes[$i]]["wage"] = $wage_total;
            $response_arr["personnel"][$group_indexes[$i]]["puantaj"] = $pntj_total;
        }
        return response()->json($response_arr, 200);
    }


    /**
     *
     * PRIVATE FUNCTIONS
     *
     */


    private function uploadFile($file, $directory = null)
    {
        if (empty($directory)) {
            $directory = public_path() . '/uploads/' . uniqid(rand(), true);
        }
        $filename = $file->getClientOriginalName();

        if ($file->move($directory, $filename))

            return File::create([
                "name" => $filename,
                "path" => $directory
            ]);

        return null;
    }

    private function trCurrencyFormatter($my_arr)
    {
        if (array_key_exists("_token", $my_arr)) {
            unset($my_arr["_token"]);
        }
        foreach ($my_arr as $key => $value) {
            $my_arr[$key] = str_replace(",", ".", str_replace(".", "", $value));
        }
        return $my_arr;
    }

    private function getDistinct($arr)
    {
        $my_arr = [];
        foreach ($arr as $val) {
            if (!in_array($val, $my_arr)) {
                array_push($my_arr, $val);
            }
        }
        return $my_arr;
    }

    private function createShiftsMealsFromPerRepSite($per, $site, $report)
    {
        $create_arr = [
            'personnel_id' => $per->id,
            'report_id' => $report->id,
            'site_id' => $site->id
        ];
        $shift = Shift::firstOrNew($create_arr);
        $per->shift()->save($shift);
        $site->shift()->save($shift);
        $report->shift()->save($shift);
        $shift->save();
        $meal = Meal::firstOrNew($create_arr);
        $per->meal()->save($meal);
        $site->meal()->save($meal);
        $report->meal()->save($meal);
        $meal->save();
    }

    private function deleteShiftsMealsFromPerRepSite($per, $site, $report)
    {
        Shift::where('personnel_id', $per->id)->where('site_id', $site->id)->where('report_id', $report->id)->delete();
        Meal::where('personnel_id', $per->id)->where('site_id', $site->id)->where('report_id', $report->id)->delete();

    }
}
