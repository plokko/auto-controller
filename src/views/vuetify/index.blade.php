@extends('layouts.vue')
@section('page.title','test')
@section('app')
    @verbatim
        <app-layout>

            <h2>Test headers</h2>
            <table-list
                :headers="[{key:'email',title:'E-mail'},{key:'name'},'id']"
            ></table-list>


        </app-layout>
    @endverbatim
@endsection
@section('page-script')
    <script>
        new Vue({
            vuetify: vuetify,
            data: function () {
                return {
                    filter3: {
                        filter1: '',
                        filter2: ''
                    },
                    loading3: true,
                }
            }
        }).$mount('#app')
    </script>
@endsection
