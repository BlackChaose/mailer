@extends('layouts.app')
@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('content')
    <div class="container align-content-center">
        @if($mailing->status === null)
            <h3>Создать новую рассылку</h3>
            {{ Form::model($mailing, ['url' => route('mailer.store'),'method'=>'POST', 'class'=>'needs-validation', 'validate'=>'validate','files'=>true]) }}
            @csrf
            @include('admin.form')
            <div class="form-group">
                {{ Form::submit('Создать',['class'=>'btn btn-primary']) }}
            </div>
            {{ Form::close() }}
        @endif
        @if($mailing->status === 'new' && $form_type === 'edit')
            <h3>Редактировать рассылку "{{$mailing->mailing_name}}"</h3>
            {{ Form::model($mailing, ['url' => route('mailer.update',[$mailing->id]),'method'=>'PUT', 'class'=>'was-validated', 'validate'=>'validate','files'=>true]) }}
            @csrf
            @include('admin.form')
            <div class="form-group">
                {{ Form::submit('Сохранить',['class'=>'btn btn-danger'])}}
                {{ Form::reset('Не сохранять',['class'=>'btn btn-success']) }}
            </div>
            {{ Form::close() }}
        @endif
        @if($mailing->status !==null && $form_type === 'show')
            <h3>Рассылка:"{{$mailing->mailing_name}}"</h3>
            @if($mailing->status ==='new')
                <div class="btn btn-primary m-1" id="btn_run_mailing">
                    Запустить отправку рассылки!
                </div>
            @endif
            <table class="table">
                <thead>
                <th>id</th>
                <th>статус</th>
                <th>название рассылки</th>
                <th>тип</th>
                <th>список адресов e-mail</th>
                <th>отправитель</th>
                <th>дата отправки</th>
                <th>дата создания</th>
                <th>дата обновления</th>
                @if($mailing->status === 'new')
                    <th>edit</th>
                    <th>delete</th>
                @endif
                </thead>
                <tbody>
                <tr>
                    <td>{{$mailing->id}}</td>
                    <td><span class="badge badge-warning p-1">{{$mailing->status}}</span></td>
                    <td>{{$mailing->mailing_name}}</td>
                    <td>
                        @if($mailing->mode ==='mode1')
                            <span class="badge badge-secondary">на один адрес</span>
                        @elseif($mailing->mode === 'mode2')
                            <span class="badge badge-primary">выборочно</span>
                        @elseif($mailing->mode === 'mode3')
                            <span class="badge badge-secondary">всем пользователям</span>
                        @endif
                    </td>
                    <td>
                        @if($mailing->list_of_emails)
                            <div class="col-2">
                                @php
                                    $emails_list = explode(';', $mailing->list_of_emails);
                                @endphp
                                <ul class="list-unstyled mh-5">
                                    @foreach($emails_list as $email)
                                        <li><span class="badge badge-primary">{{$email}}</span></li>
                                    @endforeach
                                </ul>
                            </div>
                        @elseif($mailing->email_address)
                            <span class="badge badge-primary">{{$mailing->email_address}}</span>
                        @else
                            --
                        @endif
                    </td>
                    <td><span
                            title="{{$user->find($mailing->sender)->email}}">{{$user->find($mailing->sender)->name}}</span>
                    </td>
                    <td><span class="badge badge-success">{{$mailing->sended_at}}</span></td>
                    <td><span class="badge badge-secondary">{{$mailing->created_at}}</span></td>
                    <td><span class="badge badge-secondary">{{$mailing->updated_at}}</span></td>
                    @if($mailing->status === 'new')
                        <td><a class="btn btn-primary" href="{{route('mailer.edit',['mailer'=>$mailing->id])}}">edit</a>
                        </td>
                        <td>{{Form::model($mailing, ['url' => route('mailer.destroy',['mailer'=>$mailing->id]),'method'=>'DELETE','id'=>'form-delete']) }}
                            @csrf
                            {{Form::submit('удалить',['class'=>'btn btn-danger'])}}
                            {{Form::close()}}
                        </td>
                    @endif
                </tr>
                </tbody>
            </table>
        @endif
        @if($mailing->status === 'deleted' && $form_type === 'delete' && $result === true)
            <div class="alert alert-success">
                <span>Рассылка была удалена!</span>
            </div>
            <a class="btn btn-primary" href="{{route('mailer.index')}}">к списку рассылок</a>
        @endif
        @if($mailing->status !=='deleted' && $form_type === 'delete')
            <div class="alert alert-danger">
                <span>Ошибка! Что-то пошло не так!</span>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endif
    </div>
