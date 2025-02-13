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
                    <h6>Edit Evaluation</h6>
                </div>
                <div class="ms-panel-body">
                    <form id="form_evaluation" method="POST" enctype="multipart/form-data"
                          action="{{ route('update.evaluations', ['id' => $id]) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="title">Title:</label>
                            <input type="text" name="title" id="title" class="form-control form-control-sm"
                                   value="{{ $evaluacion->titulo }}">
                        </div>
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea name="description" id="description"
                                      class="form-control form-control-sm">{{ $evaluacion->descripcion }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="areas">Areas:</label>
                            <select name="areas[]" id="areas" class="select form-control" multiple>
                                @foreach ($areas as $key => $area)
                                    <option value="{{ $area->how_areas_id }}"
                                            {{ in_array($area->how_areas_id,$evaluacion->areas()->pluck('how_areas_id')->toArray())  ? 'selected' : '' }}>
                                        {{ $area->nombre }}</option>
                                @endforeach
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
                                    <td scope="col" width="40%">Question</td>
                                    <td scope="col">Input</td>
                                    <td scope="col">N° Options</td>
                                    <td scope="col">*</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @foreach ($evaluacion->questions()->withCount('options')->get() as $val)
                                        <td data-label="Question:">
                                            <select name="questions[]" class="form-control form-control-sm questions_id">
                                                <option value="{{ $val->question_id }}">{{ $val->nombre }}</option>
                                            </select>
                                        </td>
                                        <td data-label="Input:"><span class="inputs">{{ $val->t_input }}</span></td>
                                        <td data-label="N° Options:"><span
                                                  class="options">{{ $val->options_count }}</span></td>
                                        <td data-label="*">
                                            <div class="ms-btn-icon btn-danger btn-sm remove_tr"><i
                                                   class="fas fa-trash-alt mr-0"></i></div>
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
        $('.select').select2();
        var td_question = `<tr>
        <td data-label="Question:"><select name="questions[]" class="form-control form-control-sm questions_id"></select></td>
        <td data-label="Input:"><span class="inputs"></span></td>
        <td data-label="N° Options:"><span class="options"></span></td>
        <td data-label="*"> <div class="ms-btn-icon btn-danger btn-sm remove_tr"><i class="fas fa-trash-alt mr-0"></i></div></td>
        </tr>`;
        select();

        $("#enviar").click(function(e) {
            $(this).prop("disabled", true);
            e.preventDefault();
            send_form();
        });
        $(document).on("click", ".remove_tr", function() {
            $(this).parents("tr").remove();
        });
        $(".add_tr").on('click', function() {
            console.log('click');
            $("#table-values tbody").append(td_question);
            select();
        });

        function send_form() {
            let $form = $('#form_evaluation');
            $.ajax({
                type: "PUT",
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

        function select() {
            $('.questions_id').select2({
                theme: "bootstrap4",
                ajax: {
                    url: `${base_url}/get_questions`,
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            }).on('select2:select', function(e) {
                $(this).parents('tr').find(".options").html(e.params.data['count_question']);
                $(this).parents('tr').find(".inputs").html(e.params.data['inputs']);

            });
        }

    </script>
@endpush
