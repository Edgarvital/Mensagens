@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row my-4">
            <div class="col-sm" align="center">
                <img src="{{asset('imagens/logo_secomdigital_semfundo.png')}}">
            </div>
            <div class="col-sm" align="justify">
                <h2 class="titulo">Bem Vindo!</h2>
                <p class="paragrafo">A <b class="secom">secom digital</b> é um sistema
                    lorem Ipsum é simplesmente uma
                    simulação de texto da indústria
                    tipográfica e de impressos, e vem
                    sendo utilizado desde o século
                    XVI, quando um impressor
                    desconhecido pegou uma bandeja.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm" align="center">
                <div class="crop">
                    <img src="{{asset('imagens/letreiro.jpg')}}">
                </div>
            </div>
            <div class="col-sm">
                <h2 class="titulo">Geração de <b class="secom">clipping</b></h2>
                <p class="paragrafo" style="text-align: justify">Sistema para geração automatizada de
                    clipping de notícias a partir do portal
                    da UFAPE.</p>
                <br>
                <div class="balao">
                    <h3 class="mx-3" style="margin-top: 10px; color: white">Selecione um periodo</h3>
                    <hr style="border-color: white; width: 92%; padding: 0px; margin-top: -10px"></hr>
                    <form method="post" id="formGerar" action="{{ route('clipping.gerar') }}">
                        @csrf
                        <div class="row d-flex mx-1">
                            <div class="col-sm">
                                <label><b style="color: white">De:</b></label>
                                <input name="dataInicio" placeholder="dd/mm/aaaa" required
                                       class="form-control @error('dataInicio') is-invalid @enderror"
                                       value="{{date("d/m/Y", strtotime("-1 week"))}}">
                            </div>
                            <div class="col-sm">
                                <label><b style="color: white">Até:</b></label>
                                <input name="dataFinal" placeholder="dd/mm/aaaa" required
                                       class="form-control @error('dataFinal') is-invalid @enderror"
                                       value="{{date("d/m/Y")}}">
                            </div>
                        </div>
                        <br>
                        <center>
                            <button type="submit" class="btn btn-primary" id="gerar" style="width: 90%">Gerar Clipping
                            </button>
                        </center>
                    </form>
                </div>
            </div>
        </div>
        <div class="row py-lg-5">
            <div class="col-sm" align="center">
                <h2 class="titulo">Geração de <b class="secom">cartões de aniversario</b></h2>
                <p class="paragrafo">Lorem Ipsum é simplesmente uma
                    simulação de texto da indústria
                    tipográfica e de impressos, e vem
                    sendo utilizado desde o século XVI,
                    quando um impressor desconhecido
                    pegou uma bandeja de tipos e os
                    embaralhou para fazer um livro de
                    modelos de tipos.</p>
            </div>
            <div class="col-sm" style="left: 15px">
                <div class="crop2">
                    <img src="{{asset('imagens/balao.jpg')}}">
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
