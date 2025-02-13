@extends('layouts.panel')
@push('css-header')
<!-- Page Specific Css (Datatables.css) -->
 <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="ms-panel">
        <div class="ms-panel-header ms-panel-custome">
          <h6>Customer</h6>
          
        </div>
        <div class="ms-panel-body">
          <div class="table-responsive">
            <table id="list-proyect" class="table table-striped thead-primary w-100">
              <thead>
                <tr>
                    <th>#</th>
                    <th>CI</th>
                    <th>Name</th>
                    <th>Last name</th>
                    <th>Telephone</th>
                    <th>Direction</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
    </div>
  </div>
</div>
@endsection
