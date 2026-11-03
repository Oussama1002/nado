@extends('layouts.app')

@section('title', 'Comparison | Nado.ma')

@section('link')

@endsection
@section('content')
        <div class="dlab-bnr-inrs overlay-black-middles" style="height: 100px;width:100%;text-align:center;background-image:url({{ asset('assets/images/banner/achat-voiture.jpg')}});">
            <div class="container">
                <div class="dlab-bnr-inr-entrys">
                    <h1 class="text-secondry">{{ __('messages.compare.title') }}</h1>
                </div>
            </div>
        </div>
        <!-- inner page banner END -->
        <!-- Breadcrumb row -->
        <div class="breadcrumb-row">
            <div class="container">
                <ul class="list-inline">
                <li><a href="{{ route('index') }}">{{ __('messages.common.home') }}</a></li>
                <li class="text-secondry">{{ __('messages.compare.title') }}</li>
                </ul>
            </div>
        </div>
        <!-- Breadcrumb row END -->
		<div class="section-full p-t50 bg-white content-inner-2 compare-car">
            <div class="container">
                <div class="row">
                    <!-- Side bar start -->
					<div class="col-md-12 col-lg-3">
					</div>
                    <div class="col-md-4 col-lg-4 col-md-4 col-sm-4">
                        @csrf
						<div class="input-group m-b30">
							<select class="form-control car1" id="car1" data-nb="1">
								<option>{{ __('messages.compare.select_car') }}</option>
								@foreach($ads as $ad)
								<option value="{{ $ad->id }}">{{ $ad->mark }} {{ $ad->modele }} {{ $ad->year }}</option>
								@endforeach
							</select>
						</div>
						<div class="dlab-feed-list m-b30 text-left">
							<div class="dlab-media blockimgcar1" > 
								<a href="car-details-overview.html"><img src="" alt=""></a> 
							</div>
							<div class="dlab-info text-left">
								<h4 class="dlab-title titlecar1"><a href="car-details-overview.html"> </a></h4>
								<p class="dlab-price "><span class="text-black prixcar1"></span></p>
							</div>
						</div>
					</div>
					<div class="col-md-4 col-lg-4 col-md-4 col-sm-4">
						<div class="input-group m-b30">
							<select class="form-control car2" id="car2" data-nb="2">
								<option>{{ __('messages.compare.select_car') }}</option>
								@foreach($ads as $ad)
								<option value="{{ $ad->id }}">{{ $ad->mark }} {{ $ad->modele }} {{ $ad->year }}</option>
								@endforeach
							</select>
						</div>
						<div class="dlab-feed-list m-b30 text-left">
							<div class="dlab-media blockimgcar2"> 
								<a href="car-details-overview.html"><img src="" alt=""></a> 
							</div>
							<div class="dlab-info text-left">
								<h4 class="dlab-title titlecar2"><a href="car-details-overview.html"> </a></h4>
								<p class="dlab-price "><span class="text-black prixcar2"></span></p>
							</div>
						</div>
					</div>
                </div>
				<div class="row">
					<div class="col-md-12">
						<div class="icon-bx-wraper bx-style-1 m-b30">
							<ul class="table-dl table-col3 clearfix ">
								<li class="table-head">
									<div class="leftview compare-list-1">{{ __('messages.compare.tech_specs') }}</div>
									<div class="rightviewss compare-list-2 titlecar1">  {{ __('messages.compare.car1') }} </div>
									<div class="rightviewss compare-list-3 titlecar2">  {{ __('messages.compare.car2') }} </div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.marque') }}</div>
									<div class="rightviewss compare-list-2 markcar1"> —</div>
									<div class="rightviewss compare-list-3 markcar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.modele') }}</div>
									<div class="rightviewss compare-list-2 modelecar1"> —</div>
									<div class="rightviewss compare-list-3 modelecar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.year') }}</div>
									<div class="rightviewss compare-list-2 yearcar1"> —</div>
									<div class="rightviewss compare-list-3 yearcar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.fuel') }}</div>
									<div class="rightviewss compare-list-2 fuelcar1"> —</div>
									<div class="rightviewss compare-list-3 fuelcar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.gearbox') }}</div>
									<div class="rightviewss compare-list-2 boxcar1"> —</div>
									<div class="rightviewss compare-list-3 boxcar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.mileage') }}</div>
									<div class="rightviewss compare-list-2 kmcar1"> —</div>
									<div class="rightviewss compare-list-3 kmcar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.fiscal_power') }}</div>
									<div class="rightviewss compare-list-2 cvcar1"> —</div>
									<div class="rightviewss compare-list-3 cvcar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.doors') }}</div>
									<div class="rightviewss compare-list-2 nbpocar1"> —</div>
									<div class="rightviewss compare-list-3 nbpocar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.body') }}</div>
									<div class="rightviewss compare-list-2 caroscar1"> —</div>
									<div class="rightviewss compare-list-3 caroscar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.origin') }}</div>
									<div class="rightviewss compare-list-2 originecar1"> —</div>
									<div class="rightviewss compare-list-3 originecar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.state') }}</div>
									<div class="rightviewss compare-list-2 etatcar1"> —</div>
									<div class="rightviewss compare-list-3 etatcar2"> —</div>
								</li>
							</ul>
						</div>	
						
						<div class="icon-bx-wraper bx-style-1 m-b30">	
							<ul class="table-dl table-col3 clearfix">
								<li class="table-head">
									<div class="leftview compare-list-1">{{ __('messages.compare.equipment') }}</div>
									<div class="rightviewss compare-list-2 titlecar1"> {{ __('messages.compare.car1') }} </div>
									<div class="rightviewss compare-list-3 titlecar2"> {{ __('messages.compare.car2') }} </div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.alloy_wheels') }}</div>
									<div class="rightviewss compare-list-2 jantescar1"> —</div>
									<div class="rightviewss compare-list-3 jantescar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.airbags') }}</div>
									<div class="rightviewss compare-list-2 airbagscar1"> —</div>
									<div class="rightviewss compare-list-3 airbagscar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.ac') }}</div>
									<div class="rightviewss compare-list-2 climatisationcar1"> —</div>
									<div class="rightviewss compare-list-3 climatisationcar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.navigation') }}</div>
									<div class="rightviewss compare-list-2 gpscar1"> —</div>
									<div class="rightviewss compare-list-3 gpscar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.sunroof') }}</div>
									<div class="rightviewss compare-list-2 toit_ouvrantcar1"> —</div>
									<div class="rightviewss compare-list-3 toit_ouvrantcar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.double_sunroof') }}</div>
									<div class="rightviewss compare-list-2 double_toit_ouvrantcar1"> —</div>
									<div class="rightviewss compare-list-3 double_toit_ouvrantcar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.leather_seats') }}</div>
									<div class="rightviewss compare-list-2 siege_cuircar1"> —</div>
									<div class="rightviewss compare-list-3 siege_cuircar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.rear_radar') }}</div>
									<div class="rightviewss compare-list-2 radar_reculcar1"> —</div>
									<div class="rightviewss compare-list-3 radar_reculcar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.rear_camera') }}</div>
									<div class="rightviewss compare-list-2 camera_reculcar1"> —</div>
									<div class="rightviewss compare-list-3 camera_reculcar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.electric_windows') }}</div>
									<div class="rightviewss compare-list-2 vitreseleccar1"> —</div>
									<div class="rightviewss compare-list-3 vitreseleccar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.abs') }}</div>
									<div class="rightviewss compare-list-2 abscar1"> —</div>
									<div class="rightviewss compare-list-3 abscar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.eps') }}</div>
									<div class="rightviewss compare-list-2 epscar1"> —</div>
									<div class="rightviewss compare-list-3 epscar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.cruise') }}</div>
									<div class="rightviewss compare-list-2 rvcar1"> —</div>
									<div class="rightviewss compare-list-3 rvcar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.speed_limiter') }}</div>
									<div class="rightviewss compare-list-2 lvcar1"> —</div>
									<div class="rightviewss compare-list-3 lvcar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.cd') }}</div>
									<div class="rightviewss compare-list-2 cdcar1"> —</div>
									<div class="rightviewss compare-list-3 cdcar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.onboard_computer') }}</div>
									<div class="rightviewss compare-list-2 ordinateurcar1"> —</div>
									<div class="rightviewss compare-list-3 ordinateurcar2"> —</div>
								</li>
								<li>
									<div class="leftview compare-list-1">{{ __('messages.compare.central_locking') }}</div>
									<div class="rightviewss compare-list-2 verrouillagecar1"> —</div>
									<div class="rightviewss compare-list-3 verrouillagecar2"> —</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
            </div>
        </div>
		<div class="section-full bg-white  p-t40 p-b70 ">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="section-head">
							<h3 class="h4 text-uppercase">{{ __('messages.common.latest_ads') }}</h3>

						</div>
						<div class="clearfix owl-carousel owl-btn-style-2 quick-look">
						    @foreach($ads as $ad)
							<div class="item">

                                <div class="dlab-feed-list style-list m-b30 text-left box-s border-blr">
                                        <div class="dlab-media border-tlr">
                                            <a href="{{ route('achat.detail', [ 'marque' => str_replace(' ', '', $ad->mark),  'modele' =>  str_replace(' ', '', $ad->modele),  'id' => $ad['id'] ]) }}">
                                                <div class="block-status-listAds {{$ad['status']}}"><i class="far fa-check-square"></i> {{ucfirst($ad['status'])}}</div>
                                                <img src="{{ asset('storage/ads/'.$ad->pathImageAds()) }}" alt="">
                                                <div class="bg-primary block-price-list">
                                                    <div class="p-a5 text-center text-secondry" style="font-size: 18px;">
                                                        <b>{{$ad['prix']}} Dh</b>
                                                    </div>
                                                    <div class="bg-secondry text-center text-primary p-a5" style="font-size: 8px;">
                                                         {{ number_format(($ad['prix']) / 72, 2, '.', '') }} {{ __('messages.compare.months_suffix') }}
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="dlab-info block-detail-list text-left border-blr" style="text-align: left !important;">
                                            <h4 class="dlab-title m-b0" ><a href="{{ route('achat.detail', [ 'marque' => str_replace(' ', '', $ad->mark),  'modele' =>  str_replace(' ', '', $ad->modele),  'id' => $ad['id'] ]) }}" style="color:#333 !important;font-weight: normal;">{{$ad['mark']}} {{$ad['modele']}} - {{$ad['year']}}</a></h4>
                                            <span class="text-muted font-14">{{ __('messages.compare.used_cars') }}</span>
                                            <img src="{{ asset('assets/images/icons/icon-car.png')}}" class="icon-image-list" style="width: 60px;display: inline-block;">
                                            <ul class="used-car-dl-info" style="font-size: 10px;">
                								<li><i class="fa fa-map-marker"></i> {{$ad->city}}</li>
                								<li><i class="fa fa-car"></i> {{ __('messages.compare.ref') }} vh00{{$ad->id}}</li>
                								<li class="hide"><i class="fa fa-calendar"></i> {{$formatDate($ad->created_at)}}</li>
                							</ul>
                						    <div class="site-filters btn-list m-t10">
                                				<ul class="filters clearfix" data-toggle="buttons">
                                				    @if($ad['showroom'] == 1)
                                					<li data-filter="" class="btn active">
                                						<a href="#" class="btn btn-sm bg-primary text-white"><span><i class="far fa-check-square"></i> {{ __('messages.compare.in_showroom') }}</span></a> 
                                					</li>
                                					@endif
                                					@if($ad['finance'] == 1)
                                					<li data-filter="interior" class="btn">
                                						<a href="#" class="btn btn-sm btn-finance text-white"><span><i class="far fa-check-square"></i> {{ __('messages.compare.finance') }}</span></a> 
                                					</li>
                                					@endif
                                					@if($ad['certifie'] == 1)
                                					<li data-filter="exterior" class="btn">
                                						<a href="#" class="btn btn-sm btn-success text-white"><span><i class="far fa-check-square"></i> {{ __('messages.compare.certified') }}</span></a> 
                                					</li>
                                					@endif
                                				</ul>
                                			</div>
                                            <div class="icon-box-btn text-center m-tb10">
                                                <ul class="clearfix">
                                                    <li><a class="box-list" href="javascript:;"><i class="flaticon-calendar"></i> {{$ad['year']}}</a></li>
                                                    <li><a class="box-list" href="javascript:"><i class="flaticon-settings"></i> {{$ad['box']}}</a></li>
                                                    <li><a class="box-list" href="javascript:;"><i class="flaticon-oil"></i> {{$ad['carburant']}}</a></li>
                                                    <li><a class="box-list" href="javascript:;"><i class="flaticon-dashboard-1"></i> {{$ad['km']}} km</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
							</div>
						@endforeach	
						</div>
					</div>
				</div>

			</div>
		</div>
