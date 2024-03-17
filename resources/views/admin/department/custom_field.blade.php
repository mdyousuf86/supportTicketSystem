<form action="{{ route('admin.department.custom.field.save', $ticketDepartment->id) }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    <div class="">
        <div class="pt-3 px-3 d-flex justify-content-between">
            <h5>@lang('User Data')</h5>
            <button type="button" class="btn btn-sm btn-outline--primary form-generate-btn"> <i
                    class="la la-fw la-plus"></i>@lang('Add New')</button>
        </div>
        <div class="card-body pt-2">
            <div class="row addedField">
                @if ($form)
                    @foreach ($form->form_data as $formData)
                        <div class="col-md-4">
                            <div class="card border mb-3" id="{{ $loop->index }}">
                                <input type="hidden" name="form_generator[is_required][]"
                                    value="{{ $formData->is_required }}">
                                <input type="hidden" name="form_generator[extensions][]"
                                    value="{{ $formData->extensions }}">
                                <input type="hidden" name="form_generator[options][]"
                                    value="{{ implode(',', $formData->options) }}">

                                <div class="card-body">
                                    <div class="form-group">
                                        <label>@lang('Label')</label>
                                        <input type="text" name="form_generator[form_label][]" class="form-control"
                                            value="{{ $formData->name }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>@lang('Type')</label>
                                        <input type="text" name="form_generator[form_type][]" class="form-control"
                                            value="{{ $formData->type }}" readonly>
                                    </div>
                                    @php
                                        $jsonData = json_encode([
                                            'type' => $formData->type,
                                            'is_required' => $formData->is_required,
                                            'label' => $formData->name,
                                            'extensions' => explode(',', $formData->extensions) ?? 'null',
                                            'options' => $formData->options,
                                            'old_id' => '',
                                        ]);
                                    @endphp
                                    <div class="btn-group w-100">
                                        <button type="button" class="btn btn--primary editFormData"
                                            data-form_item="{{ $jsonData }}" data-update_id="{{ $loop->index }}"><i
                                                class="las la-pen"></i></button>
                                        <button type="button" class="btn btn--danger removeFormData"><i
                                                class="las la-times"></i></button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    @can('admin.department.custom.field.save')
        <div class="card-footer">
            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
        </div>
    @endcan
    
</form>

<x-form-generator />
@push('script')
    <script>
        "use strict"
        var formGenerator = new FormGenerator();
    </script>

    <script src="{{ asset('assets/global/js/form_actions.js') }}"></script>
@endpush
