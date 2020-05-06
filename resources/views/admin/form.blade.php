@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@csrf
<div class="form-group">
    {{ Form::label('mailing_name', 'Комментарий, навание рассылки',['class'=>'form-label']) }}
    {{ Form::textArea('mailing_name',null,['class'=>'form-control is-invalid','rows'=>'2','required'=>'required','placeholder'=>'Новая рассылка']) }}
    @if($form_type==='create')
        <div class="invalid-feedback">
            Пожалуйста добавьте комментарий, название рассылки (в письме его не будет).
        </div>
        <div class="valid-feedback">
            ok!
        </div>
    @endif
    @if($form_type==='edit')
        <div class="invalid-feedback">
            Пожалуйста отредактируйте комментарий, название рассылки (в письме его не будет).
        </div>
    @endif
    @php
    if($form_type==='edit'){
        switch($mailing->mode){
            case 'mode1': $checked=['0'=>'checked','1'=>null, '2'=>null]; break;
            case 'mode2': $checked=['0'=>null,'1'=>'checked', '2'=>null]; break;
            case 'mode3': $checked=['0'=>null,'1'=>null, '2'=>'checked']; break;
                default: $checked=['0'=>'checked','1'=>null, '2'=>null];
        }
    }
    @endphp
{{--    FIXME:: посмотри что можно поменять на использвоание Form:: чтобы избавиться от @php--}}
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="mode" id="mode1" value="mode1" {{$checked[0]??''}}>
        <label class="form-check-label" for="mode11">отправить на один адрес</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="mode" id="mode2" value="mode2" {{$checked[1]??''}}>
        <label class="form-check-label" for="mode2">отправить списку адресатов</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="mode" id="mode3" value="mode3" {{$checked[2]??''}}>
        <label class="form-check-label" for="mode3">отправить всем адресатам</label>
    </div>
    <div class="form-group " id="gr-mode1">
        {{ Form::label('email_address', 'e-mail',['class'=>'form-label']) }}
        {{ Form::email('email_address',null,['class'=>'form-control is-invalid', 'id'=>'input-mode1','required'=>'required','placeholder'=>'е-маил@пример.ком']) }}
        @if($form_type==='create')
            <div class="invalid-feedback">
                Пожалуйста добавьте email адресата!
            </div>
            <div class="valid-feedback">
                ok!
            </div>
        @endif
        @if($form_type==='edit')
            <div class="invalid-feedback">
                Пожалуйста отредактируйте email адресата!
            </div>
        @endif
    </div>
    <div class="form-group d-none" id="gr-mode2">
        {{--            {{dd($users->pluck('id','name','email'))}}--}}
        {{ Form::label('list_of_emails', 'список e-mail',['class'=>'form-label']) }}
        {{ Form::select('list_of_emails[]',$users->pluck('name','email'), null,['id'=>'emails_list','multiple'=>'multiple','class'=>'custom-select is-invalid', 'id'=>'input-mode2']) }}
        @if($form_type==='create')
            <div class="invalid-feedback">
                Пожалуйста выберите адресатов!
            </div>
            <div class="valid-feedback">
                ok!
            </div>
        @endif
        @if($form_type==='edit')
            <div class="invalid-feedback">
                Пожалуйста выберите адресатов!
            </div>
        @endif

    </div>
    <div class="form-group">
        {{ Form::label('subject', 'Тема сообщения',['class'=>'form-label']) }}
        {{ Form::text('subject',null,['class'=>'form-control is-invalid','required'=>'required','placeholder'=>'Уведомляем Вас, что...']) }}
        @if($form_type==='create')
            <div class="invalid-feedback">
                Пожалуйста добавьте текст темы сообщения!
            </div>
            <div class="valid-feedback">
                ok!
            </div>
        @endif
        @if($form_type==='edit')
            <div class="invalid-feedback">
                Пожалуйста отредактируйте текст темы сообщения!
            </div>
        @endif
    </div>
    <div class="form-group">
        {{ Form::label('greetings', 'Шаблон обращения к Ф.И.О',['class'=>'form-label']) }}
        {{ Form::textArea('greetings',null,['class'=>'form-control is-invalid','rows'=>'3','required'=>'required','placeholder'=>'Дорогой клиент']) }}
        @if($form_type==='create')
            <div class="invalid-feedback">
                Пожалуйста добавьте шаблон обращения, например "Доброго времени суток! Уважаемый(-ая)
                {фамилия}{имя}{отчество}
            </div>

            <div class="valid-feedback">
                ok!
            </div>
        @endif
        @if($form_type==='edit')
            <div class="invalid-feedback">
                Пожалуйста отредактируйте шаблон обращения
            </div>
        @endif
    </div>
    <div class="form-group">
        {{ Form::label('message', 'Шаблон сообщения',['class'=>'form-label']) }}
        {{ Form::textArea('message',null,['class'=>'form-control is-invalid','rows'=>'7','required'=>'required','placeholder'=>'Текст сообщения']) }}
        @if($form_type==='create')
            <div class="invalid-feedback">
                Пожалуйста, добавьте текст сообщения!
            </div>
            <div class="valid-feedback">
                ok!
            </div>
        @endif
        @if($form_type==='edit')
            <div class="invalid-feedback">
                Пожалуйста, отредактируйте текст сообщения!
            </div>
        @endif
    </div>
    <div class="form-group">
        {{ Form::label('signature','Шаблон Подписи',['class'=>'form-label']) }}
        {{ Form::textArea('signature',null,['class'=>'form-control is-invalid','rows'=>'3','required'=>'required','placeholder'=>'С Уважением, ...']) }}
        @if($form_type==='create')
            <div class="invalid-feedback">
                Пожалуйста добавьте подпись!
            </div>
            <div class="valid-feedback">
                ok!
            </div>
        @endif
        @if($form_type==='edit')
            <div class="invalid-feedback">
                Пожалуйста отредактируйте подпись!
            </div>
        @endif
    </div>
    <div class="form-group">
        @if($form_type === 'edit' && count($attached_files)!==0)
            <strong>Список прикреплённых файлов:</strong>
            <ul style="list-style: none;" id="files_list_old">
                @foreach($attached_files as $item)
                    <li class="m-0 p-1" data-id="{{$item->id}}"><span class="btn btn-danger btn-sm">удалить</span>&nbsp;&nbsp;<span
                            class="text-success"><a href="{{url($item->path_to_file)}}" target="_blank">{{$item->file_name}}</a></span>
                    </li>
                @endforeach
            </ul>
            <input type="hidden" name="files_for_delete[]" id="arr_files_for_del">
        @endif
        <div class="btn btn-secondary" id="attach_file_section">Прикрепить файл</div>
        <div class="form-group m-0 p-1 border-1">
            <ul style="list-style: none;" id="files_list">
            </ul>
        </div>
        @if($form_type==='create')
            <div class="invalid-feedback">
                Пожалуйста прикрепите файлы, рамером не более 5Mb
            </div>
            <div class="valid-feedback">
                ok!
            </div>
        @endif
        @if($form_type==='edit')
            <div class="invalid-feedback">
                Пожалуйста прикрепите файлы, рамером не более 5Mb
            </div>
        @endif
    </div>
