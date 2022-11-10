@extends('layouts.app')
@section('content')
@section('head')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css" />
    
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.css" />
    
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" />
@endsection

<div class="container">
  <div class="row">
    
    <div class="col-6" style="margin-bottom: 2%">
      <h1><i class="fa-solid fa-users"></i> Users list</h1>
    </div>
 

  @if(session('message'))
    <div class="alert alert-success">{{session('message')}}</div>
  @endif

<table class="table table-hover" style="width:100%">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Email</th>
      <th scope="col">Name</th>
      <th scope="col">Surname</th>
      <th scope="col">Phone number</th>
      <th scope="col">Role</th>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody>
   @foreach($users as $user)
    <tr>
      <th scope="row">{{$user -> id}}</th>
      <td>{{$user->email}}</td>
      <td>{{$user->name}}</td>
      <td>{{$user->surname}}</td>
      <td>{{$user->phone_number}}</td>
      <td>{{$user->role}}</td>
      <td>
        <a href="{{route('users.edit', $user->id)}}">
           {{-- url('/users/edit') --}}
          <button class="btn btn-success btn-sm"><i class="fa-solid fa-pen"></i></button>
          {{-- <i class="far fa-edit"></i> --}}
        </a> 
        <button class="btn btn-danger btn-sm delete" data-id="{{$user -> id}}"><i class="fa-solid fa-trash-can"></i></button>
      </td>
      <td></td>
    </tr>
      @endforeach
  </tbody>
</table>
{{$users->links()}}
</div>
@endsection


@section('javascript')
const deleteURL = "{{url ('users')}}/";
@endsection
@section('js-files')
    <script src="{{ asset('js/delete.js') }}"></script>

@endsection
