@php
    $data  ??=  [];
    $value ??= '';
    $name  ??= '';
    $label ??= ucfirst($name);
    $class ??= null;
    $multiple ??= '';
    $required ??= false;

    //dd($data);
@endphp
<!--multiple   []-->
<div @class(['form-group', $class ])>
    <label for="{{ $name }}">{{ $label }}</label>

    <select name="{{$name}}@if($multiple)[]@endif" id="{{$name}}" class="form-select @if($multiple) choices @endif @error($name) is-invalid  @enderror" {{ $multiple }} @required($required)>
        @foreach ($data as $k => $v)
            <option value="{{ $k }}" @if($multiple) @selected($value->contains($k)) @else @selected($value == $k) @endif>{{ $v }}</option>
        @endforeach
    </select>


    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
