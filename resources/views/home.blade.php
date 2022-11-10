@extends('layouts.app')
@section('content')
@section('head')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css" />
    
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.css" />
    
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" />

        <style>
        .button {
          padding: 4px 8px;
          font-size: 16px;
          text-align: center;
          cursor: pointer;
          color: #fff;
          background-color: #045aaa;
          border: none;
          border-radius: 15px;
          box-shadow: 0 5px #999;
        }
        
        .button:hover {background-color: #3e8e41}
        
        .button:active {
          background-color: #3e8e41;
          box-shadow: 0 5px #666;
          transform: translateY(4px);
        }
        </style>
@endsection


<div class="container">

   @if(Gate::check('isAdmin') || Gate::check('isModerator'))
    
    <div class="col-6" style="margin-bottom: 2%">
        <h1><i class="fa-solid fa-file"></i> File</h1>
    </div>

   <div class="row justify-content-centre">

       <div class="col-md-8">

           <div class="card">

               <div class="card-header bgsize-primary-4 white card-header">

                   <h4 class="card-title">Import Excel Data</h4>

               </div>

               <div class="card-body">

                   @if ($message = Session::get('success'))

                       <div class="alert alert-success alert-block">

                           <button type="button" class="close" data-dismiss="alert">×</button>

                           <strong>{{ $message }}</strong>

                       </div>

                       <br>

                   @endif

                   <form action="{{url("import")}}" method="post" enctype="multipart/form-data">

                       @csrf

                       <fieldset>

                           <label>Select File to Upload  <small class="warning text-muted">{{__('Please upload only Excel (.xlsx, .xls or csv) files')}}</small></label>

                           <div class="input-group">

                               <input type="file" required class="form-control" name="uploaded_file" id="uploaded_file">

                               @if ($errors->has('uploaded_file'))

                                   <p class="text-right mb-0">

                                       <small class="danger text-muted" id="file-error">{{ $errors->first('uploaded_file') }}</small>

                                   </p>

                               @endif

                               <div style="margin-left: 1%">

                                   <button class="button" type="submit"><i class="ft-upload mr-1"></i><i class="fa-solid fa-upload"></i> Upload</button>

                               </div>

                           </div>

                       </fieldset>

                   </form>

               </div>

           </div>

       </div>

   </div>

   @endif

   <div class="row justify-content-left">

       <div class="col-md-12">

           <br />

           <div class="card">

               <div class="card-header bgsize-primary-4 white card-header">

                   <h4 class="card-title">File Data Table</h4>

               </div>

               <div class="card-body">

                <div>
                    <form action="{{url("search")}}" method="GET">
                        <div class="form-check form-check-inline" style="margin-left:3%">
                            <input class="form-check-input" type="checkbox" value="true" id="scr_name_real_checkbox" name="scr_name_real_checkbox" {{ $scr_name_real_checkbox ?? "" === "true" ? "checked" : "" }}>
                                <label class="form-check-label" for="scr_name_real_checkbox">
                                    Scr name real
                                </label>
                        </div>

                        <div class="form-check form-check-inline" style="margin-left:4.5%">
                            <input class="form-check-input" type="checkbox" value="true" id="external_partner_checkbox" name="external_partner_checkbox" {{ $external_partner_checkbox ?? "" === "true" ? "checked" : "" }}>
                                <label class="form-check-label" for="external_partner_checkbox">
                                    External partner
                                </label>
                        </div>

                        <div class="form-check form-check-inline" style="margin-left:3.3%">
                            <input class="form-check-input" type="checkbox" value="true" id="connection_no_checkbox" name="connection_no_checkbox" {{ $connection_no_checkbox ?? "" === "true" ? "checked" : "" }}>
                                <label class="form-check-label" for="connection_no_checkbox">
                                    Connection no.
                                </label>
                        </div>

                        <div class="form-group" style="margin-left:3%">
                            <input type="text" name="scr_name_real_input" style="height:30px" value="{{ $scr_name_real_input ?? ""}}">
                            <input type="text" name="external_partner_input" style="height:30px" value="{{ $external_partner_input ?? ""}}">
                            <input type="text" name="connection_no_input" style="height:30px" value="{{ $connection_no_input ?? ""}}">
                                <span style="margin-left: 1px">
                                    <button type="submit" class="button"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                                </span>
                        </div>
                    </form>
                </div>

                   <div style="margin-bottom: 1%">

                       <button class="button" style="margin-left:85%"><a href="{{url("export")}}" class="navbar-brand"><i class="fa-solid fa-file-export"></i> Export Excel Data</a></button>

                   </div>

                   <div class=" card-content table-responsive">

                       <table id="example" class="table table-striped table-bordered" style="width:100%">

                           <thead>
                           <th>Date</th>
                           <th>Time</th>
                           <th>Duration</th>
                           <th>LCRNo</th>
                           <th>External partner</th>
                           <th>External name</th>
                           <th>Scr no. invoice</th>
                           <th>Scr name invoice</th>
                           <th>Scr no. real</th>
                           <th>Scr name real</th>
                           <th>Connection no.</th>
                           <th>Charges</th>
                           <th>Direction</th>
                           <th>Bill type</th>
                           <th>Call type</th>
                           <th>Proj.</th>
                           <th>HotId</th>

                           </thead>

                           <tbody>
                               @if(!empty($data) && $data->count())
                               @foreach($data as $row)
                                   <tr>
                                       <td>{{ $row->Date}}</td>
                                       <td>{{ $row->Time}}</td>
                                       <td>{{ $row->Duration}}</td>
                                       <td>{{ $row->LCRNo}}</td>
                                       <td>{{ $row->External_partner}}</td>
                                       <td>{{ $row->External_name}}</td>
                                       <td>{{ $row->Scr_no_invoice}}</td>
                                       <td>{{ $row->Scr_name_invoice}}</td>
                                       <td>{{ $row->Scr_no_real}}</td>
                                       <td>{{ $row->Scr_name_real}}</td>
                                       <td>{{ $row->Connection_no}}</td>
                                       <td>{{ $row->Charges}}</td>
                                       <td>{{ $row->Direction}}</td>
                                       <td>{{ $row->Bill_type}}</td>
                                       <td>{{ $row->Call_type}}</td>
                                       <td>{{ $row->Proj}}</td>
                                       <td>{{ $row->HotId}}</td>

                                   </tr>

                               @endforeach

                           @else

                               <tr>

                                   <td colspan="10">There are no data.</td>

                               </tr>

                           @endif

                           </tbody>

                       </table>

                       {!! $data->links() !!}

                   </div>

               </div>

           </div>

       </div>

   </div>

</div>

@endsection
