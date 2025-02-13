<div id="mailModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send {{ $title ?? '' }} to emails</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <span id="form_result"></span>
                <form id="mail">
                    @csrf
                    <input type="hidden" name="row_id" id="row_id">
                    <div class="form-group">
                        <label style="width: 100%">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="inline mt-1">TO: </div>
                                </div>
                                <div class="col-md-10">
                                    <select name="" id="all_email_to" class="inline">

                                    </select>
                                </div>
                            </div>
                        </label>
                        <input type="email" id="to" class="form-control tagsinput" data-role="tagsinput">
                    </div>
                    <div class="form-group">
                      <label style="width: 100%">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="inline mt-1">CC: </div>
                            </div>
                            <div class="col-md-10">
                                <select name="" id="all_email_cc" class="inline">

                                </select>
                            </div>
                        </div>
                    </label>
                        <input type="email" id="cc" class="form-control tagsinput" data-role="tagsinput">
                    </div>
                    <div class="form-group">
                        <input type="text" id="title_m" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Mail Body: </label>
                        <textarea id="body_m" class="form-control"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm" id="send_mail">Send Mail</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
