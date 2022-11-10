@extends('layouts.app')
@section('content')
@section('head')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css" />
    
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.css" />
    
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" />
@endsection

<div class="container">
    <div class="row justify-content-center">
        
        <div class="col-md-8" style="margin-bottom: 2%">
          <h1><i class="fa-solid fa-user-pen"></i> Edit</h1>
        
        </div>
        <div class="col-md-8">
            <div class="card">
                {{-- <div class="card-header">{{ __('Edit', ['name'=>$user->name]) }}</div> --}}
                <div class="card-header">Update record of {{ucwords($user->name)}}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        {{ method_field('PUT') }}
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" pattern="[A-Za-z]{3,25}" title="Only uppercase or lowercase letters, minimum 3 characters." class="form-control @error('name') is-invalid @enderror" name="name" value="{{$user->name}}"  autocomplete="on" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="surname" class="col-md-4 col-form-label text-md-end">{{ __('Surname') }}</label>

                            <div class="col-md-6">
                                <input id="surname" type="text" pattern="[A-Za-z]{3,25}" title="Only uppercase or lowercase letters, minimum 3 characters." class="form-control @error('surname') is-invalid @enderror" name="surname" value="{{$user->surname}}"  autocomplete="on" autofocus>

                                @error('surname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="phone_number" class="col-md-4 col-form-label text-md-end">{{ __('Phone number') }}</label>

                            <div class="col-md-6">
                                <input id="phone_number" type="tel" pattern="[0-9]{3}-[0-9]{3}-[0-9]{3}" placeholder="xxx-xxx-xxx" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="{{$user->phone_number}}"  autocomplete="on" autofocus>

                                @error('phone_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{$user->email}}"  autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"  autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation"  autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row">
                                <label for="role" class="col-md-4 col-form-label text-md-end">Role</label>
    
                                <div class="col-md-6">
                                    <select name="role" id="role" class="form-control @error('role') is-invalid @enderror">
                                        @if($user->role == 'admin')
                                        <option value="admin" selected>Administrator</option> 
                                        <option value="moderator">Moderator</option> 
                                        <option value="user">User</option>
                                        
                                        @elseif($user->role == 'moderator')
                                        <option value="admin" >Administrator</option>
                                        <option value="moderator" selected>Moderator</option> 
                                        <option value="user">User</option>

                                        @else
                                        <option value="admin" >Administrator</option>
                                        <option value="moderator">Moderator</option> 
                                        <option value="user" selected>User</option>

                                        @endif
                                    
                                    </select>
                                    @error('role')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                               <div class="col-md-6 offset-md-4">
                                       <button type="submit" style="margin-top: 10px" onclick="return confirm('Are you sure you want to update {{ucwords($user->name)}}\'s account?')" class="btn btn-success"><i class="fa-solid fa-check-double"></i>
                                           Submit
                                        </button>
                                </div>
                            </div>
                        </form>
                       
                        <div class="form-group row mb-0" style="margin-top: 10px">
                           <div class="col-md-6 offset-md-4">
                               <a href="{{route('users.index')}}">
                                   <button  class="btn btn-primary"><i class="fa-solid fa-angles-left"></i>
                                       Back to List
                                   </button>
                               </a>
                           </div>
                      </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
