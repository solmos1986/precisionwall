@extends('layouts.panel')
@push('css-header')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
<link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
<link rel="stylesheet" href="{{ asset('css/table-responsive.css') }}">
@endpush
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="div-error container-fluid" id="validate" style="display: none;">
            <ul class="alert alert-danger ul-error">
            </ul>
        </div>
        <div class="ms-panel">
            <div class="ms-panel-header ms-panel-custome">
                <h6>Edit Question</h6>
            </div>
            <div class="ms-panel-body">
                <form id="from_question" method="post" action="{{ route('update.questions', ['id' => $id]) }}">
                    @csrf
                    <div class="form-group">
                        <label for="nombre">Name:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control form-control-sm"
                            value="{{ $question->nombre }}">
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Description:</label>
                        <textarea name="descripcion" id="descripcion"
                            class="form-control form-control-sm">{{ $question->descripcion }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="nombre">Input:</label>
                        <select name="t_input" id="t_input" class="form-control form-control-sm">
                            <option value="radio" {{ $question->t_input == 'radio' ? 'selected' : '' }}>input radio
                            </option>
                            <option value="checkbox" {{ $question->t_input == 'checkbox' ? 'selected' : '' }}>input
                                checkbox</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="btn btn-sm btn-info float-right mb-3 add_tr">Add
                                Options</div>
                        </div>
                    </div>
                    <table class="table" id="table-values">
                        <thead>
                            <tr>
                                <td scope="col">Option</td>
                                <td scope="col">*</td>
                            </tr>
                            <a href=""></a>
                        </thead>
                        <tbody>
                            @foreach ($options as $val)
                            <tr>
                                <td data-label="Title:"><input type="text" name="title[]"
                                        class="form-control form-control-sm title" value="{{ $val->titulo }}">
                                </td>
                                <td data-label="*">
                                    <div class="ms-btn-icon btn-danger btn-sm remove_tr">
                                        <i class="fas fa-trash-alt mr-0"></i></div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="ms-panel-footer">
                <button class="btn btn-success d-block" type="submit" id="enviar">Save and Continue</button>
            </div>
        </div>
    </div>
</div>

@endsection
@push('javascript-form')
<script src="{{ asset('js/select2.min.js') }}"></script>
<script>
    td_question = `<tr>
                                                <td data-label="Title:"><input type="text" name="title[]" class="form-control form-control-sm title"></td>
                                                <td data-label="*"> <div class="ms-btn-icon btn-danger btn-sm remove_tr"><i class="fas fa-trash-alt mr-0"></i></div></td>
                                    </tr>`;

        $('.select').select2();
        $(".add_tr").on('click', function() {
            console.log('click');
            $("#table-values tbody").append(td_question);
        });
        $("#enviar").click(function(e) {
            $(this).prop("disabled", true);
            e.preventDefault();
            send_form();
        });
        $(document).on("click", ".remove_tr", function() {
            $(this).parents("tr").remove();
        });

        function send_form() {
            let $form = $('#from_question');
            $.ajax({
                type: "POST",
                url: $form.attr('action'),
                data: $form.serialize(),
                dataType: "json",
                success: function(data) {
                    if (data.errors.length > 0) {
                        $alert = "complete the following fields to continue:\n";
                        data.errors.forEach(function(error) {
                            $alert += `* ${error}\n`;
                        });
                        alert($alert);
                        $('#enviar').prop("disabled", false);
                    } else {
                        $form.submit();
                    }
                }
            });
        }

</script>
@endpush