<div class="large-thumb-block">
    <img src="{{$largeThumbUrl}}" class="large-thumb-img">
</div>
@if($type=='customizable')
    <div class="edit-btn-block">
        @if($side == 1)
            <a href="https://www.expresscopy.com/ec/editor/index/side/front">Edit Design</a>
        @else
            <a href="https://www.expresscopy.com/ec/editor/index/side/back">Edit Design</a>
        @endif
    </div>
@endif
<div class="change-btn-block">
    @php
        $sideText = "";
        if ($side ==1) {
            $sideText = "front";
        }else{
            $sideText = "back";
        }
    @endphp
    <span> <i class="fa fa-trash"></i></span> <a href="#" id="changeDesignBtn" class="change-design-btn" data-side="{{$side}}" >Change {{$sideText}}</a>
</div>