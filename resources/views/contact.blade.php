@extends('layouts.app')

@section('title', 'Contact | Nado.ma')

@section('content')
  <!-- inner page banner -->
   <div class="dlab-bnr-inr overlay-black-middle" style="background-image:url({{ asset('assets/images/background/bg4.jpg')}} );">
    <div class="container">
        <div class="dlab-bnr-inr-entry">
            <h1 class="text-white">Contact</h1>
        </div>
    </div>
</div>
<!-- inner page banner END -->
<!-- Breadcrumb row -->
<div class="breadcrumb-row">
    <div class="container">
        <ul class="list-inline">
            <li><a href="/">Home</a></li>
            <li>Contact</li>
        </ul>
    </div>
</div>
<!-- Breadcrumb row END -->
<!-- contact area -->
<!-- contact area -->
<div class="section-full content-inner bg-white contact-style-1">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-sm-6 m-b30">
                <div class="icon-bx-wraper bx-style-1 p-a30 center">
                    <div class="icon-xl text-primary m-b20"> <a href="#" class="icon-cell"><i class="fas fa-map-marker-alt"></i></a> </div>
                    <div class="icon-content">
                        <h5 class="dlab-tilte text-uppercase">Addresse</h5>
                        <p>Rue 93, N° 11 Jnane Californie Casablanca</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 m-b30">
                <div class="icon-bx-wraper bx-style-1 p-a30 center">
                    <div class="icon-xl text-primary m-b20"> <a href="#" class="icon-cell"><i class="fa fa-envelope"></i></a> </div>
                    <div class="icon-content">
                        <h5 class="dlab-tilte text-uppercase">Email</h5>
                        <p>contact@nado.ma</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 m-b30">
                <div class="icon-bx-wraper bx-style-1 p-a30 center">
                    <div class="icon-xl text-primary m-b20"> <a href="#" class="icon-cell"><i class="fa fa-phone"></i></a> </div>
                    <div class="icon-content">
                        <h5 class="dlab-tilte text-uppercase">Téléphone portable</h5>
                        <p>+212 770 080 009 </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 m-b30">
                <div class="icon-bx-wraper bx-style-1 p-a30 center">
                    <div class="icon-xl text-primary m-b20"> <a href="#" class="icon-cell"><i class="fa fa-fax"></i></a> </div>
                    <div class="icon-content">
                        <h5 class="dlab-tilte text-uppercase">Téléphone fix</h5>
                        <p>+212 808 631 836</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Left part start -->
            <div class="col-md-6">
                <div class="p-a30 bg-gray clearfix m-b30 ">
                    <h2>Ecrivez-nous</h2>
                    @if (session('success'))
                        <div class="FormMsg">
                            <div class="alert alert-success no-bg"> {{ session('success') }} </div>
                        </div>
                    @endif
                    <form method="post" class="Form" action="{{ route('contactez-nous.store') }}">
                    @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input name="name" type="text" required class="form-control" placeholder="Nom">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input name="email" type="email" class="form-control" required  placeholder="Email" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input name="phone" type="text" required class="form-control" placeholder="Téléphone">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input name="subject" type="text" required class="form-control" placeholder="Sujet" value="{{ old('subject', request('subject')) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <textarea name="message" rows="4" class="form-control" required placeholder="Message...">{{ old('message', request('message')) }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button name="submit" type="submit" value="Submit" class="site-button ">
                                    <span>
                                        Envoyer le message
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Left part END -->
            <!-- right part start -->
            <div class="col-md-6">
                <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d831.497999123923!2d-7.607901806936464!3d33.527593717096664!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xda62d2b43f5fa89%3A0x4a08c15bb60afbf5!2sNADO.ma%20-%20Auto%20Ench%C3%A8re!5e0!3m2!1sfr!2sma!4v1704650848180!5m2!1sfr!2sma" style="border:0; width:100%; height:450px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <!-- right part END -->
        </div>
    </div>
</div>
<!-- contact area  END -->
<!-- contact area  END -->
@endsection


