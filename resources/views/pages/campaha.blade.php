@extends('layouts.app')

@section('content')
    <div class="container">
        <h5 class="text-bold">Campanha</h5>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('messenger.campanha.store') }}">
            @csrf
            <div class="form-group p-3">
                <label for="formGroupExampleInput">Valor do conteúdo</label>
                <input type="text" class="form-control" name="valor" id="formGroupExampleInput"
                    placeholder="Definir valor do anuncio" required>
            </div>
            <div class="form-group p-3">
                <label for="formGroupExampleInput2">Mensagem</label>
                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" placeholder="Mensagem de campanha"
                    name="message" required></textarea>
            </div>

            <div class="p-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="defaultCheck1" name="subscribers">
                    <label class="form-check-label" for="defaultCheck1">
                        Assinantes
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="defaultCheck2" name="followers">
                    <label class="form-check-label" for="defaultCheck2">
                        Seguidores
                    </label>
                </div>
            </div>
            <div class="ml-3 mr-3 mb-2">
                <button type="submit"
                    class="btn btn-block btn-round border btn-primary btn-round px-3 p-3 mt-3 border text-sm">
                    Promover
                </button>
            </div>
        </form>
    </div>
@endsection
