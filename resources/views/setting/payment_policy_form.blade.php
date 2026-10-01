<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? 'Payment Policy' }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('payment-policy-save'))->attribute('data-toggle', 'validator')->open() }}
                        {{ html()->hidden('id', $setting_data->id ?? null) }}

                        @include('partials._language_toggale')
                        @foreach($language_array as $language)
                            <div id="form-language-{{ $language['id'] }}" class="language-form" style="display: {{ $language['id'] == app()->getLocale() ? 'block' : 'none' }};">
                                <div class="row">
                                    @foreach(['value' => 'Payment Policy'] as $field => $label)
                                        <div class="form-group col-md-12">
                                            {{ html()->label($label . ' <span class="text-danger">*</span>', $field)->class('form-control-label language-label') }}
                                            @php
                                                $val = $language['id'] == 'en' 
                                                    ? ($payment_policy ? $payment_policy : '') 
                                                    : ($setting_data ? $setting_data->translate($field, $language['id']) : '');
                                                $name = $language['id'] == 'en' ? $field : "translations[{$language['id']}][$field]";
                                            @endphp
                                            {{ html()->textarea($name, $val)
                                                ->class('form-control tinymce-payment_policy')
                                                ->rows(3)
                                                ->placeholder('Enter Payment Policy (HTML content or URL)') }}
                                            <small class="help-block with-errors text-danger"></small>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach 
                        <div class="form-group col-md-4">
                            <div class="form-control d-flex align-items-center justify-content-between">
                                <label for="status" class="mb-0">{{ __('messages.status') }}</label>
                                <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                                    <input type="checkbox" class="custom-control-input" name="status" id="status" value="1" {{ ($status ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="status"></label>
                                </div>
                            </div>
                        </div>
                        @if(auth()->user()->hasRole(['admin', 'demo_admin']))
                            {{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-end') }}
                        @endif
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-master-layout>
