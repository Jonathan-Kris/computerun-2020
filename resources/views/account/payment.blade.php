@extends('layouts.app')

@section('content')
<div class="container">
    <?php
        $requireTwibbonUpload = false;
        for ($i = 0; $i < count($requests); $i++){
            if ($requests[$i]->event_id == 1 || $requests[$i]->event_id == 2){
                $requireTwibbonUpload = true;
                break;
            }
        }
    ?>
    @if (session('status'))
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            </div>
        </div>
        <?php
            Session::forget('status');
        ?>
    @endif
    @if (session('error'))
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            </div>
        </div>
        <?php
            Session::forget('error');
        ?>
    @endif
    <h1 class="full-underline">Complete Payment for {{$paymentcode}}</h1>
    <p class="h4 text-center content-divider-short">Once the documents have been approved, you will eligible for participating in:</p>
    <div class="row justify-content-center content-divider-short">
        <div class="col-md-8 p-0 row justify-content-center">
        @foreach ($requests as $request)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <b>Ticket No:</b> {{$request->id}}
                    </div>
                    <div class="card-body">
                        <h4 class="font-700">{{$request->event_name}}</h4>
                        <h5>for <b>{{$request->user_name}}</b></h5>
                        <b>Remarks:</b> {{$request->remarks}}
                    </div>
                </div>
            </div>
        @endforeach
        </div>

        <div class="col-md-8">
            <h1 class="full-underline content-divider-short">Instructions</h1>
            <div class="card content-divider-short">
                <div class="card-body">
                    @if ($requireTwibbonUpload == true)
                        If you are registering for <b>Business-IT Case</b> and <b>Mobile Application Development</b> Competitions, please attach the following in a ZIP file:
                        <ul>
                            <li>Your Payment Receipt</li>
                            <li>Student ID Card of you and your team members (Verifikasi Kartu Tanda Mahasiswa)</li>
                            <li>Screenshot of Twibbon Upload on Instagram Feeds (you and your team members)</li>
                        </ul>
                        <img class="width-100" src="/docs/Verifikasi KTM.jpg">
                    @else
                        Please upload a picture/screenshot of your payment receipt
                    @endif
                </div>
            </div>
            <h1 class="full-underline content-divider-short">Upload Documents</h1>
            <div class="card content-divider-short">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row">
                            <label for="file" class="col-md-4 col-form-label text-md-right">File (JPG, PNG, PDF, or ZIP)</label>
                            <div class="col-md-6">
                                <input id="file" type="file" class="form-control @error('file') is-invalid @enderror" name="file" value="{{ old('file') }}" required accept="application/zip,image/*,application/pdf">

                                @error('file')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <p class="red-text h5 text-center">You will be able to upload files before we have confirm and validated your registration. If you wish to reupload, your previous uploads will be overriden!</p>

                        <div class="form-group text-center">
                            <button type="submit" class="button button-gradient">
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
