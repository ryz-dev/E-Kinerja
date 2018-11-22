<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>E-Kinerja Kabupaten Kolaka</title>
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
    <!-- endinject -->
    <link rel="stylesheet" href="{{ asset('assets/css/datepicker3.css') }}">
</head>

<body>
    <section class="login">
        <div class="bg-login">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center logo">
                        <img src="{{ asset('assets/images/logo.svg') }}" alt="logo" width="180">
                    </div>
                </div>
                <div class="img-bg">
                    <img src="{{ asset('assets/images/icons/anoa.svg') }}" alt="anoa" width="60%">
                </div>
                <div class="row">
                    <div class="offset-md-4 col-md-4">
                    <form method="POST" action="{{ route('login') }}">
                        <div class="box-login">
                            <h3 class="mb-4 text-center">Masuk</h3>
                            <div class="no-induk mb-3">
                                <span><i class="far fa-user-circle"></i></span>
                                <input type="text" name="nip" class="form-control" placeholder="No. Induk Pegawai" value="{{ old('nip') }}">
                            </div>
                            <div class="sandi mb-3">
                                <span><i class="fas fa-unlock-alt"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="Kata sandi">
                                @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                                @endif
                            </div>
                            @if ($errors->has('nip'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ $errors->first('nip') }}
                                </div>
                            @endif
                            <div class="ask mb-2">
                                <label class="container-check">Ingat saya
                                    <input type="checkbox" name="remeber" {{ old('remember') ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                                <a href="">Lupa kata sandi ?</a>
                            </div>
                            <div class="clearfix"></div>
                            <button class="btn btn-default btn-block">Masuk</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- inject:js -->
    <script src="{{ asset('assets/js/bundle.min.js') }}"></script>
    <!-- endinject -->
</body>

</html>