@endsection
@section('script')
<script>
$(document).ready(function(){
    
        $("select.car1, select.car2").change(function () {
        var id = $(this).find('option:selected').val();
        var nb = $(this).data('nb');
        var formData = {
            'id': id,
            '_token' : $('input[name="_token"]').val()
        };
        var ajaxurl = "/comparison/"+id+"/getads";
        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: formData,
            dataType: 'json',
            success: function (data) {
                $(".blockimgcar"+nb+" img").attr("src", "https://nado.ma/storage/ads/"+data[0].image);
                $(".titlecar"+nb).text(data[0].mark+" "+ data[0].modele+" "+ data[0].year);
                $(".prixcar"+nb).text(data[0].prix+" Dhs");
                $(".markcar"+nb).text(data[0].mark);
                $(".modelecar"+nb).text(data[0].modele);
                $(".yearcar"+nb).text(data[0].year);
                $(".fuelcar"+nb).text(data[0].carburant);
                $(".boxcar"+nb).text(data[0].box);
                $(".kmcar"+nb).text(data[0].km);
                $(".cvcar"+nb).text(data[0].cv);
                $(".nbpocar"+nb).text(data[0].nb_Door);
                $(".caroscar"+nb).text(data[0].bodywork);
                $(".originecar"+nb).text(data[0].origine);
                $(".etatcar"+nb).text(data[0].etat);
                if(data[0].jantesA == 1){ $(".jantescar"+nb).html('<i class="fa fa-check font-18 text-green"></i>'); }else{ $(".jantescar"+nb).html('<i class="fa fa-close font-18 text-red"></i>');}
                if(data[0].airbags == 1){ $(".airbagscar"+nb).html('<i class="fa fa-check font-18 text-green"></i>'); }else{ $(".airbagscar"+nb).html('<i class="fa fa-close font-18 text-red"></i>');}
                if(data[0].climatisation == 1){ $(".climatisationcar"+nb).html('<i class="fa fa-check font-18 text-green"></i>'); }else{ $(".climatisationcar"+nb).html('<i class="fa fa-close font-18 text-red"></i>');}
                if(data[0].gps == 1){ $(".gpscar"+nb).html('<i class="fa fa-check font-18 text-green"></i>'); }else{ $(".gpscar"+nb).html('<i class="fa fa-close font-18 text-red"></i>');}
                if(data[0].toit_ouvrant == 1){ $(".toit_ouvrantcar"+nb).html('<i class="fa fa-check font-18 text-green"></i>'); }else{ $(".toit_ouvrantcar"+nb).html('<i class="fa fa-close font-18 text-red"></i>');}
                if(data[0].double_toit_ouvrant == 1){ $(".double_toit_ouvrantcar"+nb).html('<i class="fa fa-check font-18 text-green"></i>'); }else{ $(".double_toit_ouvrantcar"+nb).html('<i class="fa fa-close font-18 text-red"></i>');}
                if(data[0].siege_cuir == 1){ $(".siege_cuircar"+nb).html('<i class="fa fa-check font-18 text-green"></i>'); }else{ $(".siege_cuircar"+nb).html('<i class="fa fa-close font-18 text-red"></i>');}
                if(data[0].radar_recul == 1){ $(".radar_reculcar"+nb).html('<i class="fa fa-check font-18 text-green"></i>'); }else{ $(".radar_reculcar"+nb).html('<i class="fa fa-close font-18 text-red"></i>');}
                if(data[0].camera_recul == 1){ $(".camera_reculcar"+nb).html('<i class="fa fa-check font-18 text-green"></i>'); }else{ $(".camera_reculcar"+nb).html('<i class="fa fa-close font-18 text-red"></i>');}
                if(data[0].vitreselec == 1){ $(".vitreseleccar"+nb).html('<i class="fa fa-check font-18 text-green"></i>'); }else{ $(".vitreseleccar"+nb).html('<i class="fa fa-close font-18 text-red"></i>');}
                if(data[0].abs == 1){ $(".abscar"+nb).html('<i class="fa fa-check font-18 text-green"></i>'); }else{ $(".abscar"+nb).html('<i class="fa fa-close font-18 text-red"></i>');}
                if(data[0].eps == 1){ $(".epscar"+nb).html('<i class="fa fa-check font-18 text-green"></i>'); }else{ $(".epscar"+nb).html('<i class="fa fa-close font-18 text-red"></i>');}
                if(data[0].rv == 1){ $(".rvcar"+nb).html('<i class="fa fa-check font-18 text-green"></i>'); }else{ $(".rvcar"+nb).html('<i class="fa fa-close font-18 text-red"></i>');}
                if(data[0].lv == 1){ $(".lvcar"+nb).html('<i class="fa fa-check font-18 text-green"></i>'); }else{ $(".lvcar"+nb).html('<i class="fa fa-close font-18 text-red"></i>');}
                if(data[0].cd == 1){ $(".cdcar"+nb).html('<i class="fa fa-check font-18 text-green"></i>'); }else{ $(".cdcar"+nb).html('<i class="fa fa-close font-18 text-red"></i>');}
                if(data[0].ordinateur == 1){ $(".ordinateurcar"+nb).html('<i class="fa fa-check font-18 text-green"></i>'); }else{ $(".ordinateurcar"+nb).html('<i class="fa fa-close font-18 text-red"></i>');}
                if(data[0].verrouillage == 1){ $(".verrouillagecar"+nb).html('<i class="fa fa-check font-18 text-green"></i>'); }else{ $(".verrouillagecar"+nb).html('<i class="fa fa-close font-18 text-red"></i>');}
            }
        });
    });


    });
</script>
@endsection

