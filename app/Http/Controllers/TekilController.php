<?php

namespace App\Http\Controllers;

use App\Account;
use App\Allowance;
use App\Contract;
use App\Demand;
use App\Equipment;
use App\Expdetail;
use App\Expenditure;
use App\Expense;
use App\Feature;
use App\Fee;
use App\File;
use App\Iddoc;
use App\Inmaterial;
use App\Instrument;
use App\Labor;
use App\Library\CarbonHelper;
use App\Library\TurkishChar;
use App\Library\Weather;
use App\Manufacturing;
use App\Material;
use App\Meal;
use App\Mealcost;
use App\Module;
use App\Outmaterial;
use App\Overtime;
use App\Payment;
use App\Personnel;
use App\Photo;
use App\Pricesmd;
use App\Pwunit;
use App\Receipt;
use App\Report;
use App\Shift;
use App\Site;
use App\Http\Requests;
use App\Smdemand;
use App\Smdexpense;
use App\Staff;
use App\Stock;
use App\Subcontractor;
use App\Submaterial;
use App\Swunit;
use App\Tag;
use App\Wage;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use PhpParser\Node\Expr\AssignOp\Mod;


class TekilController extends ManagementController
{
    //
    const SEKME_BIR = 1;
    const SEKME_IKI = 2;
    const SEKME_UC = 3;
    const SEKME_DORT = 4;

    public function getIndex(Site $site, Module $modules)
    {
        return view('tekil/main', compact('site', 'modules'));
    }

    public function getGunlukRapor(Site $site, Module $modules, Request $request)
    {

            $report = new Report;

            if (empty($report->where('created_at', Carbon::now()->toDateString())->where('site_id', $site->id)->first())) {
                $report->site_id = $site->id;
                $report->save();
                $pwunit = new Pwunit();
                $staff = Staff::find(1);
                $pwunit->staff()->associate($staff);
                $pwunit->report()->associate($report);
                $pwunit->save();
                foreach ($site->equipment()->get() as $eq) {
                    $report->equipment()->attach($eq->id);
                }
            } else {
                $report = $report->where('created_at', Carbon::now()->toDateString())->where('site_id', $site->id)->first();
            }

            if (session()->has('report')) {
                $report = session()->get('report');
            }
            $yesterdays_report = $site->report()->where('created_at', Carbon::yesterday()->toDateString())->first();
            if (!is_null($yesterdays_report) && is_null($yesterdays_report->weather)) {
                $wt = new Weather(1, $site->city->name);
                $yesterdays_report->weather = $wt->getDescription();
                $yesterdays_report->temp_min = $wt->getMin();
                $yesterdays_report->temp_max = $wt->getMax();
                $yesterdays_report->wind = $wt->getWind();
                $yesterdays_report->degree = $wt->getDirection();
                $yesterdays_report->save();
            }

            $cookieVal = empty($request->cookie('viewCount')) ? 1 : (int)$request->cookie('viewCount') + 1;
            $viewCount = $cookieVal;
            $response = new Response(view('tekil.daily', compact('site', 'modules', 'report', 'viewCount')));
            return $response->withCookie('viewCount', $cookieVal, 480);
    }

    public function postRetrieveReportDays(Request $request, Site $site)
    {
        $dt = Carbon::parse($request->date);

        $resp_arr = [];
        for ($i = 1; $i <= $dt->daysInMonth; $i++) {
            $my_d = Carbon::create($dt->year, $dt->month, $i)->toDateString();
            if (!($site->report()->where('created_at', '=', $my_d)->get()->isEmpty())) {
                array_push($resp_arr, $i);
            }

        }
        return response($resp_arr, 200);
    }

    public function postFromDemand(Request $request)
    {
        foreach ($request->get("checked-id") as $arr_no) {
            $my_inmaterial = new Inmaterial();
            $my_inmaterial->coming_from = $request->get("coming_from")[$arr_no];
            $my_inmaterial->quantity = $request->get("quantity")[$arr_no];
            $my_inmaterial->unit = $request->get("unit")[$arr_no];
            $my_inmaterial->explanation = $request->get("explanation")[$arr_no];
            $my_inmaterial->report()->associate($request->get("rid"));
            $my_inmaterial->demand()->associate($request->get("demand")[$arr_no]);
            $my_inmaterial->material()->associate($request->get("mid")[$arr_no]);
            $my_inmaterial->save();
        }
        $report = Report::find($request->rid);

        return redirect()->back()->with('report', $report);
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
        $demand->firm = $request->get("firm");
        $demand->details = $request->get("details");
        $demand->demand_date = CarbonHelper::getMySQLDate($request->get("demand_date"));
        $demand->site()->associate($site);
        $demand->save();
        $i = 0;
        foreach ($request->get("materials") as $mat) {
            $my_mat = Material::find($mat);
            $my_mat->demands()->attach($demand->id,
                ["quantity" => $request->get("quantity")[$i],
                    "unit" => $request->get("unit")[$i],
                    "price" => $request->get("price")[$i],
                    "payment_type" => $request->get("payment_type")[$i]
                ]);
            $my_mat->save();
            $i++;
        }
        Session::flash('flash_message', 'Malzeme talep formu oluşturuldu. Talep no: ' . $demand->id);
        return redirect()->back();
    }

    public function getTalepDuzenle(Site $site, Module $modules, Demand $demand)
    {
        if ($demand->site()->where('id', $site->id)->get()->isEmpty()) {
            return redirect("/tekil/$site->slug/malzeme-talep");
        }
        if (session()->has("material_array")) {
            $material_array = session("material_array");
            return view('tekil.demand-edit', compact('site', 'modules', 'demand', 'material_array'));
        } else
            return view('tekil.demand-edit', compact('site', 'modules', 'demand'));

    }

    public function getTalepSevket(Site $site, Module $modules, Demand $demand)
    {
        if ($demand->site()->where('id', $site->id)->get()->isEmpty()) {
            return redirect("/tekil/$site->slug/malzeme-talep");
        }
        $demand->approval_status = 1;
        $demand->save();
        Session::flash('flash_message', 'İlgili talep proje müdürünüze sevkedilmiştir.');
        $tab = 1;
        return redirect()->back()->with('tab', $tab);
    }

    public function postUpdateDemand(Request $request)
    {
        $demand = Demand::find($request->get("did"));
        $demand->details = $request->details;
        $demand->demand_date = CarbonHelper::getMySQLDate($request->demand_date);
        $demand->firm = $request->firm;
        $demand->save();

        for ($i = 0; $i < sizeof($request->get("materials")); $i++) {
            $mat = Material::find($request->get("materials")[$i]);
            if ($demand->materials()->where('material_id', $mat->id)->get()->isEmpty()) {
                $demand->materials()->attach($mat, [
                    'unit' => TurkishChar::tr_up($request->get("unit")[$i]),
                    'quantity' => TurkishChar::convertCurrencyFromTr($request->get("quantity")[$i]),
                    'price' => TurkishChar::convertCurrencyFromTr($request->get("price")[$i]),
                    'payment_type' => $request->get("payment_type")[$i]
                ]);
            } else {
                $mat = $demand->materials()->where('material_id', $mat->id)->first();
                $mat->pivot->unit = TurkishChar::tr_up($request->get("unit")[$i]);
                $mat->pivot->quantity = TurkishChar::convertCurrencyFromTr($request->get("quantity")[$i]);
                $mat->pivot->price = TurkishChar::convertCurrencyFromTr($request->get("price")[$i]);
                $mat->pivot->payment_type = $request->get("payment_type")[$i];
                $mat->pivot->save();
            }
        }

        Session::flash('flash_message', 'Malzeme talebi güncellendi');

        return redirect()->back();
    }

    public function postDelDemand(Request $request)
    {
        Demand::find($request->get("userDeleteIn"))->delete();
        Session::flash('flash_message', 'İlgili talep kaldırıldı');

        return redirect()->back();

    }

    public function postDelMaterial(Request $request)
    {
        Demand::find($request->did)->materials()->detach($request->id);
        return response('success', 200);
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
        $report = Report::find($request->get("report_id"));
        if ($request->has("staffs")) {
            $personnel_arr = $this->getDistinct($request->get("staffs"));
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
        }
        return redirect()->back()->with('report', $report);
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
        for ($i = 0; $i < sizeof($main_staff_arr); $i++) {
            switch ($main_staff_arr[$i]) {
                case 0:
                    $report->management_staff = $q_arr[$i];
                    break;
                case 1:
                    $report->employer_staff = $q_arr[$i];
                    break;
                case 2:
                    $report->building_control_staff = $q_arr[$i];
                    break;
                case 3:
                    $report->isg_staff = $q_arr[$i];
                    break;
            }
        }

        $report->save();

        Session::flash('flash_message', 'Personel kaydı başarılı');
        return redirect()->back()->with('report', $report);
    }

    public function postDeleteManagementStaff(Request $request)
    {
        $report = Report::find($request->get("reportid"));
        $column_name = $request->get("column");
        $report->$column_name = "0";
        $report->save();
        return response()->json('success', 200);
    }

    public function postAttachTag(Request $request)
    {
        if ($request->has('fileid')) {
            $file = File::find($request->fileid);
            $file->tag()->detach();
            if (!empty($request->tags))
                $this->attachTags($request->tags, $file);
        } else {
            foreach ($request->file_id as $fid) {
                $file = File::find($fid);
                $file->tag()->detach();
                if ($request->has("tags-" . $fid)) {
                    $this->attachTags($request->get("tags-" . $fid), $file);
                }
            }
        }
        return response()->json('success', 200);
    }

    private function attachTags($tags, $file)
    {
        foreach ($tags as $tid) {
            $tag = Tag::find($tid);
            $tag->file()->attach($file);
        }
    }

    public function postSaveSubcontractorStaff(Site $site, Request $request)
    {
//        Daily report subcontractor staff
        $report = Report::find($request->get("report_id"));
        if ($request->has("substaffs")) {
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
        }
        return redirect()->back()->with('report', $report)->with('anchor', '#subcontractor_staff');
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
        return redirect()->back()->with('report', $report);
    }

    public function postRetrieveSubcontractors(Request $request)
    {
        $site = Site::find($request->sid);
        $resp_arr = [];
        foreach ($site->subcontractor()->get() as $sub) {
            if (count($sub->subdetail))
                array_push($resp_arr, [
                    'name' => $sub->subdetail->name,
                    'id' => $sub->id
                ]);
        }
        return response($resp_arr, 200);
    }

    public function postDetachEquipment(Request $request)
    {
//        Main contractor
        $equipment_id = $request->get("equipmentid");
        $report = Report::find($request->get("report_id"));
        $report->equipment()->detach($equipment_id);
        return response()->json('success', 200);
    }

