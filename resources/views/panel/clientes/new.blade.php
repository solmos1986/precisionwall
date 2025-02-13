@extends('layouts.panel')
@push('css-header')

@endpush
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="ms-panel">
        <div class="ms-panel-header">
          <h6>Add Clientes</h6>
        </div>
        <div class="ms-panel-body">
            <div class="col-xl-12 col-md-12">
          <div class="ms-panel">
            
            <div class="ms-panel-body">
              <form class="needs-validation" novalidate>
                <div class="form-row">
                  <div class="col-md-6 mb-3">
                    <label for="validationCustom010">Identity card</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="validationCustom010" placeholder="Enter First Name"  required>

                    </div>
                  </div>

                    <div class="col-md-6 mb-3">
                      <label for="validationCustom020">Name</label>
                      <div class="input-group">
                        <input type="text" class="form-control" id="validationCustom020" placeholder="" required>
                        <div class="valid-feedback">
                          
                        </div>
                      </div>
                    </div>
                  </div>

                <div class="form-row">
                  <div class="col-md-6 mb-2">
                    <label for="validationCustom040">Last name</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="validationCustom040" placeholder="" required>

                    </div>
                  </div>

                  <div class="col-md-6 mb-2">
                    <label for="validationCustom030">Telephone</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="validationCustom030" placeholder="" required>

                    </div>
                  </div>
                </div>


                <div class="form-row">
                  <div class="col-md-6 mb-3">
                    <label for="validationCustom050">Direction</label>
                    <div class="input-group">
                      <input type="" class="form-control" id="validationCustom050" placeholder=""  required>

                    </div>
                  </div>
                   <div class="col-md-6 mb-2">
                    <label for="validationCustom030">Email</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="validationCustom030" placeholder="" required>

                    </div>
                  </div>
                
                </div>

                <button class="btn btn-warning mt-4 d-inline w-20" type="submit">Back</button>
                <button class="btn btn-primary mt-4 d-inline w-20" type="submit">Save</button>
              </form>

            </div>
          </div>
        </div>
        </div>
    </div>
  </div>
</div>
@endsection
