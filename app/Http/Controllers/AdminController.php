<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Session};
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // Utility function to get all participant details of an event
    private function util_getParticipantsByEventId($event_id){
        return DB::table('registration')
        ->where('event_id', $event_id)
        ->join('users', 'users.id', '=', 'registration.ticket_id')
        ->join('universities', 'universities.id', '=', 'users.university_id')
        ->select('registration.*', 'users.name', 'users.nim', 'users.email', 'users.phone', 'users.line', 'users.whatsapp', DB::raw('universities.name AS university_name'))
        ->orderBy('status', 'desc')
        ->where('users.university_id', '!=', 2)
        ->where('users.university_id', '!=', 3)
        ->get();
    }
    // Utility function to get all attending committee details of an event
    private function util_getCommitteesByEventId($event_id){
        return DB::table('registration')
        ->where('event_id', $event_id)
        ->join('users', 'users.id', '=', 'registration.ticket_id')
        ->join('universities', 'universities.id', '=', 'users.university_id')
        ->select('registration.*', 'users.name', 'users.nim', 'users.email')
        ->orderBy('status', 'desc')
        ->where('users.university_id', 2)
        ->orWhere('users.university_id', 3)
        ->get();
    }
    public function index($path){
        // Make sure that it's an Admin
        if (!Auth::check() || (Auth::user()->university_id < 2 || Auth::user()->university_id > 3)){
            Session::put('error', 'Admin: Not Authorized');
            return redirect('login');
        }

        // Switch Case
        switch ($path){
            case "events":
                return view('admin.events');
            break;
        }
    }

    public function getAllUsers(){
        // Make sure that it's an Admin
        if (!Auth::check() || (Auth::user()->university_id < 2 || Auth::user()->university_id > 3)){
            Session::put('error', 'Admin: Not Authorized');
            return redirect('login');
        }
        $users = DB::table('users')
            ->select('users.id', 'users.name', 'users.university_id', DB::raw('universities.name AS university_name'), 'users.email', 'users.verified')
            ->join('universities', 'university_id', 'universities.id')
            ->get();
        $universities = DB::table('universities')->get();
        return view('admin.users', ['users' => $users, 'universities' => $universities]);
    }

    public function postAllUsers(Request $request){
        // Make sure that it's an Admin (Higher Level)
        if (!Auth::check() || (Auth::user()->university_id != 2)){
            Session::put('error', 'Admin: Not Authorized');
            return redirect('login');
        }

        foreach($request->all() as $key => $value) {
            if (Str::startsWith($key, "status-") && $value >= 0){
                $key = substr($key, 7);
                if ($value >= 0 && $value != '' && $key != Auth::user()->id) DB::table('users')->where('id', $key)->update(['university_id' => $value]);
            }
        }
        return redirect('/admin/users');
    }

    public function getEventsList(){
        // Make sure that it's an Admin
        if (!Auth::check() || (Auth::user()->university_id < 2 || Auth::user()->university_id > 3)){
            Session::put('error', 'Admin: Not Authorized');
            return redirect('login');
        }
        return view('admin.events');
    }

    public function getEventParticipants($event_id){
        // Make sure that it's an Admin
        if (!Auth::check() || (Auth::user()->university_id < 2 || Auth::user()->university_id > 3)){
            Session::put('error', 'Admin: Not Authorized');
            return redirect('login');
        }
        // $query = "SELECT registration.*, user.name, user.email, user.phone, user.line, user.whatsapp, user.id_mobile_legends, user.id_pubg_mobile, user.id_valorant, university.name AS university_name FROM `registration` registration INNER JOIN `users` user ON registration.ticket_id = user.id INNER JOIN `universities` university ON user.university_id = university.id WHERE registration.event_id = " . $event_id;
        $registration = $this->util_getParticipantsByEventId($event_id);
        $committees = $this->util_getCommitteesByEventId($event_id);
        $event = DB::table('events')->where('id', $event_id)->first();
        $teams = DB::table('teams')->where('event_id', $event_id)->orderBy('score', 'desc')->get();

        $registration_count = count($registration);
        $event->currentseats = 0;
        $event->firstattendance = 0;
        $event->lastattendance = 0;
        for ($i = 0; $i < $registration_count; $i++){
            if ($registration[$i]->status == 2 || $registration[$i]->status == 4 || $registration[$i]->status == 5) $event->currentseats++;
            if ($registration[$i]->status == 4) $event->firstattendance++;
            if ($registration[$i]->status == 5) $event->lastattendance++;
        }

        return view('admin.eventdetails', ['registration' => $registration, 'committees' => $committees, 'event' => $event, 'teams' => $teams]);
    }

    public function postEventParticipants(Request $request, $event_id){
        // Make sure that it's an Admin (Higher Level)
        if (!Auth::check() || (Auth::user()->university_id != 2)){
            Session::put('error', 'Admin: Not Authorized');
            return redirect('login');
        }

        foreach($request->all() as $key => $value) {
            if (Str::startsWith($key, "status-") && $value >= 0){
                $key = substr($key, 7);
                DB::table('registration')->where('id', $key)->update(['status' => $value]);
            } else if (Str::startsWith($key, "action-")) switch ($key){
                case "action-registration-status":
                    if ($value == "enabled") DB::table('events')->where('id', $event_id)->update(['opened' => 1]);
                    else if ($value == "disabled") DB::table('events')->where('id', $event_id)->update(['opened' => 0]);
                break;
                case "action-attendance-status":
                    if ($value == "enabled") DB::table('events')->where('id', $event_id)->update(['attendance_opened' => 1]);
                    else if ($value == "disabled") DB::table('events')->where('id', $event_id)->update(['attendance_opened' => 0]);
                break;
                case "action-attendance-type":
                    if ($value == "entrance") DB::table('events')->where('id', $event_id)->update(['attendance_is_exit' => 0]);
                    else if ($value == "exit") DB::table('events')->where('id', $event_id)->update(['attendance_is_exit' => 1]);
                break;
                case "action-update-name":
                    if ($value != '') DB::table('events')->where('id', $event_id)->update(['name' => $value]);
                break;
                case "action-update-link":
                    if ($value != '') DB::table('events')->where('id', $event_id)->update(['url_link' => $value]);
                break;
                case "action-update-location":
                    if ($value != '') DB::table('events')->where('id', $event_id)->update(['location' => $value]);
                break;
                case "action-update-eventtoken":
                    if ($value != '') DB::table('events')->where('id', $event_id)->update(['totp_key' => $value]);
                break;
                case "action-update-seats":
                    if ($value > 0) DB::table('events')->where('id', $event_id)->update(['seats' => $value]);
                break;
            }
        }

        return redirect("/admin/event/" . $event_id);
    }

    public function downloadEventParticipants($event_id){
        // Make sure that it's an Admin
        if (!Auth::check() || (Auth::user()->university_id < 2 || Auth::user()->university_id > 3)){
            Session::put('error', 'Admin: Not Authorized');
            return redirect('login');
        }
        $registration = $this->util_getParticipantsByEventId($event_id);
        // TODO: Save from database to CSV
    }

    // Module to download from File ID
    public function downloadFromFileId($file_id){
        // Make sure that it's an Admin
        if (!Auth::check() || (Auth::user()->university_id < 2 || Auth::user()->university_id > 3)){
            Session::put('error', 'Admin: Not Authorized');
            return redirect('login');
        }
        $file = DB::table('files')->where('id', $file_id)->first();
        if (!Auth::check() || (Auth::user()->university_id < 2 || Auth::user()->university_id > 3)){
            Session::put('error', 'Admin: File ID not found');
            return redirect('home');
        }

        try {
            return response()->download(storage_path("app/" . $file->name));
        } catch (\Exception $e){
            Session::put('error', 'Admin: Internal Server Error:' . $e);
            return redirect('home');
        }
    }
}
