<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\IncidentReport;
use App\User;
use Intervention\Image\Facades\Image;
use Auth;
use Entrust;

class IncidentReportController extends Controller
{
	protected $result = [];

    public function incident_report(Request $request)
    {
        if($request->all()){
            $incident_types_array = ['work_related_illness','plant_equipment_damage','environment','electrocution','near_miss','injury'];
            $medical_treatments_array = ['mt_none','mt_first_aid','mt_doctor','mt_hospital'];
            $attended_authorities_array = ['aa_police','aa_ambulance','aa_fire','aa_workplace_h_s','aa_epa','aa_media'];

            $incident_types = [];
            $medical_treatments = [];
            $attended_authorities = [];

            $convert_to_json = ['incident_types','medical_treatments','attended_authorities'];
            foreach($convert_to_json as $part){
                foreach(${$part.'_array'} as $it){
                    if($request->{$it}){
                        ${$part}[]=$it;
                    }
                }
            }

            $request->merge([
                'user_id' => Auth::id(),
                'type'    => json_encode($incident_types),
                'medical_treatment'  => json_encode($medical_treatments),
                'attended_authorities'    => json_encode($attended_authorities)
            ]);

            $IncidentReport = IncidentReport::create($request->all());
            if($IncidentReport){
                // $report_img = $this->generate_incident_report_form($IncidentReport->id);
                // $path=public_path('/files/test.jpg');
                // $report_img->save($path,100);

                return back()->with('message','Incident Report Generated Successfully');
            }
        }
        $incident_reports = IncidentReport::where('user_id','1');

        if(Entrust::hasRole('contractor')){
        	$incident_reports->whereHas('user', function ($query){
                                $query->where('added_by', '=', Auth::id());
                             });
        }

        if(!Entrust::hasRole(['contractor','superAdmin'])){
          	$incident_reports->where('user_id',Auth::id());
        }
        $incident_reports = $incident_reports->get();


        $employer = User::where('id',Auth::user()->added_by)->pluck('name','id')->toArray();
        return view('backend.pages.incident_report',compact('employer','incident_reports'));
    }

    public function ajax_incident_report_details(Request $request)
    {
        $incident_details = IncidentReport::select(
                              'incident_reports.id',
                              'incident_reports.user_id',
                              'incident_reports.type',
                              'incident_reports.person_involved',
                              'incident_reports.occupation',
                              'incident_reports.employer_id',
                              'incident_reports.contact',
                              'incident_reports.location',
                              'incident_reports.date',
                              'incident_reports.medical_treatment',
                              'incident_reports.cease_work',
                              'incident_reports.attended_authorities',
                              'incident_reports.desc_what',
                              'incident_reports.desc_how',
                              'incident_reports.desc_why',
                              'incident_reports.desc_relevant_controls',
                              'incident_reports.desc_immediate_actions',
                              'incident_reports.created_at',
                              'reporter.name as reporter',
                              'employer.name as employer',)
                        ->join('users as reporter','incident_reports.user_id','=','reporter.id')
                        ->leftJoin('users as employer','incident_reports.employer_id','=','employer.id')
                        ->where('incident_reports.id','=',$request->incident_id)
                        ->first();
                  
        $view = view('backend.modals.render.incident_details')->with([
           'incident_details' => $incident_details ])->render();

        $response = [
           'status' => true,
           'title' => 'Incident Report Details',
           'html' => $view
        ];
       return response()->json($response);

    }

    public function print_incident_report($id)
    {
    	$report_img = $this->generate_incident_report_form($id);
    	$path=public_path('/files/test.jpg');
        $report_img->save($path,50);

        return response($report_img)->header('Content-type','image/png');

    }