    public function postRetrieveSw(Request $request)
    {
        $report = Report::find($request->rid);
        $resp_arr = [];
        foreach ($report->swunit()->get() as $sw) {
            array_push($resp_arr, [
                'subid' => $sw->subcontractor->id,
                'id' => $sw->id,
                'name' => $sw->subcontractor->subdetail->name,
                'quantity' => $sw->quantity,
                'unit' => $sw->unit,
                'work_done' => $sw->works_done,
                'planned' => TurkishChar::convertToTRcurrency($sw->planned),
                'done' => TurkishChar::convertToTRcurrency($sw->done)
            ]);
        }
        return response($resp_arr, 200);
    }

    public function postRetrievePw(Request $request)
    {
        $report = Report::find($request->rid);
        $resp_arr = [];
        foreach ($report->pwunit()->get() as $sw) {
            array_push($resp_arr, [
                'quantity' => $sw->quantity,
                'unit' => $sw->unit,
                'work_done' => $sw->works_done,
                'planned' => TurkishChar::convertToTRcurrency($sw->planned),
                'done' => TurkishChar::convertToTRcurrency($sw->done)
            ]);
        }
        return response($resp_arr, 200);
    }

    public function postSavePw(Request $request)
    {
        $report = Report::find($request->rid);
        $swunit = $report->pwunit()->first();
        $sw_arr = $request->all();
        $sw_arr["planned"] = TurkishChar::convertCurrencyFromTr($request->planned);
        $sw_arr["done"] = TurkishChar::convertCurrencyFromTr($request->done);
        unset($sw_arr["rid"]);
        foreach ($sw_arr as $k => $v) {
            $swunit->$k = $v;
        }
        $swunit->report()->associate($report);
        $swunit->save();

        return response('success', 200);
    }

    public function postDelSwunit(Request $request)
    {
        Swunit::find($request->id)->delete();
        return response('success', 200);
    }


    public function postSaveWorkDone(Request $request)
    {
        $report = Report::find($request->rid);
        $subcontractor = Subcontractor::find($request->subid);
        $swunit = new Swunit();
        $sw_arr = $request->all();
        $sw_arr["planned"] = TurkishChar::convertCurrencyFromTr($request->planned);
        $sw_arr["done"] = TurkishChar::convertCurrencyFromTr($request->done);
        unset($sw_arr["rid"]);
        unset($sw_arr["subid"]);
        foreach ($sw_arr as $k => $v) {
            $swunit->$k = $v;
        }
        $swunit->report()->associate($report);
        $swunit->subcontractor()->associate($subcontractor);
        $swunit->save();

        return response('success', 200);
    }

    public function postAddGardenStaff(Request $request)
    {
        $report = Report::find($request->get("report_id"));
        $pwunit = new Pwunit();
        $staff = Staff::find(1);
        $pwunit->staff()->associate($staff);
        $pwunit->report()->associate($report);
        $pwunit->save();
        Session::flash('flash_message', 'Garden çalışan birimi eklendi');
        return redirect()->back()->with('report', $report);
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
        $i = 0;
        $report = Report::find($request->get("report_id"));

        if (!empty($request->get("inmat-id"))) {
            foreach ($request->get("inmat-id") as $id) {
                $inmaterial = Inmaterial::find($id);
                $this->saveIncomingMaterial($request, $inmaterial, $i);
                $i++;
            }
        }

        for ($i; $i < sizeof($in_arr); $i++) {
            $inmaterial = new Inmaterial();
            $inmaterial->material()->associate(Material::find($request->get("inmaterials")[$i]));
            $inmaterial->report()->associate($report);
            $inmaterial->save();
            $this->saveIncomingMaterial($request, $inmaterial, $i);
            Session::flash('flash_message', 'Gelen malzeme tablosu güncellendi');
        }


        return redirect()->back()->with('report', $report)->with('anchor', '#incoming_table');
    }

    private function saveIncomingMaterial($request, $inmaterial, $i)
    {
        $inmaterial->coming_from = $request->get("inmaterial-from")[$i];
        $inmaterial->quantity = str_replace(",", ".", str_replace(".", "", $request->get("inmaterial-quantity")[$i]));
        $inmaterial->unit = $request->get("inmaterial-unit")[$i];
        $inmaterial->explanation = $request->get("inmaterial-explanation")[$i];
        $inmaterial->irsaliye = $request->get("inmaterial-irsaliye")[$i];
        $inmaterial->save();
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


        return redirect()->back()->with('report', $report)->with('anchor', '#outgoing_table');
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
        $success = true;
        if (is_int($db_file) && strpos('format', $db_file) !== false) {
            Session::flash('flash_message_error', 'İzin verilmeyen dosya formatı. İlgili personel için dosyaları güncelleyin!');
            $success = false;
        }
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
        $report = Report::find($report_id);
        return redirect()->back()->with('report', $report)->with('anchor', '#shifts_meals');
    }

    public function postSaveNotes(Request $request)
    {
        $report_id = $request->get("rid");
        $report = Report::find($report_id);
        $report->notes = $request->get("notes");
        $report->save();
        Session::flash('flash_message', "Ertesi gün notları kaydedildi");
        return redirect()->back()->with('report', $report)->with('anchor', '#notes');
    }

//    END OF GUNLUK RAPOR PAGE


    //  TAŞERON CARİ HESAP PAGE AND RELATED OPERATIONS
    public function getAltYukleniciCariHesap(Site $site, Module $modules)
    {
        return view('tekil.subcontractor-account', compact('site', 'modules'));
    }

    public function getAltYukleniciDuzenle(Site $site, Module $modules, Subcontractor $subcontractor)
    {
        if (is_null($site->subcontractor()->find($subcontractor->id))) {
            return redirect()->back();
        }
        return view('tekil.subcontractor-edit', compact('subcontractor', 'site', 'modules'));
    }

    public function postAddSubcontractor(Request $request, Site $site)
    {
        $subcontractor_ids = $request->get("subcontractors");
        foreach ($subcontractor_ids as $subcontractor_id) {
            if (!is_null($site->subcontractor()->onlyTrashed()->where('subcontractors.id', $subcontractor_id)->first())) {
                $site->subcontractor()->onlyTrashed()->where('subcontractors.id', $subcontractor_id)->first()->restore();
            } elseif (!$site->hasSubcontractor($subcontractor_id)) {
                $sub = Subcontractor::create([
                    'subdetail_id' => $subcontractor_id,
                    'site_id' => $site->id]);
                $fee = new Fee();
                $fee->subcontractor()->associate($sub);
                $fee->save();
            }
        }
        Session::flash('flash_message', "Alt yüklenici seçimleri güncellendi");
        return redirect()->back();

    }

