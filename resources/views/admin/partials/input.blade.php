@php
    $value ??= '';
    $name  ??= '';
    $type  ??= 'text';
    $label ??= ucfirst($name);
    $class ??= null;
    $id ??= $name;
    $hasicon ??= '';
    $required ??= false;
    $classicon = ($hasicon != '') ? 'has-icon-left' : '';
@endphp

<div @class(['form-group', $class, $classicon ])>
    <label for="{{ $name }}">{{ $label }}</label>

    <div class="position-relative">
    @if ($type == "textarea")
        <textarea name="{{$name}}" id="{{$id}}" class="form-control @error($name) is-invalid  @enderror" rows="3" @required($required)>{{ old($name , $value)}}</textarea>
    @else
        <input type="{{$type}}" name="{{$name}}" id="{{$name}}" class="form-control round @error($name) is-invalid  @enderror"  value="{{ old($name , $value)}}" @required($required)>
        @if($hasicon)
            <div class="form-control-icon">
                <i class="bi bi-{{$hasicon}}"></i>
            </div>
        @endif

   @endif
    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
    </div>
</div>
