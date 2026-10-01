<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? __('messages.pushnotification_settings') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('sendPushNotification'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}

                        <div class="row">
                            <div class="col-lg-12"> 
                                <div class="form-group">
                                    <label for="" class="col-sm-6 form-control-label">{{ __('messages.title') }} <span class="text-danger">*</span></label>
                                    <div class="col-sm-12">
                                        {{ html()->text('title')->class('form-control')->required()->placeholder(__('messages.title')) }}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="is_type" class="col-sm-6 form-control-label">{{ __('messages.user_type') }}</label>
                                    <div class="col-sm-12">
                                        <select name="is_type" id="is_type" class="form-control select2js" required>
                                            <option value="user">{{ __('messages.user') }}</option>
                                            <option value="provider">{{ __('messages.provider') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group" id="type_div">
                                    <label for="type" class="col-sm-6 form-control-label">{{ __('messages.type') }}</label>
                                    <div class="col-sm-12">
                                        <select name="type" id="type" class="form-control select2js">
                                            <option value="alldata">{{ __('messages.all') }}</option>
                                            <option value="service">{{ __('messages.service') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group" id="service_div" style="display: none;">
                                    <label for="service_id" class="col-sm-6 form-control-label">{{ __('messages.service') }}</label>
                                    <div class="col-sm-12">
                                        <select name="service_id" id="service_id" class="form-control select2js">
                                            @if(isset($services))
                                                @foreach($services as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-6 form-control-label">{{ __('messages.description') }} <span class="text-danger">*</span></label>
                                    <div class="col-sm-12">
                                        {{ html()->textarea('description')->class('form-control textarea')->rows(3)->required()->placeholder(__('messages.description')) }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-12 mt-3"> 
                                <div class="form-group">
                                    <div class="col-md-offset-3 col-sm-12 ">
                                        {{ html()->submit(__('messages.send'))->class('btn btn-md btn-primary float-md-end') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('bottom_script')
    <script>
        $(document).ready(function() {
            $('#is_type').change(function() {
                if($(this).val() == 'provider') {
                    $('#type_div').hide();
                    $('#service_div').hide();
                } else {
                    $('#type_div').show();
                    if($('#type').val() == 'service') {
                        $('#service_div').show();
                    } else {
                        $('#service_div').hide();
                    }
                }
            });

            $('#type').change(function() {
                if($(this).val() == 'service') {
                    $('#service_div').show();
                } else {
                    $('#service_div').hide();
                }
            });
        });
    </script>
    @endsection
</x-master-layout>
