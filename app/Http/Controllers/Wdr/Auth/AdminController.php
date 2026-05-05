<?php

namespace App\Http\Controllers\Wdr\Auth;

use App\Http\Controllers\Controller;
use App\Mail\AccountCreationMail;
use App\Mail\OtpMail;
use App\Mail\SendForgotPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\ItemCategory;
use App\Models\LogicalSpaceCategory;
use App\Models\LogicalSpaceSubcategory;
use App\Models\LogicalSpaceName;
use App\Models\ItemSubcategory;
use App\Models\Product;
use App\Models\SiteCategory;
use App\Models\Site;
use App\Models\VenueType;
use App\Models\Wdr\Event;
use App\Models\Wdr\Guardian;
use App\Models\Wdr\GuardianDocument;
use App\Models\Wdr\TempUpload;
use App\Models\Task;
use App\Models\Vapp\FunctionalArea;
use App\Notifications\EmailOtpVerification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use TechEd\SimplOtp\SimplOtp;
use TechEd\SimplOtp\Models\SimplOtp as OTPModel;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

// use Brian2694\Toastr\Facades\Toastr;


class AdminController extends Controller
{
    //
    // public function adminDashboard(){

    //     return view('admin.index');
    // }  // End method

    public function trackiDashboard()
    {
        // dd('inside trackiDashboard');
        $workspace = session()->get('workspace_id');
        $user_department = auth()->user()->department_assignment_id;
        $user_workspace = auth()->user()->workspace_id;

        // if (session()->has('workspace_id')){
        //     dd('session for workspace: '.session()->get('workspace_id'));
        // }

        $proj_count = Event::leftJoin('tasks', 'tasks.event_id', '=', 'events.id')
            ->whereNull('archived')
            ->when($user_department, function ($query, $user_department) {
                return $query->where('tasks.department_assignment_id', $user_department);
            })->distinct('events.id')->count();

        $unbudgeted_proj_count = Event::leftJoin('tasks', 'tasks.event_id', '=', 'events.id')
            ->leftJoin('funds_category', 'funds_category.id', '=', 'events.fund_category_id')
            ->whereNull('archived')
            ->whereNot(function ($query) {
                $query->where('funds_category.name', '=', 'Budgeted');
            })
            ->when($user_department, function ($query, $user_department) {
                return $query->where('tasks.department_assignment_id', $user_department);
            })->distinct('events.id')->count();

        $task_count = Task::join('events', 'events.id', '=', 'tasks.event_id')
            ->whereNull('events.archived')
            ->when($user_department, function ($query, $user_department) {
                return $query->where('tasks.department_assignment_id', $user_department);
            })
            ->when(auth()->user()->functional_area_id, function ($query, $user_fa) {
                return $query->where('events.functional_area_id', $user_fa);
            })
            ->count();

        $late_tasks_count = Task::join('events', 'events.id', '=', 'tasks.event_id')
            ->whereNull('events.archived')
            ->whereRaw('datediff(tasks.due_date, CURRENT_DATE) < 0')
            ->where('tasks.progress', '<', 1)
            ->when($user_department, function ($query, $user_department) {
                return $query->where('tasks.department_assignment_id', $user_department);
            })
            ->when(auth()->user()->functional_area_id, function ($query, $user_fa) {
                return $query->where('events.functional_area_id', $user_fa);
            })
            ->count();

        $ending_tasks_count = Task::join('events', 'events.id', '=', 'tasks.event_id')
            ->whereNull('events.archived')
            ->whereRaw('datediff(tasks.due_date, CURRENT_DATE) < 3')
            ->whereRaw('datediff(tasks.due_date, CURRENT_DATE) >= 0')
            ->where('tasks.progress', '<', 1)
            ->when($user_department, function ($query, $user_department) {
                return $query->where('tasks.department_assignment_id', $user_department);
            })
            ->when(auth()->user()->functional_area_id, function ($query, $user_fa) {
                return $query->where('events.functional_area_id', $user_fa);
            })
            ->count();

        $starting_tasks_count = Task::join('events', 'events.id', '=', 'tasks.event_id')
            ->whereNull('events.archived')
            ->whereRaw('datediff(tasks.start_date, CURRENT_DATE) < 3')
            ->whereRaw('datediff(tasks.start_date, CURRENT_DATE) >= 0')
            ->where('tasks.progress', '<', 1)
            ->when($user_department, function ($query, $user_department) {
                return $query->where('tasks.department_assignment_id', $user_department);
            })
            ->when(auth()->user()->functional_area_id, function ($query, $user_fa) {
                return $query->where('events.functional_area_id', $user_fa);
            })
            ->count();

        $total_yearly_budget = OrganizationBudget::where('type', 'year')
            ->whereYear('date_from', date('Y'))
            ->first();

        $total_spent_by_department = Task::join('events', 'events.id', '=', 'tasks.event_id')
            ->join('department', 'department.id', '=', 'tasks.department_assignment_id')
            ->whereNull('events.archived')
            ->select('department.name', DB::raw("sum(tasks.actual_budget_allocated) as value"))
            ->whereYear('tasks.start_date', date('Y'))
            ->groupBy('department.name')
            ->when($user_department, function ($query, $user_department) {
                return $query->where('tasks.department_assignment_id', $user_department);
            })
            ->when(auth()->user()->functional_area_id, function ($query, $user_fa) {
                return $query->where('events.functional_area_id', $user_fa);
            })
            ->having('value', '>', '0')
            ->get();

        $total_yearly_spent = Task::select(DB::raw("sum(tasks.actual_budget_allocated) as total_spent"))
            ->join('events', 'events.id', '=', 'tasks.event_id')
            ->whereNull('events.archived')
            ->whereYear('tasks.start_date', date('Y'))
            ->first();

        // $completed_projects_by_month = Event::select(DB::raw('count(*) as total, date_format(end_date, "%m") as month'))
        //     ->whereYear('end_date', date('Y'))
        //     ->where('event_status', '=', config('tracki.project_status.completed'))
        //     ->whereNull('archived')
        //     ->groupBy('month')
        //     ->get();

        // DB::enableQueryLog();
        $total_sales_by_month = Event::select(DB::raw('IFNULL(sum(events.total_sales), 0) count, cal.month'))
            ->rightJoin('cal', function ($join) {
                $join
                    ->on('cal.month_num', DB::raw('date_format(end_date, "%m")'))
                    ->whereYear('end_date', date('Y'))
                    ->where('event_status', '=', config('tracki.project_status.completed'))
                    ->whereNull('archived');
            })
            ->groupBy('cal.month')
            ->orderBy('cal.month_num')
            ->get();

        $completed_projects_by_month = Event::select(DB::raw('IFNULL(count(date_format(end_date, "%m")), 0) count, cal.month'))
            ->rightJoin('cal', function ($join) {
                $join
                    ->on('cal.month_num', DB::raw('date_format(end_date, "%m")'))
                    ->whereYear('end_date', date('Y'))
                    ->where('event_status', '=', config('tracki.project_status.completed'))
                    ->whereNull('archived');
            })
            ->groupBy('cal.month')
            ->orderBy('cal.month_num')
            ->get();

        $projects_by_month = DB::table('events')->select(DB::raw('IFNULL(count(date_format(end_date, "%m")), 0) count, cal.month'))
            ->rightJoin('cal', function ($join) {
                $join
                    ->on('cal.month_num', DB::raw('date_format(end_date, "%m")'))
                    ->whereYear('end_date', date('Y'))
                    ->whereNull('archived');
            })
            ->groupBy('cal.month')
            ->orderBy('cal.month_num')
            ->get();

        // dd(DB::getQueryLog());
        // dd($completed_projects_by_month1);

        $budgeted_projects_by_month = Event::select(DB::raw('IFNULL(count(date_format(start_date, "%m")), 0) count, cal.month'))
            ->rightJoin('cal', function ($join) {
                $join
                    ->on('cal.month_num', DB::raw('date_format(start_date, "%m")'))
                    ->whereYear('start_date', date('Y'))
                    // ->where('event_status', '=', config('tracki.project_status.completed'))
                    ->where('fund_category_id', '=', '1')
                    ->whereNull('archived');
            })
            ->groupBy('cal.month')
            ->orderBy('cal.month_num')
            ->get();

        $unbudgeted_projects_by_month = Event::select(DB::raw('IFNULL(count(date_format(start_date, "%m")), 0) count, cal.month'))
            ->rightJoin('cal', function ($join) {
                $join
                    ->on('cal.month_num', DB::raw('date_format(start_date, "%m")'))
                    ->whereYear('start_date', date('Y'))
                    // ->where('event_status', '=', config('tracki.project_status.completed'))
                    ->where('fund_category_id', '=', '2')
                    ->whereNull('archived');
            })
            ->groupBy('cal.month')
            ->orderBy('cal.month_num')
            ->get();

        //  dd($budgeted_projects_by_month);


        // $fund_projects_by_month = Event::selectRaw('count(*) as total')
        //     ->selectRaw('count(case when fund_category_id=1 then 1 end) as budgeted')
        //     ->selectRaw('count(case when fund_category_id=2 then 1 end) as unbudgeted')
        //     ->selectRaw('date_format(end_date, "%m") as month')
        //     ->groupBy('month')
        //     ->whereYear('end_date', date('Y'))
        //     ->where('event_status', '=', config('tracki.project_status.completed'))
        //     ->whereNull('archived')
        //     ->get();


        $budgeted_monthly = array();
        $i = 0;
        foreach ($budgeted_projects_by_month as $cp) {
            $budgeted_monthly[$i] = $cp->count;
            $i++;
        }

        // dd($budgeted_monthly);

        $unbudgeted_monthly = array();
        $i = 0;
        foreach ($unbudgeted_projects_by_month as $cp) {
            $unbudgeted_monthly[$i] = $cp->count;
            $i++;
        }

        $completed_projects_by_month_array = array();
        $i = 0;
        foreach ($completed_projects_by_month as $cp) {
            $completed_projects_by_month_array[$i] = $cp->count;
            $i++;
        }

        $projects_by_month_array = array();
        $i = 0;
        foreach ($projects_by_month as $cp) {
            $projects_by_month_array[$i] = $cp->count;
            $i++;
        }

        $total_sales_by_month_array = array();
        $i = 0;
        foreach ($total_sales_by_month as $cp) {
            $total_sales_by_month_array[$i] = $cp->count;
            $i++;
        }

        if ($total_yearly_budget) {
            $remaining_budget = $total_yearly_budget?->budget_amount - $total_yearly_spent?->total_spent;
            // $total_yearly_budget->budget_amount

            $budget_percentage_used = ($total_yearly_spent?->total_spent / $total_yearly_budget?->budget_amount) * 100;
        } else {
            $remaining_budget = 0;
            $budget_percentage_used = 0;
        }

        $todo_status_chart = Event::join('statuses', 'statuses.id', '=', 'events.event_status')
            ->select('statuses.title as name', DB::raw("count(statuses.title) as value"))
            ->groupBy('statuses.title')
            ->when($workspace, function ($query, $workspace) {
                return $query->where('events.workspace_id', $workspace);
            })
            ->having('value', '>', '0')
            ->get();

        $project_status_chart = Event::join('statuses', 'statuses.id', '=', 'events.event_status')
            ->select('statuses.title as name', DB::raw("count(statuses.title) as value"))
            ->groupBy('statuses.title')
            ->when($workspace, function ($query, $workspace) {
                return $query->where('events.workspace_id', $workspace);
            })
            ->having('value', '>', '0')
            ->get();
        // dump(vsprintf(str_replace(['?'], ['\'%s\''], $total_sales_by_month->toSql()), $total_sales_by_month->getBindings()));

        // dd($total_sales_by_month_array);
        // dd($total_sales_by_month->getBindings());
        // dd($total_sales_by_month->toSql());

        return view('tracki.index', [
            'project_count' => $proj_count,
            'task_count' => $task_count,
            'late_tasks_count' => $late_tasks_count,
            'ending_tasks_count' => $ending_tasks_count,
            'starting_tasks_count' => $starting_tasks_count,
            'total_yearly_budget' => $total_yearly_budget,
            'total_yearly_spent' => $total_yearly_spent,
            'budget_percentage_used' => $budget_percentage_used,
            'unbudgeted_proj_count' => $unbudgeted_proj_count,
            'remaining_budget' => $remaining_budget,
            'total_spent_by_department' => $total_spent_by_department,
            'completed_projects_by_month' => $completed_projects_by_month_array,
            'projects_by_month' => $projects_by_month_array,
            'budgeted_projects_by_month' => $budgeted_monthly,
            'unbudgeted_projects_by_month' => $unbudgeted_monthly,
            'total_sales_by_month' => $total_sales_by_month_array,
            'project_status_chart' => $project_status_chart,
            'todo_status_chart' => $todo_status_chart,
            'user_workspace' => $user_workspace,
        ]);
    }  //trackiDashboard

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('login');
    } // End method

    public function login()
    {
        Auth::guard('web')->logout();
        return view('vapp.auth.sign-in');
    }

    public function verifyOtpAndLoginxx(Request $request)
    {
        // $maxAttempts = (int) config('simple-otp.otp_max_attempts');
        // $otp_attempts = SimpleOTP::where('identity', auth()->user()->email)
        //     ->where('validated_at', null)
        //     ->latest()
        //     ->first();

        // if($otp_attempts->attempts >= $maxAttempts){
        //     $notification = array(
        //         'message' => 'Max attempts reached',
        //         'alert-type' => 'error'
        //     );
        //     return redirect('/tracki/auth/signin')->with($notification);
        //     // return redirect('tracki/auth/otp')->with($notification);
        // };

        $user = auth()->user();

        // $isValid = SimpleOTP::verify(auth()->user()->email, $request->otp);
        $isValid = SimplOtp::validate($user->email, $request->otp);
        // dd($isValid);
        if ($isValid->status) {
            session()->put('OTPSESSIONKEY', true);
        }

        $isvalid_string = $isValid ? 'true' : 'false';

        if (auth()->check() && session()->get('OTPSESSIONKEY')) {
            return redirect()->intended('/');
        } else {
            $notification = array(
                'message' => 'Invalid OTP code Entered',
                'alert-type' => 'error'
            );
            return redirect('vapp/auth/otp')->with($notification);
        }
    }

    public function verifyOtpAndLogin(Request $request)
    {

        $user = auth()->user();
        $key = 'otp-attempts:' . $user->id;
        // $remaining = max(0, 5 - RateLimiter::attempts($key));

        // 1. Check if user is locked
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            OTPModel::where('identifier', $user->email)->where('is_valid', true)->delete();
            $notification = [
                'message' => "Too many invalid OTP attempts.",
                'alert-type' => 'danger'
            ];
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            return redirect()->route('login')->with($notification);
        }

        // 2. Verify OTP
        $isValid = SimplOtp::validate($user->email, $request->otp);

        // $isvalid_string = $isValid->status ? 'true' : 'false';

        if ($isValid->status) {
            // ✅ Success: reset attempts
            RateLimiter::clear($key);
            session()->put('OTPSESSIONKEY', true);

            if (auth()->check() && session()->get('OTPSESSIONKEY')) {
                return redirect()->intended('/');
            }
        }

        // 3. Invalid OTP → count attempt + lock if max reached
        RateLimiter::hit($key, 100); // lock for 15 minutes

        $remaining = 5 - RateLimiter::attempts($key);

        $notification = [
            'message' => "Invalid OTP code entered. Attempts left: {$remaining}",
            'alert-type' => 'warning',
        ];
        return redirect('auth/otp')->with($notification);
    }

    public function showOtp()
    {
        // $key = 'otp-attempts:' . auth()->id();
        // $remaining = RateLimiter::attempts($key);

        // appLog('AdminController::showOtp => key: ' . $key);
        // appLog('AdminController::showOtp => attempts: ' . RateLimiter::attempts($key));
        // appLog('AdminController::showOtp => remaining attempts: ' . $remaining);
        return view('auth.otp');
    }

    public function resendOTP()
    {
        // dd( Session::all());
        $user = auth()->user();
        if (config('settings.otp_enabled')) {

            $key = Str::lower($user->id);

            // Allow 3 attempts every 5 minutes
            if (RateLimiter::tooManyAttempts($key, 3)) {
                $seconds = RateLimiter::availableIn($key);
                $minutes = floor($seconds / 60);
                $remainingSeconds = $seconds % 60;

                $timeMessage = $minutes > 0
                    ? "{$minutes} minute(s) and {$remainingSeconds} second(s)"
                    : "{$remainingSeconds} second(s)";

                $notification = array(
                    'message' => 'Too many OTP requests. Try again in ' . $timeMessage,
                    'alert-type' => 'danger'
                );

                return redirect('/auth/otp')->with($notification);

                return response()->json([
                    'message' => 'Too many OTP requests. Try again in ' . $seconds . ' seconds.'
                ], 429);
            }

            // Hit the rate limiter
            RateLimiter::hit($key, 300); // 300 seconds = 5 minutes

            $otp = SimplOtp::generate($user->email);
            if ($otp->status === true) {
                $details = [
                    'otp_token' => $otp->token,
                    'body' => 'Your One-Time Password (OTP) is: ' . $otp->token,
                ];
                Mail::to($user->email)->send(new OtpMail($details));
            }
            $notification = array(
                'message' => 'We have a sent a new OTP code to your email, please check',
                'alert-type' => 'success'
            );

            return redirect('/auth/otp')->with($notification);
            // return redirect('tracki/auth/otp')->with('message', 'OTP re-sent to your email');
        }
    }

    public function signUp()
    {
        $events = Event::all();
        return view('auth.sign-up', compact('events'));
    }

    public function register($event_id)
    {

        $event = Event::findOrFail($event_id);
        return view('auth.register', compact('event'));
    }

    public function storeRegister(Request $request)
    {
        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|max:15',
            'qid' => 'required|max:50|unique:guardians,qid',
            'password' => ['required', 'confirmed', Password::defaults()],
            // 'qid_files' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'qid_files' => 'nullable|integer|exists:temp_uploads,id',
        ];

        $message = '
            [
                "name.required" => "Name is required",
                "email.required" => "Email is required",
                "email.email" => "Provide a valid email",
                "email.unique" => "Email already exists",
                "phone.required" => "Phone is required",
                "password.required" => "Password is required",
                "password.confirmed" => "Password confirmation does not match",
                "password.min" => "Password must be at least 8 characters",
                "password.max" => "Password must not exceed 16 characters",
            ]';

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }

        DB::beginTransaction();
        try {

            // @unlink(public_path('upload/instructor_images/' . $data->photo));
            // $id = Auth::user()->id;
            $user = new User();
            $guardian = new Guardian();

            $user->employee_id = 0;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->status = 1;
            $user->usertype = 'user';
            $user->is_admin = 0;
            $user->role = 'user';

            $user->save();

            $guardian->event_id = $request->event_id ?? null;
            $guardian->full_name = $request->name;
            $guardian->email = $request->email;
            $guardian->phone_main = $request->phone;
            $guardian->qid = $request->qid;
            $guardian->phone_secondary = $request->phone;
            $guardian->user_id = $user->id;
            $guardian->save();

            // handle multiple QID files upload
            $qidFiles = $request->input('qid_files', []);

            // If somehow a single value comes, normalize to array
            if (!is_array($qidFiles) && $qidFiles) {
                $qidFiles = [$qidFiles];
            }

            foreach ($qidFiles as $tempId) {

                Log::info("Processing QID file temp ID: {$tempId} for guardian ID: {$guardian->id}");
                $temp = TempUpload::find((int)$tempId);

                if (!$temp) {
                    throw new \Exception("Invalid uploaded file reference: {$tempId}");
                }

                $ext = pathinfo($temp->path, PATHINFO_EXTENSION) ?: 'jpg';
                $fileName = time() . '_' . uniqid() . '.' . $ext;

                $finalDir  = "uploads/guardians/{$guardian->id}/";
                $finalPath = $finalDir . $fileName;

                // move from temp disk -> private disk
                $contents = Storage::disk($temp->disk)->get($temp->path);
                Storage::disk('private')->put($finalPath, $contents);

                // create document row
                $doc = new GuardianDocument();
                $doc->guardian_id = $guardian->id;
                $doc->disk = 'private';
                $doc->path = $finalPath; // full file path
                $doc->original_name = $temp->original_name ?? $fileName;
                $doc->mime = $temp->mime ?? 'image/' . $ext;
                $doc->size = $temp->size ?? strlen($contents);
                $doc->created_by = $user->id;
                $doc->save();

                // cleanup temp
                Storage::disk($temp->disk)->delete($temp->path);
                $temp->delete();
            }
            // Handle FilePond temp upload -> move to private guardian_documents
            // if ($request->filled('qid_files')) {

            //     $temp = TempUpload::where('path', (int)$request->qid_files)
            //         ->when(Auth::check(), fn($q) => $q->where('user_id', Auth::id()))
            //         ->first();

            //     if (!$temp) {
            //         throw new \Exception('Invalid uploaded file reference.');
            //     }

            //     $ext = pathinfo($temp->path, PATHINFO_EXTENSION) ?: 'jpg';
            //     $imageName = time() . '_' . uniqid() . '.' . $ext;

            //     // temp is on $temp->disk (example: public), final should be private
            //     $finalDir  = "uploads/guardians/{$guardian->id}/";
            //     $finalPath = $finalDir . $imageName;

            //     // read from temp disk and write to private disk
            //     $contents = Storage::disk($temp->disk)->get($temp->path);
            //     Storage::disk('private')->put($finalPath, $contents);

            //     // cleanup temp
            //     Storage::disk($temp->disk)->delete($temp->path);
            //     $temp->delete();

            //     $guardianDocument = new GuardianDocument();
            //     $guardianDocument->guardian_id = $guardian->id;
            //     $guardianDocument->disk = 'private';
            //     $guardianDocument->path = $finalPath; // IMPORTANT: save full path including file name
            //     $guardianDocument->original_name = $temp->original_name ?? $imageName;
            //     $guardianDocument->mime = $temp->mime ?? 'image/' . $ext;
            //     $guardianDocument->size = $temp->size ?? strlen($contents);
            //     $guardianDocument->created_by = $user->id;
            //     $guardianDocument->save();

            //     appLog('Profile image moved from temp to private guardian_documents: ' . $finalPath);
            // }


            // $roles = $request->roles;
            $role_id = getRoleIdByLabel('Customer');
            // $roles = ['Customer']; // Customer role ;
            // $roles = [18]; // Customer role ;

            $intRoles = collect([$role_id])->map(function ($role) {
                return (int)$role;
            });

            $user->assignRole($intRoles);


            // if ($request->roles) {
            //     $user->assignRole($intRoles);
            // }Cat123456!  

            if (!empty($request->event_id)) {
                // foreach ($request->event_id as $key => $event) {
                //     appLog('Attaching event ID: ' . $event);
                //     appLog('User ID: ' . $user->id);
                //     appLog('Event ID: ' . $event);
                $user->events()->attach($request->event_id);
                // }
            }

            // if (!empty($request->functional_id)) {
            //     foreach ($request->functional_id as $key => $functional) {
            //         appLog('Attaching functional ID: ' . $functional);
            //         appLog('User ID: ' . $user->id);
            //         appLog('Functional ID: ' . $functional);
            //         $user->fa()->attach($request->functional_id[$key]);
            //     }
            // }

            // $this->UtilController->save_files($request, $data->id);

            $notification = array(
                'message'       => 'User created successfully',
                'alert-type'    => 'success'
            );

            if (config('settings.send_notifications')) {
                $eventNames = $user->events()->exists()
                    ? $user->events->pluck('name')->implode(', ')
                    : 'N/A';
                $details = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $request->password,
                ];
                // Send email notification
                Mail::to($user->email)->send(new AccountCreationMail($details));
                // SendAccountCreationEmailJob::dispatch($details);
            }
            DB::commit();

            return Redirect::route('login')->with($notification);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

        // Toastr::success('Has been add successfully :)','Success');
        // return redirect()->back()->with($notification);
        //mainProfileStore

    }

    public function msSignUp()
    {
        $events = Event::all();
        $roles = Role::all();
        return view('auth.ms-sign-up', compact('events', 'roles'));
    }

    public function forgotPassword()
    {
        return view('auth.forgot');
    }

    public function submitForgetPasswordForm(Request $request): RedirectResponse
    {
        $rules = [
            'email' => 'required|email|exists:users',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }

        $token = sha1(time() . config('global.key'));

        try {
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors('A reset password was already sent to your email.  please check your inbox');
            // return $e->getMessage();
        }

        $content = [
            'token'     => $token,
            'subject'   => 'Tracki: Reset Password Link',
            'url'       => "route('reset.password.get', $token)",
        ];

        Mail::to($request->email)->queue(new SendForgotPasswordMail($content));

        // Mail::send('emails.forgetPassword', ['token' => $token], function($message) use($request){
        //     $message->to($request->email);
        //     $message->subject('Reset Password');
        // });

        return back()->with('message', 'We have e-mailed your password reset link!');
    } //submitForgetPasswordForm

    public function showResetPasswordForm($token): View
    {
        return view('tracki.auth.reset', ['token' => $token]);
    } //showResetPasswordForm

    public function submitResetPasswordForm(Request $request): RedirectResponse
    {
        $rules = [
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }



        $updatePassword = DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])
            ->first();

        if (!$updatePassword) {
            appLog('update failed');
            return back()->withInput()->withErrors(['error' => 'Invalid token!']);
        }

        $user = User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

        return redirect('/tracki/auth/login')->with('message', 'Your password has been changed!');
    } //submitResetPasswordForm

    public function createUser(Request $request)
    {

        $rules = [
            'username' => 'required|unique:users',
            'password' => 'required|confirmed|min:8|max:16',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            //return ($request->get('password').' - '.$request->get('password_confirmation'));
            //return ($request->input());
            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }

        $activate_value = sha1(time() . config('global.key'));

        // $id = Auth::user()->id;
        $data = new User;

        $data->username = $request->username;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->address = $request->address;
        $data->phone = $request->phone;
        $data->department_assignment_id = $request->department_id;
        $data->password = Hash::make($request->password);
        $data->department_assignment_id = $request->department_id;
        $data->functional_area_id = $request->functional_area_id;
        $data->status = 'active';
        $data->role = 'admin';
        $data->address = 'doha';


        $data->save();

        $notification = array(
            'message'       => 'User created successfully',
            'alert-type'    => 'success'
        );

        // Toastr::success('Has been add successfully :)','Success');
        // return redirect()->back()->with($notification);
        return Redirect::route('tracki.auth.signup')->with($notification);
        //mainProfileStore

    }

    public function store(Request $request)
    {

        $rules = [
            'username' => 'required|unique:users',
            'password' => 'required|confirmed|min:8|max:16',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            //return ($request->get('password').' - '.$request->get('password_confirmation'));
            //return ($request->input());
            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }

        $activate_value = sha1(time() . config('global.key'));

        // $id = Auth::user()->id;
        $data = new User;

        $data->username = $request->username;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->address = $request->address;
        $data->phone = $request->phone;
        $data->department_assignment_id = $request->department_id;
        $data->password = Hash::make($request->password);
        $data->department_assignment_id = $request->department_id;
        $data->functional_area_id = $request->functional_area_id;
        $data->status = 'active';
        $data->role = 'admin';
        $data->address = 'doha';


        $data->save();

        $notification = array(
            'message'       => 'User created successfully',
            'alert-type'    => 'success'
        );

        // Toastr::success('Has been add successfully :)','Success');
        // return redirect()->back()->with($notification);
        return Redirect::route('tracki.auth.signup')->with($notification);
        //mainProfileStore

    } // store

    public function reportList()
    {
        return view('tracki.report');
    }

    public function orderForm()
    {

        // $country_codes = DB::table('item_category')->orderBy('arabic_value', 'asc')->get();

        $venue_type = VenueType::all();
        // dd($venue_name);
        $item_category = ItemCategory::all();
        $logical_space_categories = LogicalSpaceCategory::all();
        return view('tracki.order-form', [
            'item_category'  => $item_category,
            'venue_type'    => $venue_type,
            'logical_space_categories'    => $logical_space_categories,
        ]);
    }

    public function getSiteCategory(Request $request)
    {
        $all_site_categories = SiteCategory::where('venue_type_id', $request->venue_type_id)->get();
        return response()->json([
            'status'   => 'success',
            'all_site_categories' => $all_site_categories,
        ]);
    }

    public function getSiteCode(Request $request)
    {
        $all_site_codes = Site::where('site_category_id', $request->venue_id)->get();
        return response()->json([
            'status'   => 'success',
            'all_site_codes' => $all_site_codes,
        ]);
    }

    public function getSiteData(Request $request)
    {
        $all_site_codes = Site::where('site_id', $request->site_id)->get();
        return response()->json([
            'status'   => 'success',
            'all_site_codes' => $all_site_codes,
        ]);
    }

    public function getLogicalSpaceSubcategory(Request $request)
    {
        $all_ls_subcat = LogicalSpaceSubcategory::where('category_id', $request->category_id)
            ->where('active_flag', '1')
            ->get();
        return response()->json([
            'status'   => 'success',
            'all_ls_subcat' => $all_ls_subcat,
        ]);
    }

    public function getLogicalSpaceName(Request $request)
    {
        $all_ls_name = LogicalSpaceName::where('subcat_id', $request->subcat_id)
            ->where('active_flag', '1')
            ->get();
        return response()->json([
            'status'   => 'success',
            'all_ls_name' => $all_ls_name,
        ]);
    }

    public function getLogicalSpaceCode(Request $request)
    {
        $all_ls_code = LogicalSpaceName::where('logical_space_id', $request->logical_space_id)
            ->where('active_flag', '1')
            ->get();
        return response()->json([
            'status'   => 'success',
            'all_ls_code' => $all_ls_code,
        ]);
    }

    public function getItemSubcategory(Request $request)
    {
        $all_item_subcategory = ItemSubcategory::where('item_category_id', $request->item_category_id)
            ->where('active_flag', '1')
            ->get();
        return response()->json([
            'status'   => 'success',
            'all_item_subcategory' => $all_item_subcategory,
        ]);
    }

    public function getItemName(Request $request)
    {
        $all_item_name = Product::where('item_subcat_id', $request->item_subcat_id)
            ->where('active', '1')
            ->get();
        return response()->json([
            'status'   => 'success',
            'all_item_name' => $all_item_name,
        ]);
    }

    public function userProfile()
    {
        // first get the auth user
        $id = Auth::user()->id;
        $profileData = User::find($id);

        // dd($profileData);

        return view('tracki.profile-view', compact('profileData'));
    }

    public function mainOrderStore(Request $request)
    {
        // Log::debug('*****************mainOrderStore********session exists?? 1 is ok, 0 is not' . session()->has('user_session'));

        $id = Auth::user()->id;

        $rules = [
            'site_type' => 'required|integer',
            // 'site_type' => 'required|alpha_dash|min:3|max:25',
            'site_category' => 'required|integer',
            'site_code' => 'required',
            'site_name' => 'required',
            'logical_space_category' => 'required|alpha_dash',
            'logical_space_subcategory' => 'required',
            'logical_space_name' => 'required',
            'logical_space_code' => 'required',
        ];

        // $validator = Validator::make($request->all(), $rules);



        $order = new Order;
        $order->user_id = $id;
        $order->venue_type_id = $request->site_type;
        $order->site_category_id = $request->site_category;
        $order->site_id = $request->site_code;
        $order->project_id = 102;
        // $order->languages_known = implode(',', $request->languages_known);

        $save = $order->save();

        $notification = array(
            'message'       => 'Order# ' . $order->id . ' created successfully',
            'alert-type'    => 'success'
        );

        // Toastr::success('Has been add successfully :)','Success');
        return redirect()->back()->with($notification);
        if ($order) {
            return redirect()->back()->with($notification);
        } else {
            return back()->with('fail', 'Something went wrong, try again later');
        }
    }  // mainOrderStore


    public function mainProfileStore(Request $request)
    {

        $id = Auth::user()->id;
        $data = User::find($id);

        $data->username = $request->username;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->address = $request->address;
        $data->phone = $request->phone;
        $data->address = $request->address;

        if ($request->file('photo')) {
            $file = $request->file('photo');
            $filename = rand() . date('ymdHis') . $file->getClientOriginalName();
            $file->move(public_path('upload/admin_images'), $filename);
            $data['photo'] = $filename;
        }

        $data->save();

        $notification = array(
            'message'       => 'Profile updated successfully',
            'alert-type'    => 'success'
        );

        // Toastr::success('Has been add successfully :)','Success');
        return redirect()->back()->with($notification);
    }  //mainProfileStore

    public function getOrderData(Request $request)
    {
        // dd('getPlannerData');
        $draw            = $request->get('draw');
        $start           = $request->get("start");
        $rowPerPage      = $request->get("length"); // total number of rows per page
        $columnIndex_arr = $request->get('order');
        $columnName_arr  = $request->get('columns');
        $order_arr       = $request->get('order');
        $search_arr      = $request->get('search');

        // dd($search_arr);
        appLog($draw . ' ' . $start . ' ' . $rowPerPage . ' ' . $columnIndex_arr . ' ' . $order_arr . ' ' . $search_arr);
        // echo $draw.' '.$start.' '.$rowPerPage;


        $columnIndex     = $columnIndex_arr[0]['column']; // Column index

        // log::debug('colunmIndex: '.$columnIndex);

        $columnName      = $columnName_arr[$columnIndex]['data']; // Column name
        // log::debug('columnName: '.$columnName);

        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue     = $search_arr['value']; // Search value

        $orderDetails = DB::table('order_h');

        $totalRecords = $orderDetails
            ->join('order_item_h', 'order_h.order_id', '=', 'order_item_h.order_id')
            ->join('product', 'order_item_h.product_id', '=', 'product.product_id')
            ->join('project', 'order_h.project_id', '=', 'project.project_id')
            ->select(
                'order_h.order_id',
                'order_item_h.item_order_status',
                'project.project_name',
                'product.product_name as item_name'
            )->count();

        // Log::debug("totalRecords: " . $totalRecords);

        $totalRecordsWithFilter = $orderDetails->where(function ($query) use ($searchValue) {
            $query->join('order_item_h', 'order_h.order_id', '=', 'order_item_h.order_id');
            $query->join('product', 'order_item_h.product_id', '=', 'product.product_id');
            $query->join('project', 'order_h.project_id', '=', 'project.project_id');
            $query->select(
                'order_h.order_id',
                'order_item_h.item_order_status',
                'project.project_name',
                'product.product_name as item_name'
            );
            $query->where('order_h.order_id', 'like', '%' . $searchValue . '%');
            $query->orWhere('item_order_status', 'like', '%' . $searchValue . '%');
            $query->orWhere('project_name', 'like', '%' . $searchValue . '%');
            $query->orWhere('product_name', 'like', '%' . $searchValue . '%');
        })->count();

        // Log::debug("totalRecordsWithFilter: " . $totalRecordsWithFilter);

        $records = $orderDetails->orderBy($columnName, $columnSortOrder)
            ->where(function ($query) use ($searchValue) {
                $query->join('order_item_h', 'order_h.order_id', '=', 'order_item_h.order_id');
                $query->join('product', 'order_item_h.product_id', '=', 'product.product_id');
                $query->join('project', 'order_h.project_id', '=', 'project.project_id');
                $query->select(
                    'order_h.order_id',
                    'order_item_h.item_order_status',
                    'project.project_name',
                    'product.product_name as item_name'
                );
                $query->where('order_h.order_id', 'like', '%' . $searchValue . '%');
                $query->orWhere('item_order_status', 'like', '%' . $searchValue . '%');
                $query->orWhere('project_name', 'like', '%' . $searchValue . '%');
                $query->orWhere('product_name', 'like', '%' . $searchValue . '%');
            })
            ->skip($start)
            ->take($rowPerPage)
            ->get();

        // Log::debug("records: ".$records);

        $data_arr = [];
        // $records = $orderDetails;

        foreach ($records as $key => $record) {

            if ($record->item_order_status == '1') {
                $status = '<td><span class="badge badge-phoenix badge-phoenix-success">Approved</span></td>';
            } else {
                $status = '<td><span class="badge badge-phoenix badge-phoenix-warning">Rejected</span></td>';
            }

            $hidden_id = '<td hidden class="user_id">' . $record->order_id . '</td>';

            $modify = '
                <td class="text-end">
                    <div class="actions">
                        <a href="#" class="btn btn-sm bg-danger-light">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <a class="btn btn-sm bg-danger-light delete user_id" data-bs-toggle="modal" data-user_id="' . $record->order_id . '" data-bs-target="#plannerDelete">
                        <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>
                </td>
            ';

            $data_arr[] = [
                "order_id"         => $record->order_id,
                "status"        => $status, //$record->item_order_status,
                "project_name"  => $record->project_name,
                "item"          => $record->item_name,
                // "active_flag"       => $status,
                "modify"        => $modify,
            ];
        }

        $response = [
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordsWithFilter,
            "aaData"               => $data_arr
        ];

        // dd(response()->json($response));
        return response()->json($response);
    }  //getPlannerData

    public function getProjectData(Request $request)
    {
        // dd('getPlannerData');
        $draw            = $request->get('draw');
        $start           = $request->get("start");
        $rowPerPage      = $request->get("length"); // total number of rows per page
        $columnIndex_arr = $request->get('order');
        $columnName_arr  = $request->get('columns');
        $order_arr       = $request->get('order');
        $search_arr      = $request->get('search');

        // dd($search_arr);
        appLog($draw . ' ' . $start . ' ' . $rowPerPage . ' ' . $columnIndex_arr . ' ' . $order_arr . ' ' . $search_arr);
        // echo $draw.' '.$start.' '.$rowPerPage;


        $columnIndex     = $columnIndex_arr[0]['column']; // Column index

        // log::debug('colunmIndex: '.$columnIndex);

        $columnName      = $columnName_arr[$columnIndex]['data']; // Column name
        // log::debug('columnName: '.$columnName);

        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue     = $search_arr['value']; // Search value

        $orderDetails = DB::table('order_h');

        $totalRecords = $orderDetails
            ->join('order_item_h', 'order_h.order_id', '=', 'order_item_h.order_id')
            ->join('product', 'order_item_h.product_id', '=', 'product.product_id')
            ->join('project', 'order_h.project_id', '=', 'project.project_id')
            ->select(
                'order_h.order_id',
                'order_item_h.item_order_status',
                'project.project_name',
                'product.product_name as item_name'
            )->count();

        // Log::debug("totalRecords: " . $totalRecords);

        $totalRecordsWithFilter = $orderDetails->where(function ($query) use ($searchValue) {
            $query->join('order_item_h', 'order_h.order_id', '=', 'order_item_h.order_id');
            $query->join('product', 'order_item_h.product_id', '=', 'product.product_id');
            $query->join('project', 'order_h.project_id', '=', 'project.project_id');
            $query->select(
                'order_h.order_id',
                'order_item_h.item_order_status',
                'project.project_name',
                'product.product_name as item_name'
            );
            $query->where('order_h.order_id', 'like', '%' . $searchValue . '%');
            $query->orWhere('item_order_status', 'like', '%' . $searchValue . '%');
            $query->orWhere('project_name', 'like', '%' . $searchValue . '%');
            $query->orWhere('product_name', 'like', '%' . $searchValue . '%');
        })->count();

        // Log::debug("totalRecordsWithFilter: " . $totalRecordsWithFilter);

        $records = $orderDetails->orderBy($columnName, $columnSortOrder)
            ->where(function ($query) use ($searchValue) {
                $query->join('order_item_h', 'order_h.order_id', '=', 'order_item_h.order_id');
                $query->join('product', 'order_item_h.product_id', '=', 'product.product_id');
                $query->join('project', 'order_h.project_id', '=', 'project.project_id');
                $query->select(
                    'order_h.order_id',
                    'order_item_h.item_order_status',
                    'project.project_name',
                    'product.product_name as item_name'
                );
                $query->where('order_h.order_id', 'like', '%' . $searchValue . '%');
                $query->orWhere('item_order_status', 'like', '%' . $searchValue . '%');
                $query->orWhere('project_name', 'like', '%' . $searchValue . '%');
                $query->orWhere('product_name', 'like', '%' . $searchValue . '%');
            })
            ->skip($start)
            ->take($rowPerPage)
            ->get();

        // Log::debug("records: ".$records);

        $data_arr = [];
        // $records = $orderDetails;

        foreach ($records as $key => $record) {

            if ($record->item_order_status == '1') {
                $status = '<td><span class="badge badge-phoenix badge-phoenix-success">Approved</span></td>';
            } else {
                $status = '<td><span class="badge badge-phoenix badge-phoenix-warning">Rejected</span></td>';
            }

            $hidden_id = '<td hidden class="user_id">' . $record->order_id . '</td>';

            $modify = '
                <td class="text-end">
                    <div class="actions">
                        <a href="#" class="btn btn-sm bg-danger-light">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <a class="btn btn-sm bg-danger-light delete user_id" data-bs-toggle="modal" data-user_id="' . $record->order_id . '" data-bs-target="#plannerDelete">
                        <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>
                </td>
            ';

            $data_arr[] = [
                "order_id"         => $record->order_id,
                "status"        => $status, //$record->item_order_status,
                "project_name"  => $record->project_name,
                "item"          => $record->item_name,
                // "active_flag"       => $status,
                "modify"        => $modify,
            ];
        }

        $response = [
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordsWithFilter,
            "aaData"               => $data_arr
        ];

        // dd(response()->json($response));
        return response()->json($response);
    }  //getPlannerData


}  // end of class