@endsection
@section('scripts')
    <script>
        (function () {
            window.addEventListener('load', function () {
               @if($form_type!=='show')
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function (form) {
                    form.addEventListener('submit', function (event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);

                });
                document.getElementById('mode1').addEventListener('click', function (event) {
                    document.getElementById('gr-mode1').className = "form-group";
                    document.getElementById('gr-mode2').className = "form-group d-none";
                    document.getElementById('input-mode1').required = true;
                    document.getElementById('input-mode2').required = false;
                    document.getElementById('input-mode2').disabled = false;
                });
                document.getElementById('mode2').addEventListener('click', function (event) {
                    document.getElementById('gr-mode1').className = "form-group d-none";
                    document.getElementById('gr-mode2').className = "form-group";
                    document.getElementById('input-mode1').required = false;
                    document.getElementById('input-mode2').required = true;
                    document.getElementById('input-mode2').disabled = false;
                    document.getElementById('input-mode2').className = 'form-control is-invalid';
                    document.getElementById('input-mode1').value = null;
                });
                document.getElementById('mode3').addEventListener('click', function (event) {
                    document.getElementById('gr-mode1').className = "form-group d-none";
                    document.getElementById('gr-mode2').className = "form-group ";
                    document.getElementById('input-mode1').required = false;
                    document.getElementById('input-mode2').required = false;
                    document.getElementById('input-mode2').disabled = true;
                    document.getElementById('input-mode2').className = 'form-control is-valid';
                    document.getElementById('input-mode1').value = null;
                });
                if(document.getElementById('mode3').checked){
                    document.getElementById('gr-mode1').className = "form-group d-none";
                    document.getElementById('gr-mode2').className = "form-group";
                    document.getElementById('input-mode2').disabled = true;
                    document.getElementById('input-mode1').required = false;
                    document.getElementById('input-mode1').value = null;
                }
                if(document.getElementById('mode2').checked){
                    document.getElementById('gr-mode1').className = "form-group d-none";
                    document.getElementById('gr-mode2').className = "form-group ";
                    document.getElementById('input-mode2').disabled = false;
                    document.getElementById('input-mode2').required = false;
                    document.getElementById('input-mode1').value = null;
                }
                if(document.getElementById('mode1').checked){
                    document.getElementById('gr-mode1').className = "form-group ";
                    document.getElementById('gr-mode2').className = "form-group d-none";
                }
                @endif
                @if($form_type==='show' && $mailing->status==='new')
                document.getElementById('form-delete').addEventListener('click', function (event) {
                    alert('данная рассылка будет удалена!')
                })
                    @endif
                @if($mailing->status ==='new' && $form_type!=='edit')
                document.getElementById('btn_run_mailing').addEventListener('click',function(e){
                    if(confirm('Запустить рассылку?')) {
                        var xhttp = new XMLHttpRequest();
                        xhttp.onreadystatechange = function () {
                            if (this.readyState == 4 && this.status == 200) {
                                document.getElementById('btn_run_mailing').display = 'none';
                                window.location.reload();//FIXME: <+++++++++++++++++======= TODO:comment for debug!
                            }
                        };
                        xhttp.open("POST", "{{route('run_mailing')}}", true);
                        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        xhttp.setRequestHeader("X-CSRF-TOKEN", document.querySelector('meta[name="csrf-token"]').content);
                        xhttp.send("_token={{csrf_token()}}&mailing_num={{$mailing->id}}");
                    }
                });
                @endif

                @if(($mailing->status ==='new' && $form_type === 'edit' ) || $form_type ==='create')
                var item_file_cnt = 1;
                var files_for_delete = [];
                document.getElementById('attach_file_section').addEventListener('click', function (e) {
                    var attach_file = document.getElementById('files_list');
                    var wrap_x = document.createElement("li");
                    var btn_x = document.createElement("div");
                    var x = document.createElement("input");

                    btn_x.className = "btn btn-danger";
                    btn_x.textContent = "удалить";
                    btn_x.addEventListener('click', function () {
                        this.parentElement.remove();
                    });
                    x.setAttribute("type", "file");
                    x.name = 'attached_file_' + item_file_cnt;
                    wrap_x.appendChild(x);
                    wrap_x.appendChild(btn_x);
                    attach_file.appendChild(wrap_x);
                    item_file_cnt += 1;
                });
                @endif

                @if($form_type === 'edit' && count($attached_files)!==0)
                let obj = document.getElementById('files_list_old').querySelectorAll('li');
                Object.keys(obj).forEach(function (index) {
                    obj[index].querySelector('[class="btn btn-danger btn-sm"]').addEventListener('click', function (e) {
                        files_for_delete.push(obj[index].dataset.id);
                        document.getElementById('arr_files_for_del').value = files_for_delete;
                        e.currentTarget.nextElementSibling.style.textDecoration = "line-through";
                    });
                });
                document.querySelector('[type="reset"]').addEventListener('click', function () {
                    document.getElementById('arr_files_for_del').value = null;
                    let reload_files_list = document.getElementById('files_list_old').querySelectorAll('span');
                    Object.keys(reload_files_list).forEach(function (index) {
                        reload_files_list[index].style.textDecoration = "none";
                    });
                });
                @endif
            }, false);

        })();
    </script>
@endsection

