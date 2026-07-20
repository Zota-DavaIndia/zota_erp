<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\ManufacturerController::class, 'store']), 'method' => 'post', 'id' => $quick_add ? 'quick_add_manufacturer_form' : 'manufacturer_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('lang_v1.add_manufacturer')</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('name', __('lang_v1.manufacturer_name') . ':*') !!}
          {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.manufacturer_name') ]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('description', __('brand.short_description') . ':') !!}
          {!! Form::text('description', null, ['class' => 'form-control','placeholder' => __('brand.short_description')]); !!}
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang('messages.save')</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang('messages.close')</button>
    </div>

    {!! Form::close() !!}

  </div>
</div>
