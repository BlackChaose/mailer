@extends('layouts.app')
@section('content')
    <div class="container align-content-center">
        <div class="row justify-content-start m-1">
            <div class="col2 align-self-start">
                <button class="btn btn-primary" id="add_new_mailing">добавить новую рассылку</button>
            </div>
        </div>
        <table class="table">
            <thead>
            <th>№ п/п</th>
            <th>название рассылки</th>
            <th>список адресов</th>
            <th>отправитель</th>
            <th>отправлено</th>
            <th>посмотреть</th>
            </thead>
            <tbody>
            @foreach($mailing as $item)
                <tr>
                    <td >{{$loop->iteration}}</td>
                    <td style="max-width: 100px; max-height: 50px; word-break: break-word; overflow: auto; ">{{$item->mailing_name}}</td>
                    <td >
                        @if($item->list_of_emails)
                            <div style="max-height:100px; word-break: break-word; overflow-y: auto; ">
                                @php
                                    $emails_list = explode(';', $item->list_of_emails);
                                @endphp
                                <ul class="list-unstyled" >
                                    @foreach($emails_list as $email)
                                        <li><span class="badge badge-primary">{{$email}}</span></span></li>
                                    @endforeach
                                </ul>
                            </div>
                        @elseif($item->email_address)
                            <span class="badge badge-primary"> {{$item->email_address}}</span>
                        @else
                            --
                        @endif
                    </td>
                    <td >{{$item->sender}}</td>
                    <td ><span class="badge badge-success">{{$item->sended_at}}</span></td>
                    <td ><a class="btn btn-primary" href="{{route('mailer.show',['mailer'=>$item->id])}}">посмотреть</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
@section('scripts')
    <script>
        (function () {

            window.addEventListener('load', function () {
                document.getElementById('add_new_mailing').addEventListener('click', function (event) {
                    window.location.href = '{{route('mailer.create')}}';
                });
            });

        })();
    </script>
@endsection
