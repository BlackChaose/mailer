<?php

namespace App\Http\Controllers;

use App\Models\Mailing;
use App\User;
use App\Models\AttachedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Mail\Mailing1;
use Illuminate\Support\Facades\Mail;

class MailingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mailing = Mailing::all();
        $users = User::all();
        return view('admin.mailer_list', ["mailing" => $mailing, "users" => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        $users = User::all();
        $mailing = new Mailing();
        $form_type = 'create';
        return view('admin.mailer', ["current_user" => $user, "mailing" => $mailing, "users" => $users, 'form_type' => $form_type]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request, $request->files,$request->files, $request->file('attached_file_1')->getRealPath());
        $data = $this->validate($request, [
            "mode" => 'required',
            "email_address" => 'email:rfc,dns|nullable',
            "list_of_emails" => 'array',
            "list_of_emails.*" => 'email:rfc,dns',
            "subject" => 'string|min:3|required',
            "greetings" => 'min:3|max:255|required',
            "message" => 'min:10|max:2000|required',
            "signature" => 'min:10|max:255|required',
            "mailing_name" => 'required|min:3|max:255'
        ]);
        if (!empty($data['list_of_emails'])) {
            $data['list_of_emails'] = implode(';', $data['list_of_emails']);
        } else if ($data['mode'] === 'mode3') {
            $data['list_of_emails'] = implode(';', User::all()->pluck('email')->toArray());
        }

        $data['status'] = 'new';
        $data['sender'] = Auth::user()->id;
        $mailing = new Mailing();
        $mailing->fill($data);
        $mailing->save();
        //dd($request, $data);
        //dd($mailing->id,Carbon::now("Europe/Moscow")->isoFormat('Y_M_D__HH_mm_'));

        if (!empty($request->files && !empty($mailing->id))) {
            $destinationPathFolder = 'uploads/mailing_attaches' . Carbon::now('Europe/Moscow')->isoFormat('Y_M_D__HH_mm');
            $destinationPath = $destinationPathFolder . '_' . $mailing->id;
            foreach ($request->files as $file) {
                $file->move($destinationPath, $file->getClientOriginalName());
                $ff = new AttachedFile();
                $ff->mailing_id = $mailing->id;
                $ff->user_id = Auth::user()->id;
                $ff->path_to_file = $destinationPath . '/' . $file->getClientOriginalName();
                $ff->file_name = $file->getClientOriginalname();
                $ff->save();
            }
        }
        return redirect()->route('mailer.show', ['mailer' => $mailing->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Mailing $mailing
     * @return \Illuminate\Http\Response
     */
    public function show($mailer)
    {
        $mailing = Mailing::find($mailer);
        $user = Auth::user();
        $form_type = 'show';
        //dd($mailing,$mailer);
        return view('admin.mailer', ['mailing' => $mailing, 'user' => $user, 'form_type' => $form_type]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Mailing $mailing
     * @return \Illuminate\Http\Response
     */
    public function edit($mailer)
    {
        $mailing = Mailing::find($mailer);
        $user = Auth::user();
        $users = User::all();
        $form_type = 'edit';
        $attached_files = AttachedFile::with('mailing')->get();
        //dd($mailing->id, $attached_files->get(), $attached_files->where('mailing_id','=',$mailing->id)->get());
        //dd($mailing,$mailer);
        return view('admin.mailer', ['mailing' => $mailing, 'user' => $user, 'users' => $users, 'form_type' => $form_type, 'attached_files' => $attached_files]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Mailing $mailing
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $mailer)
    {
        $data = $this->validate($request, [
            "mode" => 'required',
            "email_address" => 'email:rfc,dns|nullable',
            "list_of_emails" => 'array',
            "list_of_emails.*" => 'email:rfc,dns',
            "subject" => 'string|min:3|required',
            "greetings" => 'min:3|max:255|required',
            "message" => 'min:10|max:2000|required',
            "signature" => 'min:10|max:255|required',
            "mailing_name" => 'required|min:3|max:255'
        ]);
        if (!empty($data['list_of_emails'])) {
            $data['list_of_emails'] = implode(';', $data['list_of_emails']);
        } else if ($data['mode'] === 'mode3') {
            $data['list_of_emails'] = implode(';', User::all()->pluck('email')->toArray());
        }

        $data['status'] = 'new';
        $data['sender'] = Auth::user()->id;
        $mailing = Mailing::find($mailer);

        if (!empty($request['files_for_delete'] && $request['files_for_delete'][0] !== null)) {
            //dd($request['files_for_delete']);
            $arr_id_of_files_for_delete = explode(',', $request['files_for_delete'][0]);
            //dd($arr_id_of_files_for_delete);
            foreach ($arr_id_of_files_for_delete as $del_id) {
                AttachedFile::find($del_id)->delete();
            }
        }

        if (!empty($request->files && !empty($mailing->id))) {
            $destinationPathFolder = 'uploads/mailing_attaches' . Carbon::now('Europe/Moscow')->isoFormat('Y_M_D__HH_mm');
            $destinationPath = $destinationPathFolder . '_' . $mailing->id;
            foreach ($request->files as $file) {
                $file->move($destinationPath, $file->getClientOriginalName());
                $ff = new AttachedFile();
                $ff->mailing_id = $mailing->id;
                $ff->user_id = Auth::user()->id;
                $ff->path_to_file = $destinationPath . '/' . $file->getClientOriginalName();
                $ff->file_name = $file->getClientOriginalname();
                $ff->save();
            }
        }

        $mailing->fill($data);
        $mailing->save();
        return redirect()->route('mailer.show', ['mailer' => $mailing->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Mailing $mailing
     * @return \Illuminate\Http\Response
     */
    public function destroy($mailer)
    {
        $mailing = Mailing::find($mailer);
        $result = $mailing->delete();
        if ($result) {
            $mailing->status = 'deleted';
            $mailing->save();
        }
        return view('admin.mailer', ['result' => $result, 'mailing' => $mailing, 'form_type' => 'delete']);
    }

    public function run_mailing(Request $request)
    {

        $data = $this->validate($request, [
            'mailing_num' => 'integer|required',
        ]);

        $message = Mailing::all()->find($data['mailing_num']);
        $files = AttachedFile::with('mailing')->get();
        $mailto_list = [];
        if ($message->mode === 'mode1') {
            $mailto_list[0] = $message->email_address;
        } else if ($message->mode === 'mode2' || $message->mode === 'mode3') {
            $mailto_list = explode(';', $message->list_of_emails);
        }
        $mes_to_send = $message->only(['greetings', 'subject', 'message', 'signature']);
        //FIXME:: get email or emal-list!
//        dd($mailto_list);
        $mail_errors = [];
        $mail_sent = [];
        $cur_mailing = Mailing::find($data['mailing_num']);
        foreach ($mailto_list as $mailto) {
            try {
                $res = Mail::to($mailto)->send(new Mailing1($mes_to_send, $files->toArray()));
                $mail_sent[]= ['mailto'=>$mailto,'status'=>'OK','res'=>$res];
                if (!empty(Mail::failures())) {
                    throw(new \Exception('Mail::failures is not null!'));
                }
            } catch (\Exception $e) {
                $mail_errors[] = ['mailto' => $mailto, 'error' => $e->getMessage()];
            }
        }
        if (count($mail_errors) === 0) {
            $cur_mailing->status = 'sent';
            $cur_mailing->sended_at = Carbon::now();
            $cur_mailing->sent_log = json_encode($mail_sent);
            $cur_mailing->save();
            $result_status="OK!";
        } else if (count($mail_errors) > 0) {
            $cur_mailing->status = 'sent_with_errors';
            $cur_mailing->sended_at = Carbon::now();
            $cur_mailing->sent_log = json_encode($mail_sent);
            $cur_mailing->error_log = json_encode($mail_errors);
            $cur_mailing->save();
            $result_status="Error!";
        }
        return json_encode(['res' => $result_status, 'errors' => $mail_errors]);
    }
}