    public function generate_incident_report_form($id)
    {
        $IncidentReport = IncidentReport::find($id);
        
        if($IncidentReport){
            $incident_types = json_decode($IncidentReport->type);
            $medical_treatments = json_decode($IncidentReport->medical_treatment);
            $attended_authorities = json_decode($IncidentReport->attended_authorities);
            $report = Image::make(public_path('/backend/incident_report/incident_report.jpg'));
            $sym_font_style=function($font) {
                $font->file(public_path('/backend/incident_report/times.ttf'));
                $font->size('40');
                $font->color('#322922');
            };
            $font_style=function($font) {
                $font->file(public_path('/backend/incident_report/times.ttf'));
                $font->size('22');
                $font->color('#000');
            };

            foreach($incident_types as $inc_typ){
                if($inc_typ=='work_related_illness')
                    $report->text('■', 437,352,$sym_font_style);
                if($inc_typ=='injury')
                    $report->text('■', 591,348,$sym_font_style);
                if($inc_typ=='plant_equipment_damage')
                    $report->text('■', 831,348,$sym_font_style);
                if($inc_typ=='environment')
                    $report->text('■', 1041,347,$sym_font_style);
                if($inc_typ=='electrocution')
                    $report->text('■', 1257,347,$sym_font_style);
                if($inc_typ=='near_miss')
                    $report->text('■', 1460,347,$sym_font_style);
            }
            
            $inci_date = \Carbon\Carbon::parse($IncidentReport->date)->format('M-d-Y');
            $inci_time = \Carbon\Carbon::parse($IncidentReport->date)->format('H:i'); 
            
            $report->text($IncidentReport->person_involved, 129,462,$font_style);
            $report->text($IncidentReport->occupation, 482,462,$font_style);
            $report->text($IncidentReport->employer_name, 834,462,$font_style);
            $report->text($IncidentReport->contact, 1186,462,$font_style);
            $report->text($IncidentReport->location, 366,536,$font_style);
            $report->text($inci_date, 833,536,$font_style);
            $report->text($inci_time, 1303,536,$font_style);

            //Medical Treatment
            foreach($medical_treatments as $med_typ){
                if($med_typ=='mt_none')
                    $report->text('■', 483,608,$sym_font_style);
                if($med_typ=='mt_first_aid')
                    $report->text('■', 665,611,$sym_font_style);
                if($med_typ=='mt_doctor')
                    $report->text('■', 817,608,$sym_font_style);
                if($med_typ=='mt_hospital')
                    $report->text('■', 983,608,$sym_font_style);
            }

            //Cease Work
            if($IncidentReport->cease_work=='1')
                $report->text('■', 1368,624,$sym_font_style);
            if($IncidentReport->cease_work=='0')
                $report->text('■', 1460,625,$sym_font_style);

            //Attended Authorities
            foreach($attended_authorities as $aa_typ){
                if($aa_typ=='aa_police')
                    $report->text('■', 367,693,$sym_font_style);
                if($aa_typ=='aa_ambulance')
                    $report->text('■', 527,692,$sym_font_style);
                if($aa_typ=='aa_fire')
                    $report->text('■', 742,692,$sym_font_style);
                if($aa_typ=='aa_workplace_h_s')
                    $report->text('■', 837,693,$sym_font_style);
                if($aa_typ=='aa_epa')
                    $report->text('■', 1086,692,$sym_font_style);
                if($aa_typ=='aa_media')
                    $report->text('■', 1235,692,$sym_font_style);
            }
   
            $desc_what_string =  $this->strings_seperate($IncidentReport->desc_what,'850');
            $y = 912;
            foreach($desc_what_string as $i => $string){
                $report->text($string, 243,$y,$font_style);
                $y+=30;
                unset($this->result[$i]);
            }

            $desc_how_string =  $this->strings_seperate($IncidentReport->desc_how,'850');
            $y = 1051;
            foreach($desc_how_string as $i => $string){
                $report->text($string, 227,$y,$font_style);
                $y+=30;
                unset($this->result[$i]);
            }

            $desc_why_string =  $this->strings_seperate($IncidentReport->desc_why,'850');
            $y = 1188;
            foreach($desc_why_string as $i => $string){
                $report->text($string, 222,$y,$font_style);
                $y+=33;
                unset($this->result[$i]);
            }

            $desc_immediate_actions_string =  $this->strings_seperate($IncidentReport->desc_immediate_actions,'920');
            $y = 1371;
            foreach($desc_immediate_actions_string as $i => $string){
                $report->text($string, 142,$y,$font_style);
                $y+=32;
                unset($this->result[$i]);
            }

            $desc_relevant_controls_string =  $this->strings_seperate($IncidentReport->desc_relevant_controls,'920');
            $y = 1506;
            foreach($desc_relevant_controls_string as $i => $string){
                $report->text($string, 142,$y,$font_style);
                $y+=32;
                unset($this->result[$i]);
            }

            // $path=public_path('/files/test.jpg');
            // $report->save($path,100);

            // return response($report)->header('Content-type','image/png');
            return $report;
        }

        return 'No Record found';
    }

    public function strings_seperate($string, $paper_width)
    {
        $font = public_path('/backend/incident_report/times.ttf');
        list($left,, $right) = imageftbbox( 11, 0, $font, $string);
        $width = $right - $left;
        if( $width <= $paper_width){
            $this->result[] = $string;
        }
        elseif($width>$paper_width){
            $length = strlen($string);
            for($i=0;$i<$length;$i++){
                $part1=substr($string,0,$i);
                list($left,, $right) = imageftbbox( 11, 0, $font, $part1);
                $lens = $right - $left;
                if($lens>$paper_width && $lens<($paper_width+15)){
                    $this->result[] = $part1;
                    $part2 = substr($string,$i,$length);
                    $this->strings_seperate($part2,$paper_width);
                    break;
                }
            }
        }
        return $this->result;
    }

}
