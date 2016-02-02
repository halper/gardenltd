<div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('name', 'Kullanıcı adı: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Kullanıcı adı giriniz']) !!}
        </div>


    </div>
</div>

<div class="form-group">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('admin', 'Yönetici Hesabı: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">

            <label class="checkbox-inline">
                {!! Form::checkbox('admin', null,
                isset($user) ? $user->isAdmin() : false,
                [
                'id'=>'admin-cb',
                ])
                !!}Yönetici hakkı ver</label>
        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('email', 'E-Posta: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Bir e-posta adresi girin']) !!}

            @if($errors->first('email') == "The email has already been taken.")
                {!! '<span class="help-block">E-posta adresi zaten kayıtlı</span>' !!}
            @endif

        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('employer') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('employer', 'Firma ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::text('employer', null, ['class' => 'form-control', 'placeholder' => 'Kullanıcının firmasını giriniz']) !!}
        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('password', 'Şifre ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            <input type="password" name="password" class="form-control" placeholder="Şifre">
            <span class="help-block">Şifreniz en az 6 karakterden oluşmalı</span>

        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('password_confirmation', 'Şifre tekrar ', ['class' => 'control-label']) !!}

        </div>
        <div class="col-sm-10">
            <input type="password" name="password_confirmation" class="form-control"
                   placeholder="Şifreyi tekrar giriniz">
            @if($errors->first('password') == "The password confirmation does not match.")
                {!! '<span class="help-block">Girmiş olduğunuz şifreler uyuşmuyor</span>' !!}
            @endif
        </div>
    </div>
</div>