    public function patchDelSubcontractor(Request $request)
    {
        $sub_id = $request->get("subDeleteIn");
        Subcontractor::find($sub_id)->delete();
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
        $subcontractor->additional_bid_cost = str_replace(",", ".", str_replace(".", "", $request->get('additional_bid_cost')));
        $subcontractor->kdv = $request->kdv;
        $subcontractor->save();
        if ($subcontractor->contract()->get()->isEmpty()) {
            $contract = Contract::firstOrCreate(['contract_date' => CarbonHelper::getMySQLDate($request->get('contract_date')),
                'contract_start_date' => CarbonHelper::getMySQLDate($request->get('contract_start_date')),
                'exit_date' => CarbonHelper::getMySQLDate($request->get('exit_date')),
                'contract_end_date' => CarbonHelper::getMySQLDate($request->get('contract_end_date'))]);
            $subcontractor->contract()->save($contract);
        } else {
            $contract = $subcontractor->contract;
            $contract->contract_date = CarbonHelper::getMySQLDate($request->get('contract_date'));
            $contract->contract_start_date = CarbonHelper::getMySQLDate($request->get('contract_start_date'));
            $contract->contract_end_date = CarbonHelper::getMySQLDate($request->get('contract_end_date'));
            $contract->exit_date = CarbonHelper::getMySQLDate($request->get('exit_date'));
            $contract->save();
        }

        if ($request->file("contractToUpload")) {
            $file = $request->file("contractToUpload");

            if (!$contract->file()->get()->isEmpty()) {
                $db_file = $contract->file()->first();

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

    public function postRetrievePayments(Request $request)
    {
        return response($this->subcontractorPayments($request->subId), 200);
    }

    public function postAddPayment(Request $request)
    {
        $pay_arr = $request->all();
        unset($pay_arr['subId']);
        $pay_arr['payment_date'] = CarbonHelper::getMySQLDate($pay_arr['payment_date']);
        $pay_arr['amount'] = $request->amount;
        $payment = Payment::create($pay_arr);
        $payment->subcontractor()->associate(Subcontractor::find($request->subId));
        $payment->save();
        return response('success', 200);
    }

    public function postDelPayment(Request $request)
    {
        Payment::find($request->id)->delete();
        return response('success', 200);
    }

    public function postSaveSubcontractorFiles(Request $request)
    {
        $subcontractor = Subcontractor::find($request->get('sub_id'));
        $db_file = $this->uploadFile($request->file("file"));
        if (is_int($db_file) && strpos('format', $db_file) !== false) {
            Session::flash('flash_message_error', 'İzin verilmeyen dosya formatı. İlgili personel için dosyaları güncelleyin!');
        }
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
            'wage' => 'required',
            'staff_id' => 'required'
        ]);
        $per_arr = $request->all();
        $per_arr["wage"] = str_replace(",", ".", $request->get("wage"));
        if (!empty($request->get("iban"))) {
            $per_arr["iban"] = preg_replace("/\\s+/ ", "", $request->get("iban"));
        }
        $success = true;
        $directory = public_path() . '/uploads/' . uniqid(rand(), true);
        $personnel = new Personnel($per_arr);
        $personnel->save();

        if(!empty($request->file("contract"))) {
            $contract_file = $this->uploadFile($request->file("contract"), $directory);
            if (is_int($contract_file) && strpos('format', $contract_file) !== false) {
                Session::flash('flash_message_error', 'İzin verilmeyen dosya formatı. İlgili personel için dosyaları güncelleyin!');
                $success = false;
            }
            $contract = new Contract();
            if (isset($request->exit_date)) {
                $contract->exit_date = CarbonHelper::getMySQLDate($request->exit_date);
            }
            $contract->save();
            $contract->file()->save($contract_file);
            $personnel->contract()->save($contract);
        }


        $wage = new Wage([
            'wage' => $per_arr["wage"],
            'since' => Carbon::parse($personnel->created_at)->toDateString()]);
        $wage->save();
        $personnel->wage()->save($wage);

        if (!empty($request->file("documents"))) {
            foreach ($request->file("documents") as $file) {
                $db_file = $this->uploadFile($file, $directory);
                if (is_int($db_file) && strpos('format', $db_file) !== false) {
                    Session::flash('flash_message_error', 'İzin verilmeyen dosya formatı!');
                    $success = false;
                }
                if ($db_file) {
                    $photo = Photo::create();
                    $photo->file()->save($db_file);
                    $personnel->photo()->save($photo);
                }
            }
        }

        if(!empty($request->file("iddoc"))) {
            $id_file = $this->uploadFile($request->file("iddoc"), $directory);
            if (is_int($id_file) && strpos('format', $id_file) !== false) {
                Session::flash('flash_message_error', 'İzin verilmeyen dosya formatı. İlgili personel için dosyaları güncelleyin!');
                $success = false;
            }
            $iddoc = new Iddoc();
            $iddoc->save();
            $iddoc->file()->save($id_file);
            $personnel->iddoc()->save($iddoc);
        }

        $subcontractor = Subcontractor::find($request->get('subcontractor_id'));
        $subcontractor->personnel()->save($personnel);
        if ($success)
            Session::flash('flash_message', 'Personel eklendi');
        else{
            $personnel->forceDelete();
            $wage->delete();
        }
        return redirect()->back()->with(['tab' => 'insert']);
    }


//  END OF TAŞERON CARİ HESAP PAGE




// START OF KASA PAGE
    public function getKasa(Site $site, Module $modules)
    {
        return view('tekil.account', compact('site', 'modules'));
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
            'expense' => empty($request->get("expense")) ? 0 : $request->get("expense"),
            'income' => empty($request->get("income")) ? 0 : $request->get("income"),
            'type' => $request->get("type"),
        ]);
        return response('success', 200);
    }

    public function postExpenses(Site $site, Request $request)
    {
        $account = $site->account;
        $co = $account->card_owner;
        $expenses = $account->expense()->get();
        if ($request->has("start_date") && !empty($request->start_date)) {

            $expenses = $account->expense()->dateRange($request->start_date, $request->end_date)->get();
        }
        $expense_arr = [];
        foreach ($expenses as $expense) {
            array_push($expense_arr, [
                'id' => $expense->id,
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

    public function postDelExpense(Request $request)
    {
        Expense::find($request->id)->delete();
        return response('success', 200);
    }

//    END OF KASA PAGE


//    START OF PUANTAJ PAGE

    public function getPuantaj(Site $site, Module $modules)
    {
        return view('tekil.shift', compact('site', 'modules'));
    }

    public function postOvertimes(Site $site, Request $request)
    {
        /**
         * site'in ve taşeronunun personeli lazım
         * günler lazım
         * personelin o rapor için shift->overtime->name kısaltılacak
         * personelin o rapor için shift->overtime->multiplier'ı lazım
         */
        $total_date = $this->getTotalDate($request->get('start_date'), $request->get('end_date'));
        $response_arr["personnel"][0]["name"] = "Garden İnşaat";
        $all_personnel = Personnel::onlyTrashed()->sitePersonnel()->where('deleted_at', '>=', $request->start_date)->get();
        foreach (Personnel::sitePersonnel()->where('created_at', '<=', $request->end_date)->get() as $sub1)
            $all_personnel->push($sub1);

        foreach ($all_personnel as $sub_arr) {
            array_push($response_arr["personnel"], $sub_arr->toArray());
        }


        $i = sizeof($response_arr["personnel"]);
        foreach ($site->subcontractor()->get() as $subcont) {
            if (count($subcont->subdetail)) {
                $response_arr["personnel"][$i]["name"] = $subcont->subdetail->name;
                $i++;
                $sub_per = $subcont->personnel()->onlyTrashed()->where('deleted_at', '>=', $request->start_date)->get();
                foreach ($subcont->personnel()->where('created_at', '<=', $request->end_date)->get() as $sub)
                    $sub_per->push($sub);

                foreach ($sub_per as $sub) {
                    $i++;
                    array_push($response_arr["personnel"], $sub->toArray());
                    $all_personnel->push($sub);
                }

            }
        }

        $group_indexes = [];
        $my_days_format = $total_date > 25 ? 'd.m' : 'd';
        $days = [];
        $weekends = [];

        for ($x = $total_date; $x >= 0; $x--) {
            $rep_date = Carbon::parse($request->get('end_date'))->subDays($x)->toDateString();
            $is_weekend = (date('N', strtotime($rep_date)) >= 6) ? 1 : 0;
            array_push($weekends, $is_weekend);
            array_push($days, Carbon::parse($rep_date)->format($my_days_format));
        }
        for ($i = 0; $i < sizeof($response_arr["personnel"]); $i++) {
            if (isset($response_arr["personnel"][$i]["tck_no"])) {
                $response_arr["personnel"][$i]["name"] = TurkishChar::tr_camel($response_arr["personnel"][$i]["name"]);
            } else {
                $response_arr["personnel"][$i]["name"] = TurkishChar::tr_up($response_arr["personnel"][$i]["name"]);
                array_push($group_indexes, $i);
            }
        }
        if (sizeof($all_personnel) > 0) {
            $all_personnel->load('wage', 'shift');
        }

        $j = 0;
        foreach ($all_personnel as $per) {
            $shift_type = [];
            $overtime_hours_total = 0;
            $pntj_total = 0;
            $wage_total = 0;
            for ($x = $total_date; $x >= 0; $x--) {
                $rep_date = Carbon::parse($request->get('end_date'))->subDays($x)->toDateString();
                $rep = $site->report()->ofDate($rep_date)->first();
                $per_wage = $per->wage()->sinceDate($rep_date)->first();
                $overtime = 0;
                $shift_multiplier = 0;
                if (!is_null($rep)) {
                    if (!($per->shift()->ofReport($rep->id)->get()->isEmpty())) {
                        $shift = $per->shift()->ofReport($rep->id)->first();
                        if (!($shift->overtime()->get()->isEmpty())) {
                            $words = preg_split("/\\s+/", $shift->overtime->name);
                            $acronym = "";

                            foreach ($words as $w) {
                                $acronym .= TurkishChar::tr_up(mb_substr($w, 0, 1, 'UTF-8'));
                            }
                            $acronym = strpos($acronym, 'TG') !== false ? 'X' : $acronym;
                            $acronym = strpos($acronym, 'ÇY') !== false ? '-' : $acronym;
                            array_push($shift_type, $acronym);
                            $shift_multiplier = (double)$shift->overtime->multiplier;
                            if (is_null($shift->hour) || empty($shift->hour) || $shift->hour == 999) {
                                $overtime = 0;
                            } else {
                                $overtime = (double)$shift->hour;
                            }
                        } else {
                            array_push($shift_type, '-');
                        }
                    } else {
                        array_push($shift_type, '-');
                    }
                    if ($overtime > 0) {
                        if (empty($per_wage)) {
                            $wage_total += 0;
                        } else {
                            $wage_total += ($shift_multiplier * $overtime * 1.5) + (double)$per_wage->wage;
                        }
                        $overtime_hours_total += $overtime;
                        $pntj_total += 1;
                    } else {
                        if (empty($per_wage)) {
                            $wage_total = 0;
                        } else {
                            $wage_total += $shift_multiplier * (double)$per_wage->wage;
                        }
                        $pntj_total += $shift_multiplier;
                    }
                } else {
                    array_push($shift_type, '-');
                }
            }

            for ($i = $j; $i < sizeof($response_arr["personnel"]); $i++, $j++) {
                if (isset($response_arr["personnel"][$i]["id"]) && $response_arr["personnel"][$i]["id"] == $per->id) {
                    $response_arr["personnel"][$i]["type"] = $shift_type;
                    $response_arr["personnel"][$i]["wage"] = $wage_total;
                    if ($overtime_hours_total > 0) {
                        $response_arr["personnel"][$i]["puantaj"] = $pntj_total . "T + $overtime_hours_total" . "FM";
                    } else {
                        $response_arr["personnel"][$i]["puantaj"] = $pntj_total;
                    }
                    break;
                }

            }
        }

        $response_arr["days"] = $days;
        $response_arr["weekends"] = $weekends;
        for ($i = 0; $i < sizeof($group_indexes); $i++) {
            $wage_total = 0;
            $pntj_total = 0;
            $os_total = 0;
            for ($j = $group_indexes[$i] + 1; $j < sizeof($response_arr["personnel"]); $j++) {
                if ($i + 1 < sizeof($group_indexes) && $group_indexes[$i + 1] == $j) {
                    break;
                }
                $wage_total += isset($response_arr["personnel"][$j]["wage"]) ? $response_arr["personnel"][$j]["wage"] : 0;
                if (isset($response_arr["personnel"][$j]["puantaj"])) {
                    $extra = explode('T + ', $response_arr["personnel"][$j]["puantaj"]);
                    $pntj_total += (double)$extra[0];
                    if (sizeof($extra) > 1)
                        $os_total += (double)(str_replace("FM", "", $extra[1]));
                }
            }
            for ($k = 0; $k < sizeof($days); $k++) {
                $response_arr["personnel"][$group_indexes[$i]]["type"][$k] = "";
            }
            $response_arr["personnel"][$group_indexes[$i]]["wage"] = $wage_total;
            $response_arr["personnel"][$group_indexes[$i]]["puantaj"] = $os_total > 0 ? $pntj_total . "T + $os_total" . "FM" : $pntj_total;
        }
        for ($i = 0; $i < sizeof($response_arr["personnel"]); $i++) {
            if (isset($response_arr["personnel"][$i]["wage"])) {
                $response_arr["personnel"][$i]["wage"] = number_format($response_arr["personnel"][$i]["wage"], 2, ',', '.') . "TL";
            }
        }
        $all_personnel = Personnel::sitePersonnel()->get();
        for ($i = 0; $i < sizeof($all_personnel); $i++) {
            $response_arr["personnel"][$i + 1]["wage"] = "-";
        }
        $response_arr["personnel"][0]["wage"] = "-";

        $response_arr["main"] = [
            ['name' => !empty($site->management_name) ? $site->management_name : "İdare belirtilmemiş", 'puantaj' => [], 'total' => 0],
            ['name' => !empty($site->employer) ? $site->employer : "İşveren belirtilmemiş", 'puantaj' => [], 'total' => 0],
            ['name' => !empty($site->building_control) ? $site->building_control : "Yapı Denetim belirtilmemiş", 'puantaj' => [], 'total' => 0],
            ['name' => !empty($site->isg) ? $site->isg : "İSG belirtilmemiş", 'puantaj' => [], 'total' => 0]
        ];
        for ($x = $total_date; $x >= 0; $x--) {
            $rep_date = Carbon::parse($request->get('end_date'))->subDays($x)->toDateString();
            $rep = $site->report()->ofDate($rep_date)->first();

            $staff = empty($rep->management_staff) ? 0 : $rep->management_staff;
            $response_arr["main"][0]['total'] += $staff;
            array_push($response_arr["main"][0]['puantaj'], $staff);

            $staff = empty($rep->employer_staff) ? 0 : $rep->employer_staff;
            $response_arr["main"][1]['total'] += $staff;
            array_push($response_arr["main"][1]['puantaj'], empty($rep->employer_staff) ? 0 : $rep->employer_staff);

            $staff = empty($rep->building_control_staff) ? 0 : $rep->building_control_staff;
            $response_arr["main"][2]['total'] += $staff;
            array_push($response_arr["main"][2]['puantaj'], empty($rep->building_control_staff) ? 0 : $rep->building_control_staff);

            $staff = empty($rep->isg_staff) ? 0 : $rep->isg_staff;
            $response_arr["main"][3]['total'] += $staff;
            array_push($response_arr["main"][3]['puantaj'], empty($rep->isg_staff) ? 0 : $rep->isg_staff);
        }
        return response()->json($response_arr, 200);
    }

    public function getYemek(Site $site, Module $modules)
    {
        return view('tekil/meal', compact('site', 'modules'));
    }

    public function postInsertMealcost(Site $site, Request $request)
    {
        $mc_arr = $request->all();
        $mc_arr["since"] = CarbonHelper::getMySQLDate($mc_arr["since"]);
        $mc = Mealcost::create($mc_arr);
        $site->mealcost()->save($mc);
        return response('success', 200);
    }

    private function getTotalDate($start_date, $end_date)
    {
        $start_date = date_create($start_date);

        $end_date = date_create($end_date);
        $total_date = str_replace("+", "", date_diff($start_date, $end_date)->format("%R%a"));
        return (int)$total_date;
    }

    public function postMeals(Site $site, Request $request)
    {

        $total_date = $this->getTotalDate($request->get('start_date'), $request->get('end_date'));

        /**
         * site'in ve yemek yiyen taşeronunun personeli lazım
         * günler lazım
         * personelin o rapor için meal->meal 1,2,4 öğün isimleri kısaltılacak
         * personelin o rapor için mealcost->breakfast, lunch, supper fiyatları lazım
         */

        $response_arr["personnel"][0]["name"] = "Garden İnşaat";
        $all_personnel = Personnel::onlyTrashed()->sitePersonnel()->where('deleted_at', '>=', $request->start_date)->get();
        foreach (Personnel::sitePersonnel()->where('created_at', '<=', $request->end_date)->get() as $sub1)
            $all_personnel->push($sub1);

        foreach ($all_personnel as $sub_arr) {
            array_push($response_arr["personnel"], $sub_arr->toArray());
        }


        $i = sizeof($response_arr["personnel"]);
        foreach ($site->subcontractor()->get() as $subcont) {
            if (count($subcont->subdetail)) {
                if (!empty($subcont->fee()->first()->has_meal)) {
                    $response_arr["personnel"][$i]["name"] = $subcont->subdetail->name;
                    $i++;
                    $sub_per = $subcont->personnel()->onlyTrashed()->where('deleted_at', '>=', $request->start_date)->get();
                    foreach ($subcont->personnel()->where('created_at', '<=', $request->end_date)->get() as $sub)
                        $sub_per->push($sub);

                    foreach ($sub_per as $sub) {
                        $i++;
                        array_push($response_arr["personnel"], $sub->toArray());
                        $all_personnel->push($sub);
                    }
                }
            }
        }


        $group_indexes = [];
        $my_days_format = $total_date > 25 ? 'd.m' : 'd';
        $days = [];
        $weekends = [];

        for ($x = $total_date; $x >= 0; $x--) {
            $rep_date = Carbon::parse($request->get('end_date'))->subDays($x)->toDateString();
            $is_weekend = (date('N', strtotime($rep_date)) >= 6) ? 1 : 0;
            array_push($weekends, $is_weekend);
            array_push($days, Carbon::parse($rep_date)->format($my_days_format));
        }
        for ($i = 0; $i < sizeof($response_arr["personnel"]); $i++) {
            if (isset($response_arr["personnel"][$i]["tck_no"])) {
                $response_arr["personnel"][$i]["name"] = TurkishChar::tr_camel($response_arr["personnel"][$i]["name"]);
            } else {
                $response_arr["personnel"][$i]["name"] = TurkishChar::tr_up($response_arr["personnel"][$i]["name"]);
                array_push($group_indexes, $i);
            }
        }

        $j = 0;
        foreach ($all_personnel as $per) {
            $report_meal_type = [];
            $breakfast_total = 0;
            $lunch_total = 0;
            $supper_total = 0;

            $breakfast_cost = 0.0;
            $lunch_cost = 0.0;
            $supper_cost = 0.0;

            $meal_cost_total = 0.0;

            for ($x = $total_date; $x >= 0; $x--) {
                $rep_date = Carbon::parse($request->get('end_date'))->subDays($x)->toDateString();
                $rep = $site->report()->ofDate($rep_date)->first();
                $meal_type = '-';
                if (!is_null($rep)) {
                    if (!is_null($site->mealcost()->first())) {
                        $breakfast_cost = (double)$site->mealcost()->sinceDate($rep_date)->first()->breakfast;
                        $lunch_cost = (double)$site->mealcost()->sinceDate($rep_date)->first()->lunch;
                        $supper_cost = (double)$site->mealcost()->sinceDate($rep_date)->first()->supper;
                    }
                    if (!($rep->meal()->ofPersonnel($per->id)->get()->isEmpty())) {
                        $meal = $rep->meal()->ofPersonnel($per->id)->first();
                        if ((int)$meal->meal % 2 == 1) {
                            $meal_type = str_replace('-', '', $meal_type);
                            $meal_type .= 'K';
                            $meal_cost_total += $breakfast_cost;
                            $breakfast_total++;
                        }
                        if ((int)$meal->meal != 4 && (int)$meal->meal / 2 >= 1) {
                            $meal_type = str_replace('-', '', $meal_type);
                            $meal_type .= 'Ö';
                            $meal_cost_total += $lunch_cost;
                            $lunch_total++;
                        }
                        if ((int)$meal->meal >= 4) {
                            $meal_type = str_replace('-', '', $meal_type);
                            $meal_type .= 'A';
                            $meal_cost_total += $supper_cost;
                            $supper_total++;
                        }

                    }
                    if (strpos($meal_type, 'KÖA') !== false) {
                        $meal_type = 'F';
                    }

                    array_push($report_meal_type, $meal_type);
                } else {
                    array_push($report_meal_type, 'ÇY');
                }
            }

            for ($i = $j; $i < sizeof($response_arr["personnel"]); $i++, $j++) {
                if (isset($response_arr["personnel"][$i]["id"]) && $response_arr["personnel"][$i]["id"] == $per->id) {
                    $response_arr["personnel"][$i]["type"] = $report_meal_type;
                    $response_arr["personnel"][$i]["cost"] = $meal_cost_total;
                    $response_arr["personnel"][$i]["meal_total"] = "$breakfast_total/$lunch_total/$supper_total";
                    break;
                }
            }
        }
        $response_arr["days"] = $days;
        $response_arr["weekends"] = $weekends;
        for ($i = 0; $i < sizeof($group_indexes); $i++) {
            $meal_cost_total = 0;
            $breakfast_total = 0;
            $lunch_total = 0;
            $supper_total = 0;

            for ($j = $group_indexes[$i] + 1; $j < sizeof($response_arr["personnel"]); $j++) {
                if ($i + 1 < sizeof($group_indexes) && $group_indexes[$i + 1] == $j) {
                    break;
                }
                $meal_cost_total += $response_arr["personnel"][$j]["cost"];
                $meal_totals = explode("/", $response_arr["personnel"][$j]["meal_total"]);
                $breakfast_total += (int)$meal_totals[0];
                $lunch_total += (int)$meal_totals[1];
                $supper_total += (int)$meal_totals[2];
            }
            for ($k = 0; $k < sizeof($days); $k++) {
                $response_arr["personnel"][$group_indexes[$i]]["type"][$k] = "";
            }
            $response_arr["personnel"][$group_indexes[$i]]["cost"] = $meal_cost_total;
            $response_arr["personnel"][$group_indexes[$i]]["meal_total"] = "$breakfast_total/$lunch_total/$supper_total";
        }

        for ($j = 0; $j < sizeof($response_arr["personnel"]); $j++) {
            $response_arr["personnel"][$j]["cost"] = number_format($response_arr["personnel"][$j]["cost"], 2, ',', '.') . "TL";
        }
        return response()->json($response_arr, 200);
    }

    public function getMealcosts(Site $site)
    {
        $mc = Mealcost::where('site_id', $site->id)->orderBy('since', 'DESC')->take(10)->get();
        $mc_arr = $mc->toArray();
        for ($i = 0; $i < sizeof($mc_arr); $i++) {
            $mc_arr[$i]['since'] = CarbonHelper::getTurkishDate($mc_arr[$i]['since']);
        }
        return response()->json($mc_arr, 200);
    }

    public function postDeleteMealcosts(Request $request)
    {
        $mc_arr = $request->get("meal");
        $mc_arr['since'] = CarbonHelper::getMySQLDate($mc_arr['since']);
        $mc = Mealcost::find($mc_arr)->first();
        $mc->delete();
        return response('success', 200);
    }

    /**
     * Alt yüklenici personel düzenle page
     */

    public function getPersonelDuzenle(Site $site, Module $modules, Subcontractor $subcontractor, Personnel $personnel)
    {
        $user = Auth::user();
        $can_edit_personnel = false;

        if ($user->isAdmin()) {
            $can_edit_personnel = true;
        } else
            foreach ($user->group()->get() as $group) {
                if ($group->hasSpecialPermissionForSlug('personel-duzenle')) {
                    $can_edit_personnel = true;
                }
            }

        if ($can_edit_personnel || !empty($site->subcontractor()->whereId($subcontractor->id)->first())) {
            if (!empty($subcontractor->personnel()->whereId($personnel->id)->first()))
                return view('tekil.personnel-edit', compact('personnel', 'site', 'modules'));
        }
        return redirect()->back();
    }

    public function postModifyPersonnel(Request $request)
    {
        $per_arr = $request->all();

        unset($per_arr["_token"]);
        unset($per_arr["wage"]);
        unset($per_arr["exit_date"]);
        unset($per_arr["id"]);
        unset($per_arr["iddoc"]);
        unset($per_arr["contract"]);
        unset($per_arr["exp_date"]);
        unset($per_arr["price"]);
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

        $success = true;
        $directory = public_path() . '/uploads/' . uniqid(rand(), true);
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

    public function postDelPersonnel(Request $request)
    {
        Personnel::find($request->get('userDeleteIn'))->delete();
        Session::flash('flash_message', 'Personel silindi');
        return redirect()->back();
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

    // End of alt yüklenici personel düzenle page

    /**
     * Ertesi gün notları page
     */
    public function getErtesiGunNotlari(Site $site, Module $modules)
    {
        return view('tekil.notes', compact('site', 'modules'));
    }

    /**
     * End of ertesi gün notları
     */

    /**
     * İş ilerleme
     */

    public function getIsIlerleme(Site $site, Module $modules)
    {
        return view('tekil.progress', compact('site', 'modules'));
    }

    public function postProgress(Site $site, Request $request)
    {
        $reports = $site->report()->dateRange($request->get("start_date"), $request->get("end_date"))->orderBy('created_at', 'DESC')->get();
        $response_arr["staff"] = [];
        foreach ($reports as $rep) {
            $my_arr = [];
            foreach ($rep->pwunit()->get() as $pw) {
                $my_arr["date"] = CarbonHelper::getTurkishDate($pw->created_at);
                $my_arr["name"] = $pw->staff->staff;
                $my_arr["quantity"] = $pw->quantity;
                $my_arr["works_done"] = $pw->works_done;
                $my_arr["unit"] = $pw->unit;
                $my_arr["planned"] = $pw->planned;
                $my_arr["done"] = $pw->done;
            }
            if (!empty($my_arr))
                array_push($response_arr["staff"], $my_arr);
            $my_arr = [];
            foreach ($rep->swunit()->get() as $sw) {
                $my_arr["date"] = CarbonHelper::getTurkishDate($sw->created_at);
                $my_arr["name"] = $sw->subcontractor->subdetail->name;
                $my_arr["quantity"] = $sw->quantity;
                $my_arr["works_done"] = $sw->works_done;
                $my_arr["unit"] = $sw->unit;
                $my_arr["planned"] = $sw->planned;
                $my_arr["done"] = $sw->done;
            }
            if (!empty($my_arr))
                array_push($response_arr["staff"], $my_arr);
        }

        return response()->json($response_arr);
    }

    //End of iş ilerleme

    /**
     * GELEN MALZEME PAGE
     */

    public function getGelenMalzeme(Site $site, Module $modules)
    {
        return view('tekil.inmaterials', compact('site', 'modules'));
    }

    public function postRetrieveInmaterials(Site $site, Request $request)
    {
        if (empty($request->get("start_date"))) {
            $reports = $site->report()->orderBy('created_at', 'DESC')
                ->with('inmaterial')->get();
        } else {
            $reports = $site->report()
                ->dateRange($request->get("start_date"), $request->get("end_date"))
                ->orderBy('created_at', 'DESC')->with('inmaterial')->get();
        }
        $response_arr["incomingmat"] = [];
        foreach ($reports as $report) {
            foreach ($report->inmaterial()->get() as $inmat) {
                $inmat_arr = [
                    'date' => CarbonHelper::getTurkishDate($inmat->created_at),
                    'id' => !is_null($inmat->demand_id) ? $inmat->demand_id : '-',
                    'name' => $inmat->material->material,
                    'unit' => $inmat->unit,
                    'quantity' => $inmat->quantity,
                    'firm' => $inmat->coming_from,
                    'explanation' => $inmat->explanation,
                    'irsaliye' => $inmat->irsaliye,
                ];
                array_push($response_arr["incomingmat"], $inmat_arr);
            }
        }

        return response()->json($response_arr);
    }

    //End of gelen malzeme

    /**
     * GİDEN MALZEME PAGE
     */

    public function getGidenMalzeme(Site $site, Module $modules)
    {
        return view('tekil.outmaterials', compact('site', 'modules'));
    }

    public function postRetrieveOutmaterials(Site $site, Request $request)
    {
        if (empty($request->get("start_date"))) {
            $reports = $site->report()->orderBy('created_at', 'DESC')
                ->with('outmaterial')->get();
        } else {
            $reports = $site->report()
                ->dateRange($request->get("start_date"), $request->get("end_date"))
                ->orderBy('created_at', 'DESC')->with('outmaterial')->get();
        }
        $response_arr["incomingmat"] = [];
        foreach ($reports as $report) {
            foreach ($report->outmaterial()->get() as $inmat) {
                $inmat_arr = [
                    'date' => CarbonHelper::getTurkishDate($inmat->created_at),
                    'id' => !is_null($inmat->demand_id) ? $inmat->demand_id : '-',
                    'name' => $inmat->material->material,
                    'unit' => $inmat->unit,
                    'quantity' => $inmat->quantity,
                    'firm' => $inmat->coming_from,
                    'explanation' => $inmat->explanation,
                    'irsaliye' => $inmat->irsaliye,
                ];
                array_push($response_arr["incomingmat"], $inmat_arr);
            }
        }

        return response()->json($response_arr);
    }

    //End of gelen malzeme

    /**
     * Hakedişler
     */

    public function getHakedisler(Site $site, Module $modules)
    {
        return view('tekil.allowance', compact('site', 'modules'));
    }

    public function postAddHakedis(Site $site, Request $request)
    {
        $all_arr = $request->all();
        unset($all_arr["_token"]);
        $all_arr["allowance_date"] = CarbonHelper::getMySQLDate($request->get("allowance_date"));
        $allowance = new Allowance();
        foreach ($all_arr as $k => $v) {
            $allowance->$k = $v;
        }
        $allowance->site()->associate($site);
        $allowance->save();

        return response(['id' => $allowance->id], 200);
    }

    public function postRetrieveHakedis(Site $site)
    {
        $response_arr = ["hakedis" => [],
            "total" => 0.0
        ];
        foreach ($site->allowance()->get() as $allowance) {
            $my_arr = [
                'no' => $allowance->no,
                'date' => CarbonHelper::getTurkishDate($allowance->allowance_date),
                'amount' => $allowance->amount,
                'detail' => $allowance->detail,
                'id' => $allowance->id
            ];
            $response_arr["total"] += $my_arr["amount"];
            $my_arr["amount"] = str_replace('.', ',', $my_arr["amount"]);
            array_push($response_arr["hakedis"], $my_arr);
        }
        $response_arr["left"] = (double)$site->contract_worth - (double)$response_arr["total"];
        return response()->json($response_arr, 200);
    }

    public function postDelHakedis(Request $request)
    {
        Allowance::find($request->get("id"))->delete();
        return response('success', 200);
    }

    // End of Hakedişler

    /**
     * Bağlantı malzeme takip
     */
    public function getBaglantiMalzemeTakip(Site $site, Module $modules)
    {
        if (session()->has("material_array")) {
            $material_array = session("material_array");
            return view('tekil.material-inventory', compact('site', 'modules', 'material_array'));
        } else
            return view('tekil.material-inventory', compact('site', 'modules'));
    }

    public function postRetrieveSubmaterials(Request $request)
    {
        $mat = Material::find($request->get("mid"));
        $sub_arr = [];
        foreach ($mat->submaterial()->bare()->get() as $sub) {
            array_push($sub_arr,
                [
                    'id' => $sub->id,
                    'text' => $sub->name,
                ]);
        }
        return response()->json($sub_arr, 200);
    }

    public function postDemandSubmaterials(Request $request, Site $site)
    {
        if (!$request->has("submaterials") && !$request->has('smfeatures')) {
            Session::flash('flash_message_error', "İlgili bağlantı malzemelerini eklemeniz gerekmekte!");
            return redirect()->back();
        }
        $mat_id = $request->has("submaterials") ? $request->get("submaterials")[0] : $request->get("smfeatures")[0];
        $mat = Submaterial::find($mat_id);
        if (!$request->has("update")) {
            if (!$mat->material->smdemand->where('site_id', $site->id)->isEmpty()) {
                Session::flash('flash_message_error', $mat->material->material . " malzemesine ait talep bulunmaktadır. Lütfen ilgili talebi güncelleyiniz.");
                return redirect()->back();
            }
        }
        if ($mat->material->feature->isEmpty()) {
            if ($request->has("submaterials")) {
                $material_array = [
                    'submat' => $request->get("submaterials"),
                ];
            } elseif ($request->has("smfeatures")) {
                $material_array = [
                    'submat' => $request->get("smfeatures")
                ];
            }
        } else {
            $material_array = [
                'submat' => $request->get("submaterials"),
                'featured' => $request->get("smfeatures")
            ];
        }
        return redirect()->back()->with("material_array", $material_array);
    }

    public function postCreateSmdemand(Request $request, Site $site)
    {
        $mat = Submaterial::find($request->get("submaterials")[0])->material;
        $smdemand = new Smdemand;
        $smdemand->contract_cost = TurkishChar::convertCurrencyFromTr($request->get("contract_cost"));
        $smdemand->site()->associate($site);
        $smdemand->material()->associate($mat);
        $smdemand->save();

        for ($i = 0; $i < sizeof($request->get("submaterials")); $i++) {
            $submat = Submaterial::find($request->get("submaterials")[$i]);
            $submat->smdemand()->save($smdemand, [
                'unit' => TurkishChar::tr_up($request->get("unit")[$i]),
                'quantity' => TurkishChar::convertCurrencyFromTr($request->get("quantity")[$i]),
            ]);
            $pricesmds = new Pricesmd();
            $pricesmds->price = TurkishChar::convertCurrencyFromTr($request->get("price")[$i]);
            $pricesmds->submaterial()->associate($submat);
            $pricesmds->smdemand()->associate($smdemand);
            $pricesmds->save();
        }

//        create new submaterial with is_sm = 0
        $smfeatured_arr = $request->get("smfeatured");
        for ($i = 0; $i < sizeof($smfeatured_arr); $i++) {
            $feature_names = '';
            $feature_arr = $request->get("sm-cb-" . $request->get("smfeatured")[$i]);
            for ($j = 0; $j < sizeof($feature_arr); $j++) {
                if ($j == 0) {
                    $feature_names = Submaterial::find($smfeatured_arr[$i])->name . "(";
                }
                $feature_names .= Feature::find($feature_arr[$j])->name . ($j + 1 != sizeof($feature_arr) ? " - " : ")");
            }
            $submat = new Submaterial();
            $submat->name = $feature_names;
            $submat->is_sm = '0';
            $submat->material()->associate($mat);

            for ($j = 0; $j < sizeof($feature_arr); $j++) {
                $feature = Feature::find($feature_arr[$j]);
                $feature->submaterial()->save($submat);
            }
            $submat->smdemand()->save($smdemand, [
                'unit' => TurkishChar::tr_up($request->get("smfeatured-unit")[$i]),
                'quantity' => TurkishChar::convertCurrencyFromTr($request->get("smfeatured-quantity")[$i]),
            ]);
            $submat->save();
            $pricesmds = new Pricesmd();
            $pricesmds->price = TurkishChar::convertCurrencyFromTr($request->get("price")[$i]);
            $pricesmds->submaterial()->associate($submat);
            $pricesmds->smdemand()->associate($smdemand);
            $pricesmds->save();
        }
        Session::flash('flash_message', 'Bağlantı malzeme talebi oluşturuldu');

        return redirect()->back();
    }

    public function postRetrieveSmdemands(Site $site)
    {
        $response_arr = [];
        foreach ($site->smdemand()->get() as $demand) {
            array_push($response_arr, [
                'id' => $demand->id,
                'matName' => $demand->material->material,
            ]);
        }
        return response()->json($response_arr, 200);
    }

    public function postRetrieveSubmaterialsFromSmdemands(Request $request)
    {
        $smdemand = Smdemand::find($request->get("id"));
        $sub_arr = ['contract_cost' => $smdemand->contract_cost,
            'submats' => []];
        foreach ($smdemand->submaterial()->orderBy('id', 'ASC')->get() as $sub) {

            array_push($sub_arr['submats'],
                [
                    'id' => $sub->id,
                    'name' => $sub->name,
                    'price' => !empty($smdemand->pricesmd()->ofSm($sub->id)->first()) ? $smdemand->pricesmd()->ofSm($sub->id)->first()->price : 0.0,
                    'quantity' => $sub->pivot->quantity,
                ]);

        }
        return response()->json($sub_arr, 200);
    }

    public function postAddSmdexpense(Request $request)
    {

        $sub = Submaterial::find($request->get('subid'));
        $smd = Smdemand::find($request->get('smdid'));
        $ddate = CarbonHelper::getMySQLDate($request->get('delivery_date'));
        $exp_arr = $request->get("bill");
        for ($i = 0; $i < sizeof($exp_arr); $i++) {
            if ((int)$request->get("bill")[$i] != -999) {
                $smdexp = new Smdexpense;
                $smdexp->quantity = TurkishChar::convertCurrencyFromTr($request->get("quantity")[$i]);
                $smdexp->delivery_date = $ddate;
                $smdexp->bill = $request->get("bill")[$i];
                $smdexp->detail = sizeof($exp_arr) == 1 ? (sizeof($request->detail) == 0 ? '' : $request->get('detail')[$i]) : $request->get('detail')[$i];
                $smdexp->submaterial()->associate($sub);
                $smdexp->smdemand()->associate($smd);
                $smdexp->save();
            }
        }

        return response('success', 200);
    }

    public function postRetrieveSmdexpenses(Request $request)
    {
        $smdemand = Smdemand::find($request->get("id"));
        $expenses = $smdemand->smdexpense()->orderBy('delivery_date', 'DESC')->orderBy('created_at', 'DESC')->get();
        $response_arr = [
            'smdexpense' => [],
            'contract_cost' => $smdemand->contract_cost,
            'total_spent' => 0.0];
        $i = -1;
        $submat_checker = [];
        $total_spent = 0.0;

        foreach ($expenses as $expense) {
            if (count($smdemand->pricesmd()->ofSm($expense->submaterial->id)->first())) {
                $price = $smdemand->pricesmd()->ofSm($expense->submaterial->id)->beforeDD($expense->delivery_date)->price;
                $spent = (double)$expense->quantity * (double)$price;
                $total_spent += $spent;
                $my_arr = [
                    'date' => CarbonHelper::getTurkishDate($expense->delivery_date),
                    'quantity' => $expense->quantity,
                    'bill' => $expense->bill,
                    'detail' => $expense->detail,
                    'spent' => $spent,
                    'subname' => $expense->submaterial->name,
                    'id' => $expense->id,
                    'price' => $price,
                    'subid' => $expense->submaterial->id,
                ];

                array_push($response_arr['smdexpense'], $my_arr);
            }
        }
        $submaterials = $smdemand->submaterial()->orderBy('id', 'ASC')->get();
        $i = 0;
        foreach ($submaterials as $sub) {
            $submat_spent = 0.0;
            if (!is_null($smdemand->smdexpense()->where('submaterial_id', '=', $sub->id)->first())) {
                foreach ($smdemand->smdexpense()->where('submaterial_id', '=', $sub->id)->get() as $expense) {
                    $submat_spent += (double)$expense->quantity;
                }
            }
            $response_arr["submat_spent"][$i] = $submat_spent;
            $i++;
        }

        $response_arr['total_spent'] = $total_spent;
        return response($response_arr, 200);
    }

    public function postDelSmdexpense(Request $request)
    {
        Smdexpense::find($request->get("id"))->delete();
        return response('success', 200);
    }

    public function postDelSmdemand(Request $request)
    {
        Smdemand::find($request->get("id"))->delete();
        Session::flash('flash_message', 'Kaldırma işlemi başarılı');
        return redirect()->back();
    }

    public function getBaglantiMalzemeDuzenle(Site $site, Module $modules, Smdemand $smdemand)
    {
        if (session()->has("material_array")) {
            $material_array = session("material_array");
            return view('tekil.smdemand-edit', compact('site', 'modules', 'material_array', 'smdemand'));
        } else
            return view('tekil.smdemand-edit', compact('site', 'modules', 'smdemand'));
    }

    public function postDelSubmaterial(Request $request)
    {
        $smd = Smdemand::find($request->get('smdid'));
        $sm = Submaterial::find($request->get('id'));
        $sm->smdemand()->detach($smd);
        return response('success', 200);
    }

    public function postUpdateSmdemand(Request $request, Site $site)
    {
        $mat = Submaterial::find($request->get("submaterials")[0])->material;
        $smdemand = Smdemand::find($request->get("smdid"));
        $smdemand->contract_cost = TurkishChar::convertCurrencyFromTr($request->get("contract_cost"));
        $smdemand->site()->associate($site);
        $smdemand->save();

        for ($i = 0; $i < sizeof($request->get("submaterials")); $i++) {
            $submat = Submaterial::find($request->get("submaterials")[$i]);
            if ($smdemand->submaterial()->where('submaterial_id', $submat->id)->get()->isEmpty()) {
                $submat->smdemand()->save($smdemand, [
                    'unit' => TurkishChar::tr_up($request->get("unit")[$i]),
                    'quantity' => TurkishChar::convertCurrencyFromTr($request->get("quantity")[$i]),
                ]);
                $pricesmds = new Pricesmd();
                $pricesmds->price = TurkishChar::convertCurrencyFromTr($request->get("price")[$i]);
                $pricesmds->submaterial()->associate($submat);
                $pricesmds->smdemand()->associate($smdemand);
                $pricesmds->save();
            } else {
                $sm = $smdemand->submaterial()->where('submaterial_id', $submat->id)->first();
                $sm->pivot->unit = TurkishChar::tr_up($request->get("unit")[$i]);
                $sm->pivot->quantity = TurkishChar::convertCurrencyFromTr($request->get("quantity")[$i]);
                $sm->pivot->save();
            }
        }

//        create new submaterial with is_sm = 0
        $smfeatured_arr = $request->get("smfeatured");
        for ($i = 0; $i < sizeof($smfeatured_arr); $i++) {
            $feature_names = '';
            $feature_arr = $request->get("sm-cb-" . $request->get("smfeatured")[$i]);
            for ($j = 0; $j < sizeof($feature_arr); $j++) {
                if ($j == 0) {
                    $feature_names = Submaterial::find($smfeatured_arr[$i])->name . "(";
                }
                $feature_names .= Feature::find($feature_arr[$j])->name . ($j + 1 != sizeof($feature_arr) ? " - " : ")");
            }
            $submat = new Submaterial();
            $submat->name = $feature_names;
            $submat->is_sm = '0';
            $submat->material()->associate($mat);

            for ($j = 0; $j < sizeof($feature_arr); $j++) {
                $feature = Feature::find($feature_arr[$j]);
                $feature->submaterial()->save($submat);
            }
            $submat->smdemand()->save($smdemand, [
                'unit' => TurkishChar::tr_up($request->get("smfeatured-unit")[$i]),
                'quantity' => TurkishChar::convertCurrencyFromTr($request->get("smfeatured-quantity")[$i]),
            ]);
            $submat->save();
            $pricesmds = new Pricesmd();
            $pricesmds->price = TurkishChar::convertCurrencyFromTr($request->get("smfeatured-price")[$i]);
            $pricesmds->submaterial()->associate($submat);
            $pricesmds->smdemand()->associate($smdemand);
            $pricesmds->save();
        }
        Session::flash('flash_message', 'Bağlantı malzeme talebi güncellendi');

        return redirect()->back();
    }

    public function postRetrievePricesmds(Request $request)
    {
        $smdemand = Smdemand::find($request->smdID);
        $resp_arr = [];
        foreach ($smdemand->pricesmd()->orderBy('since', 'DESC')->get() as $psmd) {
            $my_arr = [
                'id' => $psmd->id,
                'subid' => $psmd->submaterial->id,
                'submat' => $psmd->submaterial->name,
                'price' => $psmd->price,
                'since' => strpos($psmd->since, '1970-01-01') !== false ? 'İlk değer' : CarbonHelper::getTurkishDate($psmd->since)
            ];
            array_push($resp_arr, $my_arr);
        }
        return response()->json($resp_arr, 200);
    }

    public function postAddPricesmd(Request $request)
    {
        $smd = Smdemand::find($request->smdID);
        $pricesmd = new Pricesmd();
        $pricesmd->since = CarbonHelper::getMySQLDate($request->since);
        $pricesmd->price = $request->price;
        $pricesmd->smdemand()->associate($smd);
        $pricesmd->submaterial()->associate(Submaterial::find($request->smid));
        $pricesmd->save();

        return response('success', 200);
    }

    public function postDelPricesmd(Request $request)
    {
        Pricesmd::find($request->id)->delete();
        return response('success', 200);
    }

    // End of Bağlantı malzeme takip

    /**
     *
     * DEMİRBAŞ
     *
     */

    public function getDemirbas(Site $site, Module $modules)
    {
        if (session()->has("stock_array")) {
            $stock_array = session("stock_array");
            return view('tekil.stock', compact('site', 'modules', 'stock_array'));
        } else
            return view('tekil.stock', compact('site', 'modules'));
    }

    public function getRetrieveStocks()
    {
        $sub_arr = [];
        foreach (Stock::all() as $sub) {
            array_push($sub_arr,
                [
                    'id' => $sub->id,
                    'text' => $sub->name,
                ]);
        }
        return response()->json($sub_arr, 200);
    }

    public function postPreregisterStocks(Request $request)
    {
        $stock_array = [];
        foreach ($request->stocks as $stock_id) {
            $stock = Stock::find($stock_id);
            $total = $stock->total;
            $left = $total;
            foreach (Site::all() as $st) {
                if (!empty($st->stock()->find($stock->id))) {
                    $left -= $st->stock()->find($stock->id)->pivot->amount;
                }
            }
            array_push($stock_array, [
                    'stock' => $stock->id,
                    'left' => $left
                ]
            );
        }
        return redirect()->back()->with('stock_array', $stock_array);
    }

    public function postRegisterStocks(Request $request, Site $site)
    {
        $arr_size = sizeof($request->stocks);
        $stock_array = [];
        $over_book = false;
        for ($i = 0; $i < $arr_size; $i++) {
            $stock = Stock::find($request->stocks[$i]);
            $total = $stock->total;
            $left = $total;
            foreach (Site::all() as $st) {
                if (!empty($st->stock()->find($stock->id))) {
                    $left -= $st->stock()->find($stock->id)->pivot->amount;
                }
            }
            $over_request = $request->quantity[$i] > $left ? true : false;
            if (!$over_book && $over_request) {
                $over_book = true;
            }
            if (!$over_request) {
                $site->stock()->attach($stock, [
                    'amount' => $request->quantity[$i],
                    'entry_date' => CarbonHelper::getMySQLDate($request->entry_date[$i]),
                    'exit_date' => CarbonHelper::getMySQLDate($request->exit_date[$i]),
                    'site_detail' => $request->site_detail[$i],
                ]);
                $site->save();
            } else {
                array_push($stock_array, [
                    'stock' => $request->stocks[$i],
                    'left' => $left
                ]);
            }
        }
        if ($over_book) {
            Session::flash('flash_message_error', 'Listelenmiş demirbaşların miktar bilgilerini tekrar girmeniz gerekmekte!');
            return redirect()->back()->with('stock_array', $stock_array);
        }
        Session::flash('flash_message', 'Demirbaş kaydı başarılı');
        return redirect()->back();
    }

    public function postModifyStockAmount(Request $request)
    {
        $stock_table = DB::table('site_stock')->where('id', $request->pk)->first();
        $stock = Stock::find($stock_table->stock_id);
        $left = $stock->total;
        foreach (DB::table('site_stock')->where('stock_id', $stock->id)->get() as $st) {
            $left -= $st->amount;
        }

        if ($left < $request->value) {
            $max = $left + $stock_table->amount;
            return response("Demirbaş miktarı en fazla $max $stock->unit olabilir!", 500);
        }
        DB::table('site_stock')->where('id', $request->pk)->update(['amount' => $request->value]);
        return response('success', 200);
    }

    public function postModifyStockEntryDate(Request $request)
    {
        DB::table('site_stock')->where('id', $request->pk)->update(['entry_date' => $request->value]);
        return response('success', 200);
    }

    public function postModifyStockExitDate(Request $request)
    {
        DB::table('site_stock')->where('id', $request->pk)->update(['exit_date' => $request->value]);
        return response('success', 200);
    }

    public function postModifyStockSiteDetail(Request $request)
    {
        DB::table('site_stock')->where('id', $request->pk)->update(['site_detail' => $request->value]);
        return response('success', 200);
    }

    public function postDelSiteStock(Request $request)
    {
        DB::table('site_stock')->where('id', $request->id)->delete();
        Session::flash('flash_message', 'Silme işlemi başarılı');
        return redirect()->back();
    }

    //End of Demirbaş

    /**
     * ŞANTİYE EKLERİ
     */

    public function getSantiyeEkleri(Site $site, Module $modules)
    {
        return view('tekil.attachment', compact('site', 'modules'));
    }

    public function getRetrievePhotos(Site $site)
    {
        $site_reports = $site->report()->with('photo')->get();
        $resp_arr = [];
        foreach ($site_reports as $site_report)
            foreach ($site_report->photo as $site_photo)
                if (count($site_photo->file)) {
                    $my_path_arr = explode(DIRECTORY_SEPARATOR, $site_photo->file()->first()->path);
                    $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1];
                    $image = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $site_photo->file()->first()->name;
                    if (strpos($site_photo->file()->first()->name, 'pdf') !== false) {
                        $image = URL::to('/') . "/img/pdf.jpg";
                    } elseif (strpos($site_photo->file()->first()->name, 'doc') !== false) {
                        $image = URL::to('/') . "/img/word.png";
                    }
                    $tags = '';
                    $i = 0;
                    foreach ($site_photo->file()->first()->tag as $tag) {
                        $tags .= $tag->name;
                        if ($i + 1 < count($site_photo->file()->first()->tag)) {
                            $tags .= ', ';
                        }
                        $i++;
                    }
                    array_push($resp_arr, [
                        'name' => $site_photo->file()->first()->name,
                        'image' => $image,
                        'tags' => $tags
                    ]);
                }

        return response($resp_arr, 200);

    }

    public function getRetrieveReceipts(Site $site)
    {
        $site_reports = $site->report()->with('receipt')->get();
        $resp_arr = [];
        foreach ($site_reports as $site_report)
            foreach ($site_report->receipt as $site_receipt)
                if (count($site_receipt->file)) {
                    $my_path_arr = explode(DIRECTORY_SEPARATOR, $site_receipt->file()->first()->path);
                    $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1];
                    $image = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $site_receipt->file()->first()->name;
                    if (strpos($site_receipt->file()->first()->name, 'pdf') !== false) {
                        $image = URL::to('/') . "/img/pdf.jpg";
                    } elseif (strpos($site_receipt->file()->first()->name, 'doc') !== false) {
                        $image = URL::to('/') . "/img/word.png";
                    }
                    $tags = '';
                    $i = 0;
                    foreach ($site_receipt->file()->first()->tag as $tag) {
                        $tags .= $tag->name;
                        if ($i + 1 < count($site_receipt->file()->first()->tag)) {
                            $tags .= ', ';
                        }
                        $i++;
                    }
                    array_push($resp_arr, [
                        'name' => $site_receipt->file()->first()->name,
                        'image' => $image,
                        'tags' => $tags
                    ]);
                }

        return response($resp_arr, 200);

    }

    // End of Şantiye Ekleri

    /**
     * Start of Maliyet Modülü
     */

    public function getIcmal(Site $site, Module $modules)
    {
        return view('tekil.summary', compact('site', 'modules'));
    }

    public function getRetrieveSummary(Site $site)
    {
        $resp_arr = [];
        $grand_total = 0.0;
        for ($i = 1; $i < 5; $i++) {
            $key = 'genel';
            switch ($i) {
                case 2:
                    $key = 'sozlesme';
                    break;
                case 3:
                    $key = 'sarf';
                    break;
                case 4:
                    $key = 'insaat';
                    break;
                default:
                    break;
            }
            $exp_sum = (double)$site->expenditure()->ofType($i)->sum('amount');
            $grand_total += $exp_sum;
            $resp_arr[$key] = $exp_sum;
        }

        $sub_payments = 0;
        foreach ($site->subcontractor()->with('payment')->get() as $subcontractor) {
            $sub_payments += (double)$subcontractor->payment()->sum('amount');
        }

        $grand_total += $sub_payments;
        $resp_arr['taseron'] = $sub_payments;

        $resp_arr['iscilik'] = $site->labor()->sum('amount');
        $grand_total += $resp_arr['iscilik'];
        $personnel_salary = 0;
        foreach ($this->getSalary($site) as $salary) {
            $personnel_salary += $salary;
        }

        $resp_arr['personel'] = $personnel_salary;
        $grand_total += $resp_arr['personel'];
        $resp_arr['total'] = $grand_total;

//        calculate percentages
        $resp_arr['genel_yuzde'] = $grand_total == 0 ? 0 : $resp_arr['genel'] * 100 / $grand_total;
        $resp_arr['sozlesme_yuzde'] = $grand_total == 0 ? 0 : $resp_arr['sozlesme'] * 100 / $grand_total;
        $resp_arr['sarf_yuzde'] = $grand_total == 0 ? 0 : $resp_arr['sarf'] * 100 / $grand_total;
        $resp_arr['insaat_yuzde'] = $grand_total == 0 ? 0 : $resp_arr['insaat'] * 100 / $grand_total;
        $resp_arr['taseron_yuzde'] = $grand_total == 0 ? 0 : $resp_arr['taseron'] * 100 / $grand_total;
        $resp_arr['iscilik_yuzde'] = $grand_total == 0 ? 0 : $resp_arr['iscilik'] * 100 / $grand_total;
        $resp_arr['personel_yuzde'] = $grand_total == 0 ? 0 : $resp_arr['personel'] * 100 / $grand_total;

        $resp_arr['kdv0'] = $this->calcKDV($site, 0);
        $resp_arr['kdv1'] = $this->calcKDV($site, 1);
        $resp_arr['kdv8'] = $this->calcKDV($site, 8);
        $resp_arr['kdv18'] = $this->calcKDV($site, 18);

        $resp_arr['grand'] = $grand_total + (double)$resp_arr['kdv1'] + (double)$resp_arr['kdv8'] + (double)$resp_arr['kdv18'];

        return response($resp_arr, 200);
    }

    private function calcKDV($site, $kdv)
    {
        return (double)$site->expenditure()->where('kdv', '=', $kdv)->sum('grand_total') - (double)$site->expenditure()->where('kdv', '=', $kdv)->sum('amount');
    }

    private function getSalary($site)
    {
        $resp_arr = [];
        $start_date = Carbon::createFromFormat('Y-m-d', $site->start_date);
        $end_date = Carbon::createFromFormat('Y-m-d', $site->end_date);
        $now = Carbon::now('Europe/Istanbul');
        if ($now->lte($end_date)) {
            $end_date = $now;
        }
//            $resp_arr['dates'] = [];

        foreach ($site->employee()->get() as $personnel) {
            $inc_date = $start_date->copy();
            $inc_date->subMonth();
            while ($end_date->gt($inc_date)) {
                $inc_date->addMonth();
//                array_push($resp_arr['dates'], $inc_date->toDateString());
                if (count($personnel->salary()) && !($personnel->salary()->whereDate('since', '<', $inc_date->toDateString())->orderBy('since', 'DESC')->get()->isEmpty())) {
                    array_push($resp_arr, $personnel->salary()->whereDate('since', '<', $inc_date->toDateString())->orderBy('since', 'DESC')->first()->amount);
                } else array_push($resp_arr, 0);

            }
        }
        return $resp_arr;
    }

    public function getGenelGiderler(Site $site, Module $modules)
    {
        $type = 1;
        $title = 'Genel Giderler';
        return view('tekil.expenditures', compact('site', 'modules', 'type', 'title'));
    }

    public function getSozlesmeGiderleri(Site $site, Module $modules)
    {
        $type = 2;
        $title = 'Sözleşme Giderleri';
        return view('tekil.expenditures', compact('site', 'modules', 'type', 'title'));
    }

    public function getMuhtelifSarfMalzeme(Site $site, Module $modules)
    {
        $type = 3;
        $title = 'Sarf Malzeme Giderleri';
        return view('tekil.expenditures', compact('site', 'modules', 'type', 'title'));
    }

    public function getInsaatMalzeme(Site $site, Module $modules)
    {
        $type = 4;
        $title = 'İnşaat Malzeme Giderleri';
        return view('tekil.expenditures', compact('site', 'modules', 'type', 'title'));
    }

    public function postRetrieveGeneralExpItems(Request $request)
    {
        return $this->getExpItems($request->type);
    }

    public function postRetrieveGeneralExp(Site $site, Request $request)
    {
        return $this->getExpenditures($site, $request->type);
    }

    public function postAddGeneralExp(Site $site, Request $request)
    {
        return $this->addExpenditure($site, $request);
    }

    public function postDelGeneralExp(Request $request)
    {
        Expenditure::find($request->id)->delete();
        return response('success', 200);
    }

    public function postRetrieveMonthlyExp(Site $site, Request $request)
    {
        $expenditures = $site->expenditure()->ofType($request->type)->orderBy('exp_date', 'ASC')->get();
        $current_date = '';
        $previous_date = '';
        $resp_arr = ['months' => [], 'items' => [], 'month_total' => []];
        foreach ($expenditures as $expenditure) {
            $current_date = Carbon::parse($expenditure->exp_date)->format('m.Y');
            if ($current_date != $previous_date) {
                array_push($resp_arr['months'], $current_date);
                $previous_date = $current_date;
            }
        }

        foreach (Expdetail::ofGroup($request->type)->get() as $expdet) {
            $name = $expdet->name;
            $expdet_arr = [];
            $grand_total = 0;
            for ($i = 0; $i < sizeof($resp_arr['months']); $i++) {
                $date = explode('.', $resp_arr['months'][$i]);
                $month = (int)$date[0];
                $year = (int)$date[1];
                $total = $site->expenditure()->ofType($request->type)->where('expdetail_id', '=', $expdet->id)
                    ->whereMonth('exp_date', '=', $month)->whereYear('exp_date', '=', $year)->sum('grand_total');
                $grand_total += $total;
                array_push($expdet_arr, $total);
            }
            if ($grand_total > 0) {
                array_push($resp_arr['items'], ['name' => $name,
                    'total' => $expdet_arr,
                    'item_total' => $grand_total]);
            }
        }
        for ($i = 0; $i < sizeof($resp_arr['months']); $i++) {
            $total = 0;
            foreach ($resp_arr['items'] as $item) {
                $total += (double)$item['total'][$i];
            }
            array_push($resp_arr['month_total'], $total);
        }
        return response($resp_arr, 200);
    }

    // Taşeron Hakedişleri
    public function getTaseronHakedisleri(Site $site, Module $modules)
    {
        return view('tekil.subcontractor-allowances', compact('site', 'modules'));
    }

    public function getRetrieveSubcontractorAllowances(Site $site)
    {
        $subcontractors = $site->subcontractor()->with('subdetail')->get();
        $resp_arr = [];
        foreach ($subcontractors as $subcontractor) {
            if (count($subcontractor->subdetail)) {
                $my_arr = $this->subcontractorPayments($subcontractor->id);
                unset($my_arr['payments']);
                array_push($resp_arr, [
                    'name' => $subcontractor->subdetail->name,
                    'debt' => $my_arr['debt'],
                    'claim' => $my_arr['claim'],
                    'balance' => $my_arr['balance']
                ]);
            }
        }
        return response($resp_arr, 200);
    }

    // Şantiye İşçilik

    public function getSantiyeIscilik(Site $site, Module $modules)
    {
        return view('tekil.labor', compact('site', 'modules'));
    }

    public function getRetrieveLaborExp(Site $site)
    {
        $resp_arr = $site->labor()->get()->toArray();
        for ($i = 0; $i < sizeof($resp_arr); $i++) {
            $resp_arr[$i]['lab_date'] = CarbonHelper::getTurkishDate($resp_arr[$i]['lab_date']);
        }
        return response($resp_arr, 200);
    }

    public function postAddLaborExp(Site $site, Request $request)
    {
        $labor_arr = $request->all();
        $labor_arr['lab_date'] = CarbonHelper::getMySQLDate($request->lab_date);
        $labor = Labor::create($labor_arr);
        $labor->site()->associate($site);
        $labor->save();

        return response('success', 200);
    }

    public function postDelLabExp(Request $request)
    {
        Labor::find($request->id)->delete();
        return response('success', 200);
    }

    // Personel Maaş
    public function getSantiyePersonelMaas(Site $site, Module $modules)
    {
        return view('tekil.salary', compact('site', 'modules'));
    }

    public function postAddEmployee(Site $site, Request $request)
    {
        $site->employee()->detach();
        foreach ($request->employee as $id) {
            $employee = Personnel::find($id);
            $site->employee()->attach($employee);
        }

        return response('success', 200);
    }

    public function getRetrieveSalaries(Site $site)
    {
        $resp_arr = ['dates' => [], 'salaries' => []];
        $start_date = Carbon::createFromFormat('Y-m-d', $site->start_date);
        $end_date = Carbon::createFromFormat('Y-m-d', $site->end_date);
        $now = Carbon::now('Europe/Istanbul');
        if ($now->lte($end_date)) {
            $end_date = $now;
        }
        $i = 0;

        foreach ($site->employee()->get() as $personnel) {
            $my_arr = ['personnel' => $personnel->name, 'salary' => []];
            $inc_date = $start_date->copy();
            $inc_date->subMonth();
            while ($end_date->gt($inc_date)) {
                $inc_date->addMonth();
                if ($i == 0) {
                    $my_str = explode('-', $inc_date->toDateString());
                    array_push($resp_arr['dates'], $my_str[1] . '.' . $my_str[0]);
                }
                if (count($personnel->salary()) && !($personnel->salary()->whereDate('since', '<', $inc_date->toDateString())->orderBy('since', 'DESC')->get()->isEmpty())) {
                    array_push($my_arr['salary'], $personnel->salary()->whereDate('since', '<', $inc_date->toDateString())->orderBy('since', 'DESC')->first()->amount);
                } else array_push($my_arr['salary'], 0);
            }
            array_push($resp_arr['salaries'], $my_arr);
            $resp_arr['monthly_tot'] = [];
            for ($i = 0; $i < sizeof($resp_arr['dates']); $i++) {
                $tot = 0;
                for ($j = 0; $j < sizeof($resp_arr['salaries']); $j++) {
                    $tot += (double)$resp_arr['salaries'][$j]['salary'][$i];
                }
                array_push($resp_arr['monthly_tot'], $tot);
            }
            $i++;
        }


        return response($resp_arr, 200);
    }

    // İŞ MAKİNELERİ SAYFASI

    public function getIsMakineleri(Site $site, Module $modules)
    {
        return view('tekil.equipments', compact('site', 'modules'));
    }

    public function getRetrieveSiteEquipments(Site $site)
    {
        $equipments = $site->equipment()->get();

        return response($equipments->toArray(), 200);
    }

    public function getRetrieveInstruments(Site $site)
    {
        $resp_arr = [];

        foreach($site->instrument()->get() as $instrument){
            array_push($resp_arr, [
               'date' => CarbonHelper::getTurkishDate($instrument->followup_date),
                'firm' => $instrument->firm,
                'id' => $instrument->id,
                'name' => $instrument->equipment->name,
                'eid' => $instrument->equipment->id,
                'plate' => $instrument->plate,
                'fuel_stat' => $instrument->fuel_stat,
                'fuel' => $instrument->fuel,
                'work' => $instrument->work,
                'unit' => $instrument->unit,
                'fee' => $instrument->fee,
                'total' => $instrument->total,
                'detail' => $instrument->detail
            ]);
        }

        return response($resp_arr, 200);
    }

    public function postAddInstrument(Site $site, Request $request)
    {
        $equipment = Equipment::find($request->eid);
        $instrument_arr = $request->all();
        unset($instrument_arr['eid']);
        $instrument_arr['fee'] = (double) str_replace(',', '.', $request->fee);
        $instrument_arr['work'] = (double) str_replace(',', '.', $request->work);
        $instrument_arr['followup_date'] = CarbonHelper::getMySQLDate($request->followup_date);
        $instrument_arr['total'] = $instrument_arr['fee'] * $instrument_arr['work'];
        $instrument = new Instrument($instrument_arr);
        $instrument->equipment()->associate($equipment);
        $instrument->site()->associate($site);
        $instrument->save();
        return response('success', 200);
    }

    public function postDelInstrument(Request $request)
    {
        $instrument = Instrument::find($request->id);
        $instrument->delete();

        return response('success', 200);
    }

    public function postEditEquipments(Site $site, Request $request)
    {
        $site->equipment()->detach();

        foreach ($request->get("equipments") as $equipment) {
            $site->equipment()->attach($equipment);
        }

        Session::flash('flash_message', "Şantiye iş makineleri güncellendi");
        return redirect()->back()->with('tab', self::SEKME_IKI);
    }

    // END OF İŞ MAKİNELERİ


    /**
     *
     * PRIVATE FUNCTIONS
     *
     */
    private function subcontractorPayments($subId)
    {
        $subcontractor = Subcontractor::find($subId);
        $resp_arr = [];
        $resp_arr['claim'] = TurkishChar::convertToTRcurrency((double)$subcontractor->price + (double)$subcontractor->additional_bid_cost);
        $debt = 0;
        $balance = 0.0 - (double)$resp_arr['claim'];
        $payments = [];
        foreach ($subcontractor->payment()->orderBy('payment_date', 'DESC')->get() as $payment) {
            $balance += (double)$payment->amount;
            $debt += (double)$payment->amount;
            array_push($payments, [
                'id' => $payment->id,
                'date' => CarbonHelper::getTurkishDate($payment->payment_date),
                'type' => $payment->name,
                'method' => empty($payment->method) ? '-' : $payment->method,
                'detail' => $payment->detail,
                'balance' => TurkishChar::convertToTRcurrency($balance),
                'debt' => TurkishChar::convertToTRcurrency((double)$payment->amount)
            ]);
        }
        $resp_arr['payments'] = $payments;
        $resp_arr['debt'] = TurkishChar::convertToTRcurrency($debt);
        $resp_arr['balance'] = TurkishChar::convertToTRcurrency($balance);
        return $resp_arr;
    }


    private function addExpenditure($site, $request)
    {
        $my_arr = $request->all();
        $expdetail = Expdetail::find($request->eid);
        unset($my_arr['eid']);
        $my_arr['exp_date'] = CarbonHelper::getMySQLDate($request->exp_date);
        $expenditure = Expenditure::create($my_arr);
        $expenditure->grand_total = (($expenditure->kdv / 100) + 1.0) * $expenditure->amount;
        $expenditure->site()->associate($site);
        $expenditure->expdetail()->associate($expdetail);
        $expenditure->save();
        return response('success', 200);
    }

    private function getExpItems($group)
    {
        $my_arr = [];
        foreach (Expdetail::where('group', '=', $group)->orderBy('name', 'ASC')->get() as $item) {
            array_push($my_arr, [
                'id' => $item->id,
                'name' => $item->name
            ]);
        }
        return response($my_arr, 200);
    }

    private function getExpenditures($site, $type)
    {
        $resp_arr = [];
        foreach ($site->expenditure()->ofType($type)->get() as $exp) {
            array_push($resp_arr, [
                'id' => $exp->id,
                'date' => CarbonHelper::getTurkishDate($exp->exp_date),
                'type' => $exp->expdetail->name,
                'explanation' => $exp->explanation,
                'expItem' => ['name' => $exp->expdetail->name, 'id' => $exp->expdetail->id],
                'amount' => $exp->amount,
                'kdv' => $exp->kdv,
                'total' => ((double)($exp->kdv / 100) + 1.0) * $exp->amount
            ]);
        }
        return $resp_arr;
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